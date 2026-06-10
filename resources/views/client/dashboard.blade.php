@extends('layouts.app')

@section('title', 'Client Dashboard - Fitzone')

@section('content')
<h1>Client Dashboard</h1>

@if (! $membership && ($latestMembership?->status === 'expired'))
    <section class="card" style="margin-bottom:16px;border-color:#fbbf24;background:#fffbeb;box-shadow:none">
        <h2>Your package days are over</h2>
        <p class="muted">Recharge your package to unlock members, chat, calls, workouts, diet, and attendance again.</p>
        <a class="btn" href="{{ route('client.packages') }}">Recharge Package</a>
    </section>
@endif

<section class="card friendly-hero" style="margin-bottom:16px;display:grid;grid-template-columns:1.2fr .8fr;gap:18px;align-items:center">
    <div>
        <h2>Start strong, we will guide the steps</h2>
        <p class="muted">Choose a plan, pick a trainer if you want one, pay, and your membership unlocks workouts, attendance history, diet guidance, and payment receipts.</p>
        <div class="actions" style="justify-content:flex-start">
            <a class="btn" href="{{ route('client.packages') }}"><span>+</span> Choose Package</a>
            <a class="btn ghost" href="{{ route('client.trainers') }}">Preview Trainers</a>
        </div>
    </div>
    <div class="grid">
        <div class="step-chip"><span class="step-icon">1</span> Package</div>
        <div class="step-chip"><span class="step-icon">2</span> Trainer</div>
        <div class="step-chip"><span class="step-icon">3</span> Payment</div>
        <div class="step-chip"><span class="step-icon">4</span> Activation</div>
    </div>
</section>

<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">P</div><div><small>Membership</small><strong>{{ $membership?->package?->name ?? 'None' }}</strong><span class="up">{{ $membership ? ucfirst($membership->status) : 'Choose a package' }}</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">T</div><div><small>Trainer</small><strong>{{ $membership?->trainer?->name ?? 'Optional' }}</strong><span class="up">Available during checkout</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">N</div><div><small>Diet Plan</small><strong>{{ $dietPlan ? 'Unlocked' : 'Locked' }}</strong><span class="up">After activation</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ede9fe;color:var(--violet)">A</div><div><small>Attendance</small><strong>{{ $attendanceCount }}</strong><span class="up">Present records</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">KES</div><div><small>Wallet</small><strong>{{ number_format($wallet->balance, 2) }}</strong><span class="up">Café balance</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">ID</div><div><small>Member Number</small><strong>{{ auth()->user()->member_number }}</strong><span class="up">Use this for manual check-in</span></div></article>
</section>

<div class="grid two">
    <section class="card">
        <h2>Workout Preview</h2>
        @if ($workoutPlan)
            <p><span class="badge green">Active</span> {{ $workoutPlan->title }}</p>
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Exercise</th><th>Sets</th><th>Reps</th></tr></thead>
                    <tbody>
                        @foreach ($workoutPlan->exercises->take(3) as $exercise)
                            <tr><td>{{ $exercise->exercise_name }}</td><td>{{ $exercise->sets }}</td><td>{{ $exercise->reps }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="muted">Workout plans unlock after your membership is active and a trainer has assigned exercises.</p>
            <a class="btn" href="{{ route('client.packages') }}"><span>+</span> Choose Package</a>
        @endif
    </section>
    <section class="card">
        <h2>Recent Payments</h2>
        @if ($recentPayments->isNotEmpty())
            <table>
                <tbody>
                    @foreach ($recentPayments as $payment)
                        <tr><td>{{ $payment->created_at->format('d M Y') }}</td><td>KES {{ number_format($payment->amount) }}</td><td><span class="badge green">{{ ucfirst($payment->status) }}</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="muted">No payments yet.</p>
        @endif
    </section>
</div>

<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <h2>Recent Café Orders</h2>
        <a class="btn ghost" href="{{ route('client.cafe') }}">Open Café</a>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Order</th><th>Status</th><th>Total</th><th>Time</th></tr></thead>
            <tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td><span class="badge {{ $order->status === 'completed' ? 'green' : ($order->status === 'cancelled' ? 'red' : 'amber') }}">{{ ucfirst($order->status) }}</span></td>
                        <td>KES {{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No café orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
