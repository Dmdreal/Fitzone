<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\CallRequest;
use App\Models\ClientChat;
use App\Models\ClientChatMessage;
use App\Models\Membership;
use App\Models\MembershipPackage;
use App\Models\Payment;
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
        ]);
    }

    public function packages(): View
    {
        return view('client.packages', [
            'packages' => MembershipPackage::visible()->get(),
        ]);
    }

    public function trainers(Request $request): View
    {
        return view('client.trainers', [
            'package' => MembershipPackage::visible()->whereKey($request->integer('package'))->first(),
            'trainers' => TrainerProfile::with('user')->get(),
        ]);
    }

    public function checkout(Request $request): View
    {
        return view('client.checkout', [
            'package' => MembershipPackage::visible()->findOrFail($request->integer('package')),
            'trainer' => TrainerProfile::with('user')->whereKey($request->integer('trainer'))->first(),
        ]);
    }

    public function activate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'package_id' => ['required', 'exists:membership_packages,id'],
            'trainer_id' => ['nullable', 'exists:trainer_profiles,id'],
            'method' => ['required', 'in:mpesa,card,bank,cash'],
        ]);

        $package = MembershipPackage::visible()->findOrFail($data['package_id']);
        $trainerProfile = isset($data['trainer_id']) ? TrainerProfile::find($data['trainer_id']) : null;
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
            'starts_at' => $startsAt->toDateString(),
            'ends_at' => $endsAt->toDateString(),
            'status' => 'active',
            'activated_at' => now(),
        ]);

        Payment::create([
            'member_id' => Auth::id(),
            'membership_id' => $membership->id,
            'phone' => Auth::user()->phone,
            'amount' => $package->price,
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

    private function packageChatFor(Membership $membership): ClientChat
    {
        return ClientChat::firstOrCreate(
            ['type' => 'package_group', 'membership_package_id' => $membership->membership_package_id],
            ['title' => $membership->package->name.' Members Chat']
        );
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
