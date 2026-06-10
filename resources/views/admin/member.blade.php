@extends('layouts.app')

@section('title', $member->name.' - Client Details')

@section('content')
<h1>{{ $member->name }}</h1>

<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">P</div><div><small>Payments</small><strong>{{ $member->payments->count() }}</strong><span class="up">KES {{ number_format($member->payments->sum('amount')) }}</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">A</div><div><small>Attendance</small><strong>{{ $member->attendances->where('status', 'present')->count() }}</strong><span class="up">Present records</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">W</div><div><small>Workout Plans</small><strong>{{ $member->workoutPlans->count() }}</strong><span class="up">Assigned programs</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ede9fe;color:var(--violet)">L</div><div><small>Loyalty</small><strong>{{ $member->loyaltyPoints->sum('points') }}</strong><span class="up">Reward points</span></div></article>
</section>

<div class="grid two">
    <section class="card">
        <h2>Memberships</h2>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Package</th><th>Trainer</th><th>Dates</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($member->memberships as $membership)
                        <tr>
                            <td>{{ $membership->package->name }}</td>
                            <td>{{ $membership->trainer?->name ?? 'Not assigned' }}</td>
                            <td>{{ $membership->starts_at->format('d M Y') }} - {{ $membership->ends_at->format('d M Y') }}</td>
                            <td><span class="badge {{ $membership->status === 'active' ? 'green' : 'amber' }}">{{ ucfirst($membership->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No memberships.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="card">
        <h2>Diet Plans</h2>
        @forelse ($member->dietPlans as $plan)
            <p><span class="badge green">{{ ucfirst(str_replace('_', ' ', $plan->goal)) }}</span> {{ $plan->name }} - {{ $plan->daily_calories ?? 'Custom' }} calories</p>
        @empty
            <p class="muted">No diet plan yet.</p>
        @endforelse
    </section>
</div>

<div class="grid two">
    <section class="card">
        <h2>Payments</h2>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($member->payments as $payment)
                        <tr><td>{{ $payment->created_at->format('d M Y') }}</td><td>KES {{ number_format($payment->amount) }}</td><td>{{ strtoupper($payment->method) }}</td><td>{{ ucfirst($payment->status) }}</td></tr>
                    @empty
                        <tr><td colspan="4">No payments.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="card">
        <h2>Workout Plans</h2>
        @forelse ($member->workoutPlans as $plan)
            <h3>{{ $plan->title }}</h3>
            <p class="muted">{{ $plan->notes }}</p>
            <table>
                <tbody>
                    @foreach ($plan->exercises as $exercise)
                        <tr><td>{{ $exercise->exercise_name }}</td><td>{{ $exercise->sets }} x {{ $exercise->reps }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        @empty
            <p class="muted">No workout plan yet.</p>
        @endforelse
    </section>
</div>
@endsection
