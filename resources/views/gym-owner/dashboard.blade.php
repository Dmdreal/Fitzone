@extends('layouts.app')

@section('title', 'Gym Owner Dashboard - Fitzone')

@section('content')
<h1>Gym Owner Dashboard</h1>

<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">C</div><div><small>Clients</small><strong>{{ $clientCount }}</strong><span class="up">Registered clients</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">T</div><div><small>Trainers</small><strong>{{ $trainerCount }}</strong><span class="up">Discoverable trainers</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">M</div><div><small>Active Memberships</small><strong>{{ $activeMemberships }}</strong><span class="up">Currently active</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ede9fe;color:var(--violet)">P</div><div><small>Paid Payments</small><strong>{{ $paidPayments }}</strong><span class="up">Verified payments</span></div></article>
</section>

<div class="grid two">
    <section class="card">
        <h2>{{ $owner->gym_name ?: 'Your Gym' }}</h2>
        <p class="muted">{{ $owner->headline ?: 'Manage your gym presence and discovery details.' }}</p>
        <div class="table-scroll">
            <table>
                <tbody>
                    <tr><th>Verification</th><td><span class="badge amber">{{ ucfirst($owner->verification_status ?? 'pending') }}</span></td></tr>
                    <tr><th>Location</th><td>{{ $owner->location ?: 'Not listed' }}</td></tr>
                    <tr><th>Nearby Areas</th><td>{{ $owner->nearby_locations ?: 'Not listed' }}</td></tr>
                    <tr><th>Services</th><td>{{ $owner->gym_services ?: 'Not listed' }}</td></tr>
                    <tr><th>Phone</th><td>{{ $owner->phone ?: 'Not listed' }}</td></tr>
                </tbody>
            </table>
        </div>
        <div class="actions" style="justify-content:flex-start">
            <a class="btn" href="{{ route('profile.edit') }}">Update Gym Profile</a>
        </div>
    </section>

    <section class="card">
        <h2>Core Features</h2>
        <div class="grid">
            <div class="step-chip" style="background:#eff6ff;color:var(--blue)"><span class="step-icon" style="background:#dbeafe;color:var(--blue)">S</span> Search and location discovery</div>
            <div class="step-chip" style="background:#f0fdf4;color:var(--green)"><span class="step-icon" style="background:#dcfce7;color:var(--green)">B</span> Booking, reviews, and complaints ready</div>
            <div class="step-chip" style="background:#fff7ed;color:var(--amber)"><span class="step-icon" style="background:#ffedd5;color:var(--amber)">V</span> Payments and verification workflow</div>
        </div>
    </section>
</div>

<section class="card">
    <h2>Recently Added Trainers</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Name</th><th>Specialty</th><th>Location</th><th>Nearby Areas</th></tr></thead>
            <tbody>
                @forelse ($recentTrainers as $trainer)
                    <tr>
                        <td>{{ $trainer->user->name }}</td>
                        <td>{{ $trainer->specialty }}</td>
                        <td>{{ $trainer->user->location ?: 'Not listed' }}</td>
                        <td>{{ $trainer->user->nearby_locations ?: 'Not listed' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No trainers have registered yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
