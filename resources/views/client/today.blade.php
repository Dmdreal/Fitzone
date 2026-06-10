@extends('layouts.app')

@section('title', 'Today - Fitzone')

@section('content')
<h1>Today</h1>

@if (! $membership)
    <section class="card">
        @if ($latestMembership?->status === 'pending')
            <h2>Payment awaiting confirmation</h2>
            <p><span class="badge amber">Pending</span> {{ $latestMembership->package?->name ?? 'Membership package' }}</p>
            <p class="muted">Your diet and training for today will open automatically after the payment is confirmed.</p>
            <div class="actions" style="justify-content:flex-start">
                <a class="btn ghost" href="{{ route('client.payments') }}">View Payment Status</a>
                <a class="btn" href="{{ route('client.activation') }}">Check Activation</a>
            </div>
        @elseif ($latestMembership?->status === 'expired')
            <h2>Your package days are over</h2>
            <p class="muted">Recharge your package to unlock today's diet and training again.</p>
            <a class="btn" href="{{ route('client.packages') }}">Recharge Package</a>
        @else
            <h2>No active package yet</h2>
            <p class="muted">Choose a package and complete payment to unlock today's plan.</p>
            <a class="btn" href="{{ route('client.packages') }}">Choose Package</a>
        @endif
    </section>
@else
    <section class="card friendly-hero" style="margin-bottom:16px">
        <h2>Paid and active: {{ $membership->package->name }}</h2>
        <p class="muted">Here is your diet and training for today. Keep it simple, finish the session, and check back tomorrow.</p>
        <div class="actions" style="justify-content:flex-start">
            <a class="btn ghost" href="{{ route('client.dashboard') }}">Dashboard</a>
            <a class="btn ghost" href="{{ route('client.chat') }}">Ask Trainer</a>
        </div>
    </section>

    <div class="grid two">
        <section class="card">
            <h2>Training of the Day</h2>
            @if ($workoutPlan)
                <p><span class="badge green">Workout</span> {{ $workoutPlan->title }}</p>
                <p class="muted">{{ $workoutPlan->notes }}</p>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>Exercise</th><th>Sets</th><th>Reps</th><th>Instructions</th></tr></thead>
                        <tbody>
                            @foreach ($workoutPlan->exercises as $exercise)
                                <tr>
                                    <td>{{ $exercise->exercise_name }}</td>
                                    <td>{{ $exercise->sets }}</td>
                                    <td>{{ $exercise->reps }}</td>
                                    <td>{{ $exercise->instructions }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="muted">No workout has been created yet. Refresh this page or ask your trainer.</p>
            @endif
        </section>

        <section class="card">
            <h2>Diet of the Day</h2>
            @if ($dietPlan)
                <p><span class="badge green">{{ str_replace('_', ' ', ucfirst($dietPlan->goal)) }}</span> {{ $dietPlan->daily_calories }} calories per day</p>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>Meal</th><th>Recommendation</th></tr></thead>
                        <tbody>
                            @foreach (($dietPlan->meal_schedule ?? []) as $meal => $recommendation)
                                <tr>
                                    <td>{{ ucfirst($meal) }}</td>
                                    <td>{{ $recommendation }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="muted">{{ $dietPlan->meal_delivery_available ? 'Meal delivery can be enabled for this plan.' : 'Meal delivery is not included in this package.' }}</p>
            @else
                <p class="muted">No diet plan has been created yet. Your active package is paid, so ask admin or your trainer to review it.</p>
            @endif
        </section>
    </div>
@endif
@endsection
