<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AdminTrainerMessage;
use App\Models\CallRequest;
use App\Models\ClientChat;
use App\Models\ClientChatMessage;
use App\Models\Membership;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'memberCount' => User::where('role', 'member')->count(),
            'trainerCount' => User::where('role', 'trainer')->count(),
            'activeMemberships' => Membership::where('status', 'active')->count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
            'cafeRevenue' => Order::where('status', 'completed')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->sum('total_amount'),
            'monthlyIncome' => Payment::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'recentPayments' => Payment::with(['member', 'membership.package'])->latest()->take(6)->get(),
            'recentMembers' => User::where('role', 'member')->with(['memberships.package', 'memberships.trainer'])->latest()->take(6)->get(),
            'recentCalls' => CallRequest::with(['caller', 'trainer'])->latest()->take(5)->get(),
            'recentOrders' => Order::with('member')->latest()->take(5)->get(),
        ]);
    }

    public function users(): View
    {
        return view('admin.users', [
            'users' => User::with(['trainerProfile', 'memberships.package', 'memberships.trainer'])
                ->orderBy('role')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function trainers(): View
    {
        return view('admin.trainers', [
            'trainers' => User::with(['trainerProfile', 'assignedMemberships.member', 'trainerAdminMessages.admin'])
                ->where('role', 'trainer')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeTrainer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'specialty' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:80'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'county_id' => ['nullable', 'exists:counties,id'],
            'town' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $trainer = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'trainer',
            'status' => 'active',
        ]);

        TrainerProfile::create([
            'user_id' => $trainer->id,
            'specialty' => $data['specialty'],
            'category' => $data['category'],
            'experience_years' => $data['experience_years'],
            'rating' => $data['rating'] ?? 5,
            'bio' => $data['bio'] ?? null,
            'county_id' => $data['county_id'] ?? null,
            'town' => $data['town'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        return back()->with('status', 'Trainer added successfully.');
    }

    public function cafeStaff(): View
    {
        return view('admin.cafe-staff', [
            'cafeStaff' => User::where('role', 'cafe')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeCafeStaff(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'cafe',
            'status' => 'active',
        ]);

        return back()->with('status', 'Cafe staff added successfully.');
    }

    public function destroyCafeStaff(User $staff): RedirectResponse
    {
        abort_unless($staff->role === 'cafe', 404);

        $staffName = $staff->name;
        $staff->delete();

        return back()->with('status', $staffName.' was removed from cafe staff.');
    }

    public function messageTrainer(Request $request, User $trainer): RedirectResponse
    {
        abort_unless($trainer->role === 'trainer', 404);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        AdminTrainerMessage::create([
            'admin_id' => $request->user()->id,
            'trainer_id' => $trainer->id,
            'body' => $data['body'],
        ]);

        return back()->with('status', 'Message sent to '.$trainer->name.'.');
    }

    public function destroyTrainer(User $trainer): RedirectResponse
    {
        abort_unless($trainer->role === 'trainer', 404);

        $trainerName = $trainer->name;
        $trainer->delete();

        return back()->with('status', $trainerName.' was removed from the trainer records.');
    }

    public function member(User $member): View
    {
        abort_unless($member->role === 'member', 404);

        return view('admin.member', [
            'member' => $member->load([
                'memberships.package',
                'memberships.trainer',
                'payments.membership.package',
                'attendances',
                'workoutPlans.exercises',
                'dietPlans',
                'progressRecords.trainer',
                'loyaltyPoints',
            ]),
        ]);
    }

    public function chats(Request $request): View
    {
        $chats = ClientChat::with(['member', 'trainer', 'package', 'messages.sender'])
            ->latest()
            ->get();

        $activeChat = $request->integer('chat')
            ? $chats->firstWhere('id', $request->integer('chat'))
            : $chats->first();

        return view('admin.chats', [
            'chats' => $chats,
            'activeChat' => $activeChat,
        ]);
    }

    public function sendMessage(Request $request, ClientChat $chat): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        ClientChatMessage::create([
            'client_chat_id' => $chat->id,
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return redirect()->route('admin.chats', ['chat' => $chat->id]);
        }

    }
