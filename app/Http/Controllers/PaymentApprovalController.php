<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\Membership;
use App\Models\MembershipPackage;
use App\Models\Payment;
use App\Models\TrainerProfile;
use App\Models\WorkoutPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentApprovalController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'package_id' => ['required', 'exists:membership_packages,id'],
            'trainer_id' => ['nullable', 'exists:trainer_profiles,id'],
            'method' => ['required', 'in:paypal,card,bank,cash'],
            'paypal_email' => ['nullable', 'email', 'max:255'],
            'paypal_transaction_id' => ['nullable', 'string', 'max:100'],
            'card_name' => ['nullable', 'string', 'max:120'],
            'card_number' => ['nullable', 'string', 'max:30'],
            'card_expiry' => ['nullable', 'string', 'max:10'],
            'card_cvv' => ['nullable', 'string', 'max:4'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'bank_reference' => ['nullable', 'string', 'max:120'],
            'depositor_name' => ['nullable', 'string', 'max:120'],
            'cash_note' => ['nullable', 'string', 'max:500'],
        ]);

        $methodRules = [
            'paypal' => ['paypal_email', 'paypal_transaction_id'],
            'card' => ['card_name', 'card_number', 'card_expiry', 'card_cvv'],
            'bank' => ['bank_name', 'bank_reference', 'depositor_name'],
            'cash' => [],
        ];

        foreach ($methodRules[$data['method']] as $field) {
            if (blank($data[$field] ?? null)) {
                return back()->withErrors([$field => 'This field is required for '.strtoupper($data['method']).' verification.'])->withInput();
            }
        }

        $package = MembershipPackage::findOrFail($data['package_id']);
        $trainerProfile = isset($data['trainer_id']) ? TrainerProfile::find($data['trainer_id']) : null;
        $startsAt = now();
        $endsAt = match ($package->duration_unit) {
            'day' => $startsAt->copy()->addDays($package->duration_count),
            'week' => $startsAt->copy()->addWeeks($package->duration_count),
            'month' => $startsAt->copy()->addMonths($package->duration_count),
            'year' => $startsAt->copy()->addYears($package->duration_count),
        };

        DB::transaction(function () use ($data, $package, $trainerProfile, $startsAt, $endsAt) {
            $membership = Membership::create([
                'member_id' => Auth::id(),
                'membership_package_id' => $package->id,
                'trainer_id' => $trainerProfile?->user_id,
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => $endsAt->toDateString(),
                'status' => 'pending',
            ]);

            Payment::create([
                'member_id' => Auth::id(),
                'membership_id' => $membership->id,
                'phone' => Auth::user()->phone,
                'amount' => $package->price,
                'method' => $data['method'],
                'status' => 'pending',
                'reference' => 'FITZONE-PENDING-'.now()->format('YmdHis').'-'.Auth::id(),
                'notes' => $this->paymentNotes($data),
            ]);
        });

        return redirect()
            ->route('client.activation')
            ->with('warning', 'Payment details submitted. Access stays locked until the payment is confirmed and approved.');
    }

    public function adminIndex(): View
    {
        return view('admin.payments', [
            'payments' => Payment::with(['member', 'membership.package', 'membership.trainer'])
                ->latest()
                ->get(),
        ]);
    }

    public function trainerIndex(): View
    {
        return view('trainer.payments', [
            'payments' => Payment::with(['member', 'membership.package'])
                ->whereHas('membership', fn ($query) => $query->where('trainer_id', Auth::id()))
                ->latest()
                ->get(),
        ]);
    }

    public function approve(Payment $payment): RedirectResponse
    {
        $this->authorizePaymentApproval($payment);

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'receipt' => $payment->receipt ?: 'APPROVED-'.$payment->id,
                'notes' => trim(($payment->notes ?? '')."\nApproved by ".Auth::user()->role.' '.Auth::user()->name),
            ]);

            $payment->membership?->update([
                'status' => 'active',
                'activated_at' => now(),
            ]);

            if ($payment->membership) {
                $this->createActivationBenefits($payment->membership->fresh(['package']));
            }
        });

        return back()->with('status', 'Payment approved and membership activated.');
    }

    public function reject(Payment $payment): RedirectResponse
    {
        $this->authorizePaymentApproval($payment);

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'failed',
                'notes' => trim(($payment->notes ?? '')."\nRejected by ".Auth::user()->role.' '.Auth::user()->name),
            ]);

            $payment->membership?->update(['status' => 'cancelled']);
        });

        return back()->with('status', 'Payment rejected. Membership remains locked.');
    }

    private function authorizePaymentApproval(Payment $payment): void
    {
        if (Auth::user()->role === 'admin') {
            return;
        }

        abort_unless(
            Auth::user()->role === 'trainer'
            && $payment->membership
            && $payment->membership->trainer_id === Auth::id(),
            403
        );
    }

    private function paymentNotes(array $data): string
    {
        return match ($data['method']) {
            'paypal' => 'PayPal email: '.$data['paypal_email']."\nPayPal transaction: ".$data['paypal_transaction_id'],
            'card' => 'Card holder: '.$data['card_name']."\nCard last four: ".substr(preg_replace('/\D+/', '', $data['card_number']), -4)."\nExpiry: ".$data['card_expiry'],
            'bank' => 'Bank: '.$data['bank_name']."\nReference: ".$data['bank_reference']."\nDepositor: ".$data['depositor_name'],
            'cash' => 'Cash approval requested'.(blank($data['cash_note'] ?? null) ? '' : "\nNote: ".$data['cash_note']),
        };
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
            ['member_id' => $membership->member_id, 'title' => $package->name.' Starter Workout'],
            [
                'trainer_id' => $membership->trainer_id,
                'focus_area' => 'Membership activation',
                'notes' => 'Created automatically after payment confirmation.',
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
