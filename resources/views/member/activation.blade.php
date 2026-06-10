@extends('layouts.app')

@section('title', 'Membership Activated - Fitzone')

@section('content')
<h1>Membership Activation</h1>
<section class="card">
    @if ($membership)
        <h2>Your membership is active</h2>
        <p><span class="badge green">Active</span> {{ $membership->package->name }}</p>
        <table>
            <tbody>
                <tr><td>Trainer</td><td>{{ $membership->trainer?->name ?? 'No trainer selected' }}</td></tr>
                <tr><td>Start Date</td><td>{{ $membership->starts_at->format('d M Y') }}</td></tr>
                <tr><td>End Date</td><td>{{ $membership->ends_at->format('d M Y') }}</td></tr>
                <tr><td>Unlocked</td><td>Workout plans, attendance, diet plan, payment history</td></tr>
            </tbody>
        </table>
        <div class="actions" style="justify-content:flex-start">
            <a class="btn" href="{{ route('member.workout') }}">View Workout</a>
            <a class="btn ghost" href="{{ route('member.diet') }}">View Diet Plan</a>
        </div>
    @else
        <p class="muted">No active membership found yet.</p>
        <a class="btn" href="{{ route('member.packages') }}">Choose Package</a>
    @endif
</section>
@endsection
