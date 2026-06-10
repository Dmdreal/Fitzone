@extends('layouts.app')

@section('title', 'Diet Plan - Fitzone')

@section('content')
<h1>Diet Plan</h1>
<section class="card">
    @if ($dietPlan)
        <h2>{{ $dietPlan->name }}</h2>
        <p><span class="badge green">{{ str_replace('_', ' ', ucfirst($dietPlan->goal)) }}</span> {{ $dietPlan->daily_calories }} calories per day</p>
        <table>
            <thead><tr><th>Meal</th><th>Recommendation</th></tr></thead>
            <tbody>
                @foreach (($dietPlan->meal_schedule ?? []) as $meal => $recommendation)
                    <tr><td>{{ ucfirst($meal) }}</td><td>{{ $recommendation }}</td></tr>
                @endforeach
            </tbody>
        </table>
        <p class="muted">{{ $dietPlan->meal_delivery_available ? 'Meal delivery can be enabled for this plan.' : 'Meal delivery is not included in this package.' }}</p>
    @else
        @if ($latestMembership?->status === 'expired')
            <h2>Your package days are over</h2>
            <p class="muted">Recharge your package to unlock diet recommendations again.</p>
            <a class="btn" href="{{ route('client.packages') }}">Recharge Package</a>
        @else
            <h2>Diet plan locked</h2>
            <p class="muted">Activate a package to unlock diet recommendations.</p>
            <a class="btn" href="{{ route('client.packages') }}">Choose Package</a>
        @endif
    @endif
</section>
@endsection
