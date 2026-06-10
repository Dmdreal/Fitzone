@extends('layouts.app')

@section('title', 'Workout Plan - Fitzone')

@section('content')
<h1>Workout Plan</h1>
<section class="card">
    @if (! $membership)
        @if ($latestMembership?->status === 'expired')
            <h2>Your package days are over</h2>
            <p class="muted">Recharge your package to unlock your workout plan again.</p>
            <a class="btn" href="{{ route('client.packages') }}">Recharge Package</a>
        @else
            <h2>Workout locked until payment</h2>
            <p class="muted">Choose a package and complete payment first. After activation, this page opens the workout that matches the package you paid for.</p>
            <a class="btn" href="{{ route('client.packages') }}">Choose Package</a>
        @endif
    @elseif ($workoutPlan)
        <h2>{{ $workoutPlan->title }}</h2>
        <p><span class="badge green">Paid</span> {{ $membership->package->name }}</p>
        <p class="muted">{{ $workoutPlan->notes }}</p>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Exercise</th><th>Sets</th><th>Reps</th><th>Instructions</th><th>Trainer Notes</th></tr></thead>
                <tbody>
                    @foreach ($workoutPlan->exercises as $exercise)
                        <tr><td>{{ $exercise->exercise_name }}</td><td>{{ $exercise->sets }}</td><td>{{ $exercise->reps }}</td><td>{{ $exercise->instructions }}</td><td>{{ $exercise->trainer_notes }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <h2>No workout plan yet</h2>
        <p class="muted">Your payment is active, but a workout plan has not been created yet. Try refreshing this page or ask your trainer/admin to review your package.</p>
        <a class="btn" href="{{ route('client.packages') }}">Choose Package</a>
    @endif
</section>
@endsection
