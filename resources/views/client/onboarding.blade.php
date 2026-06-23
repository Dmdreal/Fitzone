@extends('layouts.app')

@section('title', 'Get Started - Fitzone')

@section('content')
<h1>Welcome to Fitzone!</h1>

<section class="card friendly-hero" style="margin-bottom:20px">
    <h2>{{ auth()->user()->name }}, let's get you started</h2>
    <p class="muted">Your fitness goal: <strong>{{ ucfirst(str_replace('_', ' ', auth()->user()->fitness_goal)) }}</strong> • Experience: <strong>{{ ucfirst(auth()->user()->experience_level) }}</strong></p>
    <p class="muted">Location: <strong>{{ auth()->user()->location ?: 'Not specified' }}</strong></p>
</section>

<div class="grid two" style="margin-bottom:20px">
    <section class="card">
        <h2>Find a Trainer</h2>
        <p class="muted">Get matched with experienced trainers in your area who specialize in your fitness goals.</p>
        <div style="display:flex;gap:10px;flex-direction:column">
            <form method="GET" action="{{ route('client.trainers') }}" class="form-grid">
                <label>Search trainers
                    <input name="q" placeholder="Name, specialty, location..." value="">
                </label>
                <button class="btn" type="submit">Browse Trainers</button>
            </form>
            @if (auth()->user()->location)
                <small class="muted">Searching near: {{ auth()->user()->location }}</small>
            @endif
        </div>
    </section>

    <section class="card">
        <h2>Find a Gym</h2>
        <p class="muted">Discover gyms nearby with facilities and services that match your needs.</p>
        <div style="display:flex;gap:10px;flex-direction:column">
            <form method="GET" action="{{ route('client.gyms') }}" class="form-grid">
                <label>Search gyms
                    <input name="q" placeholder="Gym name, location, services..." value="">
                </label>
                <button class="btn" type="submit">Browse Gyms</button>
            </form>
            @if (auth()->user()->location)
                <small class="muted">Searching near: {{ auth()->user()->location }}</small>
            @endif
        </div>
    </section>
</div>

@if ($nearbyTrainers->isNotEmpty())
    <section class="card" style="margin-bottom:20px">
        <h2>Trainers Near You</h2>
        <p class="muted">Recommended trainers in or near {{ auth()->user()->location ?: 'your area' }}</p>
        <div class="grid three">
            @foreach ($nearbyTrainers->take(6) as $trainer)
                <article class="card" style="box-shadow:none">
                    <div class="avatar" style="width:54px;height:54px;margin-bottom:12px">{{ strtoupper(substr($trainer->user->name, 0, 1)) }}</div>
                    <h3>{{ $trainer->user->name }}</h3>
                    <p><span class="badge green">{{ ucfirst($trainer->category) }}</span></p>
                    <p class="muted">{{ $trainer->specialty }}</p>
                    @if ($trainer->user->location)
                        <p><small>📍 {{ $trainer->user->location }}</small></p>
                    @endif
                    <p><strong>{{ $trainer->experience_years }}y exp</strong> • ⭐ {{ $trainer->rating }}</p>
                    <a class="btn" href="{{ route('client.trainers', ['q' => $trainer->user->name]) }}"><span>+</span> View</a>
                </article>
            @endforeach
        </div>
    </section>
@endif

@if ($nearbyGyms->isNotEmpty())
    <section class="card" style="margin-bottom:20px">
        <h2>Gyms Near You</h2>
        <p class="muted">Recommended gyms in or near {{ auth()->user()->location ?: 'your area' }}</p>
        <div class="grid three">
            @foreach ($nearbyGyms->take(6) as $gym)
                <article class="card" style="box-shadow:none">
                    <div class="avatar" style="width:54px;height:54px;margin-bottom:12px;border-radius:8px">{{ strtoupper(substr($gym->name, 0, 1)) }}</div>
                    <h3>{{ $gym->gym_name ?: $gym->name }}</h3>
                    <p class="muted">{{ $gym->gym_services ? substr($gym->gym_services, 0, 60) . '...' : 'Fitness facility' }}</p>
                    @if ($gym->location)
                        <p><small>📍 {{ $gym->location }}</small></p>
                    @endif
                    @if ($gym->nearby_locations)
                        <p class="muted"><small>Also near: {{ substr($gym->nearby_locations, 0, 40) }}...</small></p>
                    @endif
                    <a class="btn" href="{{ route('client.gyms', ['q' => $gym->gym_name ?: $gym->name]) }}"><span>+</span> View</a>
                </article>
            @endforeach
        </div>
    </section>
@endif

<section class="card">
    <h2>Choose Your Package</h2>
    <p class="muted">Pick a membership plan that fits your budget and fitness needs.</p>
    <a class="btn" href="{{ route('client.packages') }}"><span>+</span> Browse Packages</a>
</section>
@endsection
