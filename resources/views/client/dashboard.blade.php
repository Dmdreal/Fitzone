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

<section class="card friendly-hero" style="margin-bottom:16px;display:grid;grid-template-columns:1.3fr .9fr;gap:20px;align-items:center">
    <div>
        <p class="badge green" style="display:inline-flex;margin-bottom:12px">Goal: {{ auth()->user()->fitness_goal ? str_replace('_', ' ', ucfirst(auth()->user()->fitness_goal)) : 'Find your focus' }}</p>
        <h2>Welcome back, {{ auth()->user()->name }}</h2>
        <p class="muted">{{ auth()->user()->experience_level ? ucfirst(auth()->user()->experience_level).' level' : 'Tell us your experience to get better trainer matches.' }} • {{ auth()->user()->diet_preference ? ucfirst(auth()->user()->diet_preference).' diet' : 'Diet preference helps personalize nutrition guidance.' }}</p>
        <div class="actions" style="justify-content:flex-start;flex-wrap:wrap">
            <a class="btn" href="{{ route('client.trainers') }}"><span>+</span> Find Trainer</a>
            <a class="btn ghost" href="{{ route('client.today') }}">Today’s Plan</a>
            <a class="btn ghost" href="{{ route('client.payments') }}">Payments</a>
            <a class="btn ghost" href="{{ route('profile.edit') }}">Profile</a>
        </div>
    </div>
    <div class="grid" style="gap:12px">
        <div class="step-chip"><span class="step-icon">1</span> Choose Plan</div>
        <div class="step-chip"><span class="step-icon">2</span> Select Trainer</div>
        <div class="step-chip"><span class="step-icon">3</span> Pay & Activate</div>
        <div class="step-chip"><span class="step-icon">4</span> Track Progress</div>
    </div>
</section>

@if(session()->has('nearby_trainers') && count(session('nearby_trainers', [])) > 0)
<section class="card" style="margin-bottom:16px">
    <h2>Recommended Trainers Near You</h2>
    <p class="muted">Suggested based on your chosen location.</p>
    <div class="grid three">
        @foreach(session('nearby_trainers') as $trainer)
            <article class="card" style="box-shadow:none">
                <h3>{{ $trainer['name'] ?? 'Trainer' }}</h3>
                <p class="muted">{{ $trainer['specialty'] ?? 'Fitness coach' }}</p>
                <p><strong>Distance:</strong> {{ $trainer['distance_km'] ?? 'N/A' }} km</p>
                <p><strong>Area:</strong> {{ $trainer['town'] ?? 'Nearby' }}</p>
                <a class="btn" href="{{ route('client.trainers', ['q' => $trainer['town'] ?? $trainer['name']]) }}">View Trainers</a>
            </article>
        @endforeach
    </div>
</section>
@endif

<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">P</div><div><small>Membership</small><strong>{{ $membership?->package?->name ?? 'No package yet' }}</strong><span class="up">{{ $membership ? ucfirst($membership->status) : 'Unlock your first plan' }}</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">T</div><div><small>Trainer</small><strong>{{ $membership?->trainer?->name ?? 'Not assigned' }}</strong><span class="up">{{ $membership?->trainer ? 'Chat now' : 'Pick a trainer' }}</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">D</div><div><small>Diet plan</small><strong>{{ $dietPlan ? 'Ready' : 'Locked' }}</strong><span class="up">{{ $dietPlan ? str_replace('_', ' ', ucfirst($dietPlan->goal)) : 'Activate membership' }}</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ede9fe;color:var(--violet)">A</div><div><small>Attendance</small><strong>{{ $attendanceCount }}</strong><span class="up">Present sessions</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">KES</div><div><small>Recent spend</small><strong>KES {{ $recentPayments->first()?->amount ? number_format($recentPayments->first()->amount, 2) : '0.00' }}</strong><span class="up">Latest payment</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">N</div><div><small>Nutrition</small><strong>{{ auth()->user()->diet_preference ? ucfirst(auth()->user()->diet_preference) : 'Not set' }}</strong><span class="up">{{ auth()->user()->budget_range ? auth()->user()->budget_range : 'Budget needed' }}</span></div></article>
</section>

<div class="grid two">
    <section class="card">
        <h2>My Trainer</h2>
        @if ($membership && $membership->trainer)
            <p class="muted">Your assigned trainer is ready to support your goal.</p>
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
                <div class="avatar" style="width:60px;height:60px;border-radius:18px;background:#dbeafe;color:var(--blue);font-size:22px;">{{ strtoupper(substr($membership->trainer->name, 0, 1)) }}</div>
                <div>
                    <strong>{{ $membership->trainer->name }}</strong>
                    <div class="muted">{{ $membership->trainer->headline ?? $membership->trainer->location ?? 'Trainer profile' }}</div>
                </div>
            </div>
            <div class="actions" style="justify-content:flex-start">
                <a class="btn" href="{{ route('client.chat') }}">Chat Trainer</a>
                <a class="btn ghost" href="{{ route('client.workout') }}">View Workout</a>
            </div>
        @else
            <p class="muted">A trainer helps you stay consistent, track progress, and adjust workouts.</p>
            <a class="btn" href="{{ route('client.trainers') }}">Browse Trainers</a>
        @endif
    </section>

    <section class="card">
        <h2>Progress Tracker</h2>
        @if ($latestProgress)
            <p class="muted">Latest measurement was recorded on {{ $latestProgress->recorded_at->format('d M Y') }}.</p>
            <div class="table-scroll">
                <table>
                    <tbody>
                        <tr><td>Weight</td><td>{{ $latestProgress->weight_kg }} kg</td></tr>
                        <tr><td>Body fat</td><td>{{ $latestProgress->body_fat_percentage }}%</td></tr>
                        <tr><td>Notes</td><td>{{ $latestProgress->notes ?? 'No notes recorded' }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="actions" style="justify-content:flex-start">
                <a class="btn ghost" href="{{ route('client.attendance') }}">Attendance</a>
                <a class="btn ghost" href="{{ route('client.diet') }}">Diet Plan</a>
            </div>
        @else
            <p class="muted">No progress record found yet. Your trainer or admin can add your first measurement.</p>
            <div class="actions" style="justify-content:flex-start">
                <a class="btn ghost" href="{{ route('client.attendance') }}">Attendance</a>
                <a class="btn ghost" href="{{ route('client.diet') }}">Diet Plan</a>
            </div>
        @endif
    </section>
</div>

<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <h2>Recent Payments</h2>
        <a class="btn ghost" href="{{ route('client.payments') }}">View All</a>
    </div>
    @if ($recentPayments->isNotEmpty())
        <div class="table-scroll">
            <table>
                <thead><tr><th>Date</th><th>Amount</th><th>Status</th><th>Package</th></tr></thead>
                <tbody>
                    @foreach ($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td>KES {{ number_format($payment->amount, 2) }}</td>
                            <td><span class="badge {{ $payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : 'amber') }}">{{ ucfirst($payment->status) }}</span></td>
                            <td>{{ $payment->membership?->package?->name ?? 'General payment' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="muted">No payments yet. Choose a package to begin.</p>
    @endif
</section>
@endsection
