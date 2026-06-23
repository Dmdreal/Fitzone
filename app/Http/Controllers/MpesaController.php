<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipPackage;
use App\Models\Payment;
use App\Models\TrainerProfile;
use App\Models\User;
use App\Models\DietPlan;
use App\Models\WorkoutPlan;
use App\Services\PaymentDistributionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MpesaController extends Controller
{
    public function pay(): View
    {
        return view('pay');
    }

    public function stkPush(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->filled('phone')) {
            $request->merge([
                'phone' => $this->normalizePhone($request->input('phone')),
            ]);
        }

        $data = $request->validate([
            'phone' => ['required', 'regex:/^254(7|1)\d{8}$/'],
            'package_id' => ['nullable', 'exists:membership_packages,id'],
            'trainer_id' => ['nullable', 'exists:trainer_profiles,id'],
            'gym_id' => ['nullable', 'exists:users,id'],
            'amount' => ['nullable', 'numeric', 'min:1'],
        ]);

        if (blank(config('services.mpesa.consumer_key')) || blank(config('services.mpesa.consumer_secret')) || blank(config('services.mpesa.passkey'))) {
            return $this->failedResponse($request, 'M-PESA credentials are missing');
        }

        $callbackUrl = $this->callbackUrl();

        if (! $this->isPublicHttpsUrl($callbackUrl)) {
            return $this->failedResponse($request, 'Set MPESA_CALLBACK_URL in your .env file to a public HTTPS callback URL.');
        }

        $package = isset($data['package_id'])
            ? MembershipPackage::findOrFail($data['package_id'])
            : null;
        $trainerProfile = isset($data['trainer_id'])
            ? TrainerProfile::find($data['trainer_id'])
            : null;

        $gymOwner = isset($data['gym_id'])
            ? User::where('role', 'gym_owner')->find($data['gym_id'])
            : null;

        $amount = $data['amount'] ?? null;

        if ($package) {
            $amount = $trainerProfile?->preferred_rate ?? $gymOwner?->preferred_rate ?? $package->price;
        } elseif ($gymOwner) {
            $amount = $gymOwner->preferred_rate ?? $amount;
        }

        if (! $amount) {
            return $this->failedResponse($request, 'Enter an amount or select a package.');
        }

        try {
            $payment = null;
            $response = null;
            $body = [];
            $membership = null;

            DB::transaction(function () use (&$payment, &$response, &$body, &$membership, $data, $package, $trainerProfile, $gymOwner, $amount, $callbackUrl) {
                if ($package && Auth::check()) {
                    $startsAt = now();
                    $endsAt = match ($package->duration_unit) {
                        'day' => $startsAt->copy()->addDays($package->duration_count),
                        'week' => $startsAt->copy()->addWeeks($package->duration_count),
                        'month' => $startsAt->copy()->addMonths($package->duration_count),
                        'year' => $startsAt->copy()->addYears($package->duration_count),
                    };

                    $membership = Membership::create([
                        'member_id' => Auth::id(),
                        'membership_package_id' => $package->id,
                        'trainer_id' => $trainerProfile?->user_id,
                        'gym_owner_id' => $gymOwner?->id,
                        'starts_at' => $startsAt->toDateString(),
                        'ends_at' => $endsAt->toDateString(),
                        'status' => 'pending',
                    ]);
                }

                $payment = Payment::create([
                    'member_id' => Auth::id(),
                    'membership_id' => $membership?->id,
                    'trainer_id' => $trainerProfile?->user_id,
                    'gym_owner_id' => $gymOwner?->id,
                    'phone' => $data['phone'],
                    'amount' => $amount,
                    'method' => 'mpesa',
                    'status' => 'pending',
                    'reference' => 'FITZONE-'.now()->format('YmdHis').'-'.(Auth::id() ?? 'GUEST'),
                    'notes' => $package ? 'M-PESA checkout for '.$package->name : 'Standalone M-PESA payment',
                ]);

                $timestamp = now()->format('YmdHis');
                $response = Http::withToken($this->getToken())
                    ->acceptJson()
                    ->post($this->mpesaUrl('/mpesa/stkpush/v1/processrequest'), [
                        'BusinessShortCode' => config('services.mpesa.shortcode'),
                        'Password' => base64_encode(config('services.mpesa.shortcode').config('services.mpesa.passkey').$timestamp),
                        'Timestamp' => $timestamp,
                        'TransactionType' => 'CustomerPayBillOnline',
                        'Amount' => (int) ceil((float) $amount),
                        'PartyA' => $data['phone'],
                        'PartyB' => config('services.mpesa.shortcode'),
                        'PhoneNumber' => $data['phone'],
                        'CallBackURL' => $callbackUrl,
                        'AccountReference' => $payment->reference,
                        'TransactionDesc' => 'Fitzone payment',
                    ]);

                $body = $response->json();

                Log::info('M-PESA STK push response', [
                    'payment_id' => $payment->id,
                    'status' => $response->status(),
                    'body' => $body,
                ]);

                if (! $response->successful() || ($body['ResponseCode'] ?? null) !== '0') {
                    $notes = $body['errorMessage'] ?? $body['ResponseDescription'] ?? 'M-PESA STK push failed.';

                    $payment->update([
                        'status' => 'failed',
                        'mpesa_response' => $body,
                        'notes' => $notes,
                    ]);

                    throw new \RuntimeException($notes);
                }

                $payment->update([
                    'mpesa_checkout_request_id' => $body['CheckoutRequestID'] ?? null,
                    'mpesa_merchant_request_id' => $body['MerchantRequestID'] ?? null,
                    'transaction_code' => $body['CheckoutRequestID'] ?? $body['MerchantRequestID'] ?? null,
                ]);
            });
        } catch (\Throwable $exception) {
            $message = $exception instanceof \RuntimeException
                ? $exception->getMessage()
                : 'M-PESA payment failed. '.$exception->getMessage();

            return $this->failedResponse($request, $message);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Check your phone for the M-PESA prompt.',
                'mpesa' => $body,
            ]);
        }

        return redirect()
            ->route('client.activation')
            ->with('success', 'Check your phone for the M-PESA prompt. Your package activates after you enter your PIN.');
    }

    public function callback(Request $request): JsonResponse
    {
        $stk = $request->input('Body.stkCallback', []);
        $checkoutRequestId = $stk['CheckoutRequestID'] ?? null;
        $resultCode = (int) ($stk['ResultCode'] ?? 1);

        Log::info('M-PESA callback received', [
            'checkout_request_id' => $checkoutRequestId,
            'result_code' => $resultCode,
            'result_desc' => $stk['ResultDesc'] ?? null,
        ]);

        $payment = $checkoutRequestId
            ? Payment::where('mpesa_checkout_request_id', $checkoutRequestId)->first()
            : null;

        if (! $payment) {
            Log::warning('M-PESA callback payment not found', [
                'checkout_request_id' => $checkoutRequestId,
            ]);

            return response()->json(['ok' => true]);
        }

        if ($resultCode !== 0) {
            $payment->update([
                'status' => 'failed',
                'mpesa_response' => $request->all(),
                'notes' => $stk['ResultDesc'] ?? 'M-PESA payment failed or was cancelled.',
            ]);

            return response()->json(['ok' => true]);
        }

        $metadata = collect($stk['CallbackMetadata']['Item'] ?? [])
            ->mapWithKeys(fn (array $item) => [$item['Name'] => $item['Value'] ?? null]);

        DB::transaction(function () use ($payment, $metadata, $request) {
            $payment->update([
                'status' => 'paid',
                'amount' => $metadata->get('Amount', $payment->amount),
                'receipt' => $metadata->get('MpesaReceiptNumber'),
                'phone' => $metadata->get('PhoneNumber', $payment->phone),
                'paid_at' => now(),
                'mpesa_response' => $request->all(),
                'notes' => 'M-PESA payment confirmed.',
            ]);

            $payment->membership?->update([
                'status' => 'active',
                'activated_at' => now(),
            ]);

            if ($payment->membership) {
                $this->createActivationBenefits($payment->membership->fresh(['package']));
                (new PaymentDistributionService())->distribute($payment);
            }
        });

        return response()->json(['ok' => true]);
    }

    private function getToken(): string
    {
        $credentials = base64_encode(config('services.mpesa.consumer_key').':'.config('services.mpesa.consumer_secret'));

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$credentials,
        ])->acceptJson()
            ->get($this->mpesaUrl('/oauth/v1/generate'), [
                'grant_type' => 'client_credentials',
            ]);

        $response->throw();

        return $response->json('access_token');
    }

    private function mpesaUrl(string $path): string
    {
        $baseUrl = config('services.mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';

        return $baseUrl.$path;
    }

    private function callbackUrl(): string
    {
        return config('services.mpesa.callback_url') ?: route('mpesa.callback');
    }

    private function isPublicHttpsUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = $parts['host'] ?? null;

        return ($parts['scheme'] ?? null) === 'https'
            && filled($host)
            && ! in_array($host, ['127.0.0.1', 'localhost'], true);
    }

    private function failedResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => $message], 422);
        }

        return back()->withErrors(['mpesa' => $message])->withInput();
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '254'.substr($phone, 1);
        }

        if (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            return '254'.$phone;
        }

        return $phone;
    }

    private function createActivationBenefits(Membership $membership): void
    {
        $package = $membership->package;

        DietPlan::firstOrCreate(
            ['member_id' => $membership->member_id, 'name' => $package->name.' Starter Diet Plan'],
            [
                'membership_package_id' => $package->id,
                'goal' => 'maintenance',
                'daily_calories' => 2200,
                'meal_schedule' => [
                    'breakfast' => 'Oats, fruit, eggs',
                    'lunch' => 'Lean protein, rice, vegetables',
                    'snack' => 'Yogurt and nuts',
                    'dinner' => 'Fish or beans, greens, sweet potato',
                ],
                'meal_delivery_available' => $package->access_level !== 'basic',
                'is_active' => true,
            ]
        );

        $plan = WorkoutPlan::firstOrCreate(
            [
                'member_id' => $membership->member_id,
                'title' => $package->name.' Starter Workout',
            ],
            [
                'trainer_id' => $membership->trainer_id,
                'focus_area' => 'Membership activation',
                'notes' => 'Created automatically after M-PESA confirmation.',
                'starts_at' => $membership->starts_at,
                'ends_at' => $membership->ends_at,
                'is_active' => true,
            ]
        );

        if ($plan->exercises()->doesntExist()) {
            foreach ([
                ['exercise_name' => 'Bike Warmup', 'sets' => 1, 'reps' => 10, 'instructions' => 'Ten minutes at an easy pace.'],
                ['exercise_name' => 'Bodyweight Squat', 'sets' => 3, 'reps' => 12, 'instructions' => 'Move slowly and keep your chest up.'],
                ['exercise_name' => 'Dumbbell Row', 'sets' => 3, 'reps' => 10, 'instructions' => 'Pull your elbow toward your hip.'],
            ] as $index => $exercise) {
                $plan->exercises()->create($exercise + ['sort_order' => $index + 1]);
            }
        }
    }
}
