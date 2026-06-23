<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\CallRequest;
use App\Models\Booking;
use App\Models\ClientChat;
use App\Models\ClientChatMessage;
use App\Models\Complaint;
use App\Models\Membership;
use App\Models\MembershipPackage;
use App\Models\Payment;
use App\Models\Review;
use App\Models\TrainerProfile;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function dashboard(): View|RedirectResponse
    {
        Auth::user()->ensureMemberIdentity();
        $membership = $this->paidActiveMembership();
        $latestMembership = $this->latestRelevantMembership();

        if ($membership && ! session()->has($this->todayPlanSessionKey($membership))) {
            session()->put($this->todayPlanSessionKey($membership), true);

            return redirect()->route('client.today');
        }

        return view('client.dashboard', [
            'membership' => $membership,
            'latestMembership' => $latestMembership,
            'wallet' => Auth::user()->wallet()->firstOrCreate(['member_id' => Auth::id()], ['balance' => 0]),
            'recentOrders' => Auth::user()->cafeOrders()->latest()->take(3)->get(),
            'workoutPlan' => $membership
                ? Auth::user()->workoutPlans()->with('exercises')->latest()->first()
                : null,
            'dietPlan' => $membership ? Auth::user()->dietPlans()->latest()->first() : null,
            'recentPayments' => Auth::user()->payments()->latest()->take(3)->get(),
            'attendanceCount' => $membership ? Auth::user()->attendances()->where('status', 'present')->count() : 0,
            'latestProgress' => Auth::user()->progressRecords()->latest('recorded_at')->first(),
        ]);
    }

    public function onboarding(): View
    {
        Auth::user()->ensureMemberIdentity();

        $userLocation = Auth::user()->location;
        $nearbyTrainers = collect();
        $nearbyGyms = collect();

        $sessionTrainerIds = collect(session('nearby_trainers', []))->pluck('id')->filter()->values();
        if ($sessionTrainerIds->isNotEmpty()) {
            $trainerMap = TrainerProfile::with('user')
                ->whereIn('id', $sessionTrainerIds->all())
                ->get()
                ->keyBy('id');

            $nearbyTrainers = $sessionTrainerIds->map(fn ($id) => $trainerMap->get($id))->filter();
        } elseif ($userLocation) {
            $nearbyTrainers = TrainerProfile::with('user')
                ->whereHas('user', function ($query) use ($userLocation) {
                    $query->where('location', 'like', "%{$userLocation}%")
                        ->orWhere('nearby_locations', 'like', "%{$userLocation}%");
                })
                ->limit(6)
                ->get();

            $nearbyGyms = User::query()
                ->where('role', 'gym_owner')
                ->where(function ($query) use ($userLocation) {
                    $query->where('location', 'like', "%{$userLocation}%")
                        ->orWhere('nearby_locations', 'like', "%{$userLocation}%");
                })
                ->limit(6)
                ->get();
        }

        return view('client.onboarding', [
            'nearbyTrainers' => $nearbyTrainers,
            'nearbyGyms' => $nearbyGyms,
        ]);
    }

    public function packages(Request $request): View
    {
        $trainer = $request->filled('trainer')
            ? TrainerProfile::with(['user', 'preferredPackage'])->find($request->integer('trainer'))
            : null;

        $gym = $request->filled('gym')
            ? User::with('preferredPackage')->where('role', 'gym_owner')->find($request->integer('gym'))
            : null;

        return view('client.packages', [
            'packages' => MembershipPackage::visible()->get(),
            'trainer' => $trainer,
            'gym' => $gym,
            'recommendedPackage' => $trainer?->preferredPackage ?? $gym?->preferredPackage,
        ]);
    }

    public function trainers(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $package = MembershipPackage::visible()->whereKey($request->integer('package'))->first();

        $query = TrainerProfile::with(['user', 'preferredPackage'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('specialty', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('headline', 'like', "%{$search}%")
                                ->orWhere('location', 'like', "%{$search}%")
                                ->orWhere('nearby_locations', 'like', "%{$search}%");
                        });
                });
            });

        $trainers = $query->get();

        // compute client coords from request or user profile location
        $locationService = app(\App\Services\LocationService::class);
        $clientLat = $request->input('lat');
        $clientLng = $request->input('lng');

        if (empty($clientLat) || empty($clientLng)) {
            if (auth()->check() && auth()->user()->location) {
                $storedLocation = auth()->user()->location;
                $parsedLocation = json_decode($storedLocation, true);
                if (is_array($parsedLocation) && ! empty($parsedLocation['latitude']) && ! empty($parsedLocation['longitude'])) {
                    $clientLat = $parsedLocation['latitude'];
                    $clientLng = $parsedLocation['longitude'];
                } else {
                    $coords = $locationService->geocode($storedLocation);
                    if ($coords) {
                        $clientLat = $coords['lat'];
                        $clientLng = $coords['lng'];
                    }
                }
            }
        }

        $trainers = $trainers->map(function (TrainerProfile $trainer) use ($clientLat, $clientLng, $locationService) {
            $trainer->distance_km = null;

            if (! empty($clientLat) && ! empty($clientLng) && ! empty($trainer->latitude) && ! empty($trainer->longitude)) {
                $trainer->distance_km = round($locationService->distanceBetween((float) $clientLat, (float) $clientLng, (float) $trainer->latitude, (float) $trainer->longitude), 2);
            }

            return $trainer;
        });

        if (! empty($clientLat) && ! empty($clientLng)) {
            $trainers = $trainers->sortBy(function (TrainerProfile $trainer) {
                return $trainer->distance_km === null ? INF : $trainer->distance_km;
            })->values();
        }

        return view('client.trainers', [
            'package' => $package,
            'trainers' => collect($trainers),
            'search' => $search,
        ]);
    }

    public function gyms(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        return view('client.gyms', [
            'gyms' => User::query()
                ->where('role', 'gym_owner')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('gym_name', 'like', "%{$search}%")
                            ->orWhere('gym_services', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%")
                            ->orWhere('nearby_locations', 'like', "%{$search}%");
                    });
                })
                ->orderByRaw('coalesce(gym_name, name)')
                ->get(),
            'search' => $search,
            'package' => MembershipPackage::visible()->whereKey($request->integer('package'))->first(),
        ]);
    }

    public function checkout(Request $request): View
    {
        $trainer = $request->integer('trainer')
            ? TrainerProfile::with(['user', 'preferredPackage'])->find($request->integer('trainer'))
            : null;

        $gym = $request->integer('gym')
            ? User::with('preferredPackage')->where('role', 'gym_owner')->find($request->integer('gym'))
            : null;

        $package = $request->integer('package')
            ? MembershipPackage::visible()->find($request->integer('package'))
            : null;

        if (! $package) {
            $package = $trainer?->preferredPackage ?? $gym?->preferredPackage;
        }

        if (! $package) {
            if ($trainer) {
                return redirect()->route('client.packages', ['trainer' => $trainer->id])
                    ->with('warning', 'Please select a package for the chosen trainer.');
            }
            if ($gym) {
                return redirect()->route('client.packages', ['gym' => $gym->id])
                    ->with('warning', 'Please select a package for the chosen gym.');
            }

            abort(404);
        }

        return view('client.checkout', [
            'package' => $package,
            'trainer' => $trainer,
            'gym' => $gym,
            'priceOverride' => $trainer?->preferred_rate ?? $gym?->preferred_rate,
        ]);
    }

    public function activate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'package_id' => ['required', 'exists:membership_packages,id'],
            'trainer_id' => ['nullable', 'exists:trainer_profiles,id'],
            'gym_id' => ['nullable', 'exists:users,id'],
            'method' => ['required', 'in:mpesa,card,bank,cash'],
            'amount' => ['nullable', 'numeric', 'min:1'],
        ]);

        $package = MembershipPackage::visible()->findOrFail($data['package_id']);
        $trainerProfile = isset($data['trainer_id']) ? TrainerProfile::find($data['trainer_id']) : null;
        $gymOwner = isset($data['gym_id']) ? User::where('role', 'gym_owner')->find($data['gym_id']) : null;
        $amount = $data['amount'] ?? $package->price;

        if ($trainerProfile) {
            $amount = $trainerProfile->preferred_rate ?? $amount;
        } elseif ($gymOwner) {
            $amount = $gymOwner->preferred_rate ?? $amount;
        }

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
            'status' => 'active',
            'activated_at' => now(),
        ]);

        Payment::create([
            'member_id' => Auth::id(),
            'membership_id' => $membership->id,
            'trainer_id' => $trainerProfile?->user_id,
            'gym_owner_id' => $gymOwner?->id,
            'phone' => Auth::user()->phone,
            'amount' => $amount,
            'method' => $data['method'],
            'status' => 'paid',
            'reference' => 'FITZONE-'.now()->format('YmdHis').'-'.Auth::id(),
            'paid_at' => now(),
            'notes' => 'Client checkout activation',
        ]);

        DietPlan::firstOrCreate(
            ['member_id' => Auth::id(), 'name' => $package->name.' Starter Diet Plan'],
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

        $this->createPackageWorkout($membership);

        return redirect()->route('client.today');
    }

    public function activation(): View|RedirectResponse
    {
        $membership = $this->paidActiveMembership();

        if ($membership) {
            return redirect()->route('client.today');
        }

        return view('client.activation', [
            'membership' => $membership,
            'latestMembership' => $this->latestRelevantMembership(),
        ]);
    }

    public function today(): View
    {
        $membership = $this->paidActiveMembership();
        $latestMembership = $this->latestRelevantMembership();
        $workoutPlan = null;
        $dietPlan = null;

        if ($membership) {
            $workoutPlan = Auth::user()->workoutPlans()->with(['trainer', 'exercises'])->latest()->first()
                ?? $this->createPackageWorkout($membership)->load(['trainer', 'exercises']);
            $dietPlan = Auth::user()->dietPlans()->latest()->first();
        }

        return view('client.today', [
            'membership' => $membership,
            'latestMembership' => $latestMembership,
            'workoutPlan' => $workoutPlan,
            'dietPlan' => $dietPlan,
        ]);
    }

    public function diet(): View
    {
        $membership = $this->paidActiveMembership();

        return view('client.diet', [
            'latestMembership' => $this->latestRelevantMembership(),
            'dietPlan' => $membership ? Auth::user()->dietPlans()->latest()->first() : null,
        ]);
    }

    public function workout(): View
    {
        $membership = $this->paidActiveMembership();
        $latestMembership = $this->latestRelevantMembership();
        $workoutPlan = null;

        if ($membership) {
            $workoutPlan = Auth::user()->workoutPlans()->with(['trainer', 'exercises'])->latest()->first()
                ?? $this->createPackageWorkout($membership)->load(['trainer', 'exercises']);
        }

        return view('client.workout-plan', [
            'membership' => $membership,
            'latestMembership' => $latestMembership,
            'workoutPlan' => $workoutPlan,
        ]);
    }

    public function attendance(): View
    {
        $membership = $this->paidActiveMembership();
        Auth::user()->ensureMemberIdentity();

        return view('client.attendance', [
            'membership' => $membership,
            'latestMembership' => $this->latestRelevantMembership(),
            'member' => Auth::user(),
            'attendances' => $membership ? Auth::user()->attendances()->latest('attendance_date')->take(30)->get() : collect(),
        ]);
    }

    public function payments(): View
    {
        return view('client.payments', [
            'payments' => Auth::user()->payments()->with('membership.package')->latest()->get(),
        ]);
    }

    public function chat(Request $request): View
    {
        $membership = $this->paidActiveMembership();
        $latestMembership = $this->latestRelevantMembership();
        $groupChat = $membership
            ? $this->packageChatFor($membership)
            : null;
        $trainerChat = $membership?->trainer_id
            ? $this->trainerChatFor($membership)
            : null;
        $memberChat = $request->integer('member_chat')
            ? ClientChat::with(['messages.sender', 'member', 'trainer'])->find($request->integer('member_chat'))
            : null;

        if ($memberChat && ! $this->canAccessChat($memberChat)) {
            abort(403);
        }

        return view('client.chat', [
            'membership' => $membership,
            'latestMembership' => $latestMembership,
            'groupChat' => $groupChat?->load(['messages.sender']),
            'trainerChat' => $trainerChat?->load(['messages.sender']),
            'memberChat' => $memberChat,
            'trainers' => TrainerProfile::with('user')->get(),
            'activeChat' => $request->query('room', 'group'),
            'activeCall' => CallRequest::where('caller_id', Auth::id())
                ->whereIn('status', ['ringing', 'accepted'])
                ->latest()
                ->first(),
        ]);
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'chat_id' => ['required', 'exists:client_chats,id'],
            'body' => ['required', 'string', 'max:1000'],
            'room' => ['nullable', 'in:group,trainer,member'],
        ]);

        $chat = ClientChat::findOrFail($data['chat_id']);

        abort_unless($this->canAccessChat($chat), 403);

        ClientChatMessage::create([
            'client_chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        return redirect()->route('client.chat', [
            'room' => $data['room'] ?? 'group',
            'member_chat' => $chat->type === 'member_direct' ? $chat->id : null,
        ]);
    }

    public function switchTrainer(Request $request): RedirectResponse
    {
        $membership = $this->paidActiveMembership();

        if (! $membership) {
            return $this->rechargeRedirect();
        }

        $data = $request->validate([
            'trainer_profile_id' => ['required', 'exists:trainer_profiles,id'],
        ]);

        $trainer = TrainerProfile::findOrFail($data['trainer_profile_id']);
        $membership->update(['trainer_id' => $trainer->user_id]);

        $this->trainerChatFor($membership->fresh(['trainer']));

        return redirect()->route('client.chat', ['room' => 'trainer']);
    }

    public function members(?MembershipPackage $package = null): View|RedirectResponse
    {
        if (! $this->paidActiveMembership()) {
            return $this->rechargeRedirect();
        }

        $package ??= MembershipPackage::where('slug', 'weekly-plan')->firstOrFail();
        $packages = MembershipPackage::visible()
            ->whereIn('duration_unit', ['week', 'month', 'year'])
            ->get();

        $members = User::query()
            ->where('role', 'member')
            ->whereKeyNot(Auth::id())
            ->whereHas('memberships', function ($query) use ($package) {
                $query->where('membership_package_id', $package->id)
                    ->paidActive();
            })
            ->with(['memberships' => function ($query) use ($package) {
                $query->where('membership_package_id', $package->id)
                    ->paidActive()
                    ->latest();
            }])
            ->orderBy('name')
            ->get();

        return view('client.members', [
            'package' => $package,
            'packages' => $packages,
            'members' => $members,
        ]);
    }

    public function startMemberChat(User $member): RedirectResponse
    {
        if (! $this->paidActiveMembership()) {
            return $this->rechargeRedirect();
        }

        abort_if($member->id === Auth::id() || $member->role !== 'member', 403);
        abort_unless($member->memberships()->paidActive()->exists(), 403);

        $chat = $this->memberChatFor(Auth::id(), $member->id);

        return redirect()->route('client.chat', ['room' => 'member', 'member_chat' => $chat->id]);
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'target_user_id' => ['required', 'exists:users,id'],
            'target_type' => ['required', 'in:trainer,gym'],
            'scheduled_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $target = $this->discoveryTarget($data['target_user_id'], $data['target_type']);

        Booking::create($data + [
            'client_id' => Auth::id(),
            'target_user_id' => $target->id,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Booking request sent to '.$this->targetLabel($target, $data['target_type']).'.');
    }

    public function storeReview(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'target_user_id' => ['required', 'exists:users,id'],
            'target_type' => ['required', 'in:trainer,gym'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['nullable', 'string', 'max:700'],
        ]);

        $target = $this->discoveryTarget($data['target_user_id'], $data['target_type']);

        Review::create($data + [
            'client_id' => Auth::id(),
            'target_user_id' => $target->id,
        ]);

        return back()->with('status', 'Review submitted for '.$this->targetLabel($target, $data['target_type']).'.');
    }

    public function storeComplaint(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'target_user_id' => ['required', 'exists:users,id'],
            'target_type' => ['required', 'in:trainer,gym'],
            'subject' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $target = $this->discoveryTarget($data['target_user_id'], $data['target_type']);

        Complaint::create($data + [
            'client_id' => Auth::id(),
            'target_user_id' => $target->id,
            'status' => 'open',
        ]);

        return back()->with('status', 'Complaint submitted for admin review.');
    }

    private function packageChatFor(Membership $membership): ClientChat
    {
        return ClientChat::firstOrCreate(
            ['type' => 'package_group', 'membership_package_id' => $membership->membership_package_id],
            ['title' => $membership->package->name.' Members Chat']
        );
    }

    private function discoveryTarget(int $targetUserId, string $targetType): User
    {
        $role = $targetType === 'trainer' ? 'trainer' : 'gym_owner';

        return User::whereKey($targetUserId)->where('role', $role)->firstOrFail();
    }

    private function targetLabel(User $target, string $targetType): string
    {
        return $targetType === 'gym'
            ? ($target->gym_name ?: $target->name)
            : $target->name;
    }

    private function trainerChatFor(Membership $membership): ClientChat
    {
        return ClientChat::firstOrCreate(
            ['type' => 'trainer_direct', 'member_id' => Auth::id(), 'trainer_id' => $membership->trainer_id],
            ['title' => 'Chat with '.$membership->trainer->name]
        );
    }

    private function memberChatFor(int $firstUserId, int $secondUserId): ClientChat
    {
        $memberIds = [min($firstUserId, $secondUserId), max($firstUserId, $secondUserId)];
        $firstUser = User::findOrFail($memberIds[0]);
        $secondUser = User::findOrFail($memberIds[1]);

        return ClientChat::firstOrCreate(
            ['type' => 'member_direct', 'member_id' => $memberIds[0], 'trainer_id' => $memberIds[1]],
            ['title' => $firstUser->name.' and '.$secondUser->name]
        );
    }

    private function canAccessChat(ClientChat $chat): bool
    {
        if ($chat->type === 'trainer_direct') {
            return $chat->member_id === Auth::id()
                && Auth::user()->memberships()->paidActive()->where('trainer_id', $chat->trainer_id)->exists();
        }

        if ($chat->type === 'member_direct') {
            return in_array(Auth::id(), [$chat->member_id, $chat->trainer_id], true)
                && User::whereKey($chat->member_id)->whereHas('memberships', fn ($query) => $query->paidActive())->exists()
                && User::whereKey($chat->trainer_id)->whereHas('memberships', fn ($query) => $query->paidActive())->exists();
        }

        return Auth::user()->memberships()
            ->where('membership_package_id', $chat->membership_package_id)
            ->paidActive()
            ->exists();
    }

    private function paidActiveMembership(): ?Membership
    {
        return Auth::user()->memberships()
            ->with(['package', 'trainer'])
            ->paidActive()
            ->latest()
            ->first();
    }

    private function latestRelevantMembership(): ?Membership
    {
        return Auth::user()->memberships()
            ->with('package')
            ->orderByRaw("case when status = 'active' then 0 when status = 'pending' then 1 when status = 'expired' then 2 else 3 end")
            ->latest('activated_at')
            ->latest()
            ->first();
    }

    private function rechargeRedirect(): RedirectResponse
    {
        return redirect()
            ->route('client.packages')
            ->with('warning', 'Your package days are over. Please recharge to unlock members, chat, calls, workouts, diet, and attendance again.');
    }

    private function todayPlanSessionKey(Membership $membership): string
    {
        return 'today_plan_opened_for_membership_'.$membership->id;
    }

    private function createPackageWorkout(Membership $membership): WorkoutPlan
    {
        $package = $membership->package;
        $template = $this->workoutTemplateFor($package);

        $plan = WorkoutPlan::firstOrCreate(
            [
                'member_id' => Auth::id(),
                'title' => $template['title'],
            ],
            [
                'trainer_id' => $membership->trainer_id,
                'focus_area' => $template['focus_area'],
                'notes' => $template['notes'],
                'starts_at' => $membership->starts_at,
                'ends_at' => $membership->ends_at,
                'is_active' => true,
            ]
        );

        if ($plan->exercises()->doesntExist()) {
            foreach ($template['exercises'] as $index => $exercise) {
                $plan->exercises()->create($exercise + ['sort_order' => $index + 1]);
            }
        }

        return $plan;
    }

    private function workoutTemplateFor(MembershipPackage $package): array
    {
        return match ($package->access_level) {
            'elite' => [
                'title' => $package->name.' Elite Performance Workout',
                'focus_area' => 'Strength, conditioning, and recovery',
                'notes' => 'A complete weekly routine for paid yearly members with trainer guidance.',
                'exercises' => [
                    ['exercise_name' => 'Barbell Squat', 'sets' => 5, 'reps' => 5, 'instructions' => 'Warm up first and keep controlled depth.', 'trainer_notes' => 'Increase load only when form is stable.'],
                    ['exercise_name' => 'Bench Press', 'sets' => 4, 'reps' => 8, 'instructions' => 'Keep shoulder blades tight.', 'trainer_notes' => 'Use a spotter for heavy sets.'],
                    ['exercise_name' => 'Row Machine Intervals', 'sets' => 6, 'reps' => 1, 'instructions' => 'One minute hard, one minute easy.', 'trainer_notes' => 'Track distance each round.'],
                    ['exercise_name' => 'Mobility Cooldown', 'sets' => 3, 'reps' => 10, 'instructions' => 'Stretch hips, shoulders, and back.', 'trainer_notes' => 'Do this after every session.'],
                ],
            ],
            'premium' => [
                'title' => $package->name.' Premium Strength Workout',
                'focus_area' => 'Muscle gain and consistency',
                'notes' => 'A balanced plan for paid monthly members.',
                'exercises' => [
                    ['exercise_name' => 'Incline Dumbbell Press', 'sets' => 4, 'reps' => 10, 'instructions' => 'Use a medium incline and slow tempo.', 'trainer_notes' => 'Do not rush the lowering phase.'],
                    ['exercise_name' => 'Lat Pulldown', 'sets' => 4, 'reps' => 12, 'instructions' => 'Pull to upper chest.', 'trainer_notes' => 'Keep shoulders down.'],
                    ['exercise_name' => 'Leg Press', 'sets' => 4, 'reps' => 12, 'instructions' => 'Keep feet flat.', 'trainer_notes' => 'Avoid locking knees.'],
                    ['exercise_name' => 'Plank', 'sets' => 3, 'reps' => 45, 'instructions' => 'Hold for seconds with a braced core.', 'trainer_notes' => 'Stop if lower back drops.'],
                ],
            ],
            'standard' => [
                'title' => $package->name.' Weekly Conditioning Workout',
                'focus_area' => 'General fitness',
                'notes' => 'A simple weekly routine for paid weekly members.',
                'exercises' => [
                    ['exercise_name' => 'Treadmill Walk/Jog', 'sets' => 1, 'reps' => 20, 'instructions' => 'Twenty minutes at a steady pace.', 'trainer_notes' => 'Keep breathing controlled.'],
                    ['exercise_name' => 'Goblet Squat', 'sets' => 3, 'reps' => 12, 'instructions' => 'Hold a dumbbell close to your chest.', 'trainer_notes' => 'Keep knees aligned with toes.'],
                    ['exercise_name' => 'Push Ups', 'sets' => 3, 'reps' => 10, 'instructions' => 'Use knees if needed.', 'trainer_notes' => 'Keep a straight body line.'],
                ],
            ],
            default => [
                'title' => $package->name.' Starter Workout',
                'focus_area' => 'Beginner gym access',
                'notes' => 'A light routine for paid basic or daily access.',
                'exercises' => [
                    ['exercise_name' => 'Bike Warmup', 'sets' => 1, 'reps' => 10, 'instructions' => 'Ten minutes at easy pace.', 'trainer_notes' => 'Use this to warm up safely.'],
                    ['exercise_name' => 'Bodyweight Squat', 'sets' => 3, 'reps' => 12, 'instructions' => 'Sit hips back and keep chest up.', 'trainer_notes' => 'Move slowly.'],
                    ['exercise_name' => 'Dumbbell Row', 'sets' => 3, 'reps' => 10, 'instructions' => 'Pull elbow toward your hip.', 'trainer_notes' => 'Keep back flat.'],
                ],
            ],
        };
    }
}
