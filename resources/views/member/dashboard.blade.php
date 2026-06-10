@extends('layouts.app')

@section('title', 'Client Dashboard - Fitzone')

@section('content')
@php
    $membership = auth()->user()->memberships()->with(['package', 'trainer'])->latest()->first();
@endphp

<h1>Welcome to Fitzone</h1>

<section class="card" style="margin-bottom:16px;display:grid;grid-template-columns:1.2fr .8fr;gap:18px;align-items:center">
    <div>
        <h2>Your fitness journey starts here</h2>
        <p class="muted">Choose a package, select a trainer, complete payment, then unlock workouts, attendance tracking, and diet guidance.</p>
        <div class="actions" style="justify-content:flex-start">
            <a class="btn" href="{{ route('member.packages') }}">Choose Package</a>
            <a class="btn ghost" href="{{ route('member.trainers') }}">Preview Trainers</a>
        </div>
    </div>
    <div class="flow">
        <div class="flow-box">1. Package</div><div class="connector"></div>
        <div class="flow-box">2. Trainer</div><div class="connector"></div>
        <div class="flow-box">3. Payment</div><div class="connector"></div>
        <div class="flow-box">4. Activation</div>
    </div>
</section>

<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">P</div><div><small>Membership</small><strong>{{ $membership?->package?->name ?? 'Not selected' }}</strong><span class="up">{{ $membership?->status ? ucfirst($membership->status) : 'Choose a package' }}</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">T</div><div><small>Trainer</small><strong>{{ $membership?->trainer?->name ?? 'Optional' }}</strong><span class="up">Select any time</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">D</div><div><small>Diet Plan</small><strong>{{ auth()->user()->dietPlans()->exists() ? 'Unlocked' : 'Locked' }}</strong><span class="up">After activation</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ede9fe;color:var(--violet)">W</div><div><small>Workout Plan</small><strong>{{ auth()->user()->workoutPlans()->exists() ? 'Ready' : 'Pending' }}</strong><span class="up">Trainer assigned</span></div></article>
</section>

<div class="grid two">
    <section class="card">
        <h2>Client Benefits</h2>
        <table>
            <tbody>
                <tr><td>Active membership</td><td>Workout, diet, attendance access</td></tr>
                <tr><td>Trainer selection</td><td>Strength, aerobics, personal, wellness</td></tr>
                <tr><td>Payment receipt</td><td>M-Pesa, card, bank, or cash support</td></tr>
                <tr><td>Progress tracking</td><td>Weight notes and attendance history</td></tr>
            </tbody>
        </table>
    </section>
    <section class="card">
        <h2>Activation Status</h2>
        @if ($membership)
            <p><span class="badge green">Active</span> {{ $membership->package->name }} ends {{ $membership->ends_at->format('d M Y') }}</p>
            <a class="btn" href="{{ route('member.activation') }}">View Activation</a>
        @else
            <p class="muted">No active package yet. Start by choosing a client package.</p>
            <a class="btn" href="{{ route('member.packages') }}">Start Now</a>
        @endif
    </section>
</div>
@endsection
