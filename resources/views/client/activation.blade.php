@extends('layouts.app')

@section('title', 'Activation - Fitzone')

@section('content')
<h1>Membership Activation</h1>
<section class="card">
    @php
        $activeMembership = $membership ?? ($latestMembership?->status === 'active' ? $latestMembership : null);
    @endphp

    @if ($activeMembership)
        <h2>Your membership is active</h2>
        <p><span class="badge green">Paid</span> {{ $activeMembership->package->name }}</p>
        <table>
            <tbody>
                <tr><td>Trainer</td><td>{{ $activeMembership->trainer?->name ?? 'No trainer selected' }}</td></tr>
                <tr><td>Start Date</td><td>{{ $activeMembership->starts_at->format('d M Y') }}</td></tr>
                <tr><td>End Date</td><td>{{ $activeMembership->ends_at->format('d M Y') }}</td></tr>
                <tr><td>Unlocked</td><td>Workout plan, attendance, diet plan, and payment history</td></tr>
            </tbody>
        </table>
        <div class="actions" style="justify-content:flex-start">
            <a class="btn" href="{{ route('client.workout') }}">View Workout</a>
            <a class="btn ghost" href="{{ route('client.diet') }}">View Diet Plan</a>
        </div>
    @else
        @if ($latestMembership?->status === 'pending')
            <h2>Payment awaiting confirmation</h2>
            <p><span class="badge amber">Pending</span> {{ $latestMembership->package?->name ?? 'Membership package' }}</p>
            <p class="muted">Your workouts, diet, attendance, chat, calls, and members area stay locked until the payment is confirmed by M-PESA callback or approved by an admin/trainer.</p>
            <div class="actions" style="justify-content:flex-start">
                <a class="btn ghost" href="{{ route('client.payments') }}">View Payment Status</a>
                <a class="btn" href="{{ route('client.packages') }}">Choose Another Package</a>
            </div>
        @elseif ($latestMembership?->status === 'expired')
            <h2>Your package days are over</h2>
            <p class="muted">Recharge your package to unlock members, chat, calls, workouts, diet, and attendance again.</p>
            <a class="btn" href="{{ route('client.packages') }}">Recharge Package</a>
        @else
            <p class="muted">No active membership found yet.</p>
            <a class="btn" href="{{ route('client.packages') }}">Choose Package</a>
        @endif
    @endif
</section>
@endsection
