@extends('layouts.app')

@section('title', 'Choose Package - Fitzone')

@section('content')
<h1>Choose Your Package</h1>
<section class="card">
    <h2>Membership Packages</h2>
    <p class="muted">Pick your access level. You can continue with or without a trainer depending on your plan.</p>
    @if(isset($trainer) || isset($gym))
        <section class="card" style="margin-bottom:18px; background:#f8fafc; border-color:#c7d2fe;">
            <h3>Selected provider</h3>
                <p class="muted">You selected {{ $trainer ? 'trainer '.$trainer->user->name : ('gym '.($gym->gym_name ?: $gym->name)) }}.</p>
            @if($recommendedPackage)
                <p><strong>Recommended package:</strong> {{ $recommendedPackage->name }} at KES {{ number_format($recommendedPackage->price, 2) }}</p>
                <a class="btn" href="{{ route('client.checkout', ['package' => $recommendedPackage->id, 'trainer' => $trainer?->id, 'gym' => $gym?->id]) }}">Continue with recommended package</a>
            @elseif(isset($trainer))
                <p class="muted">This trainer has not set a preferred package yet. Choose any available plan below.</p>
            @else
                <p class="muted">This gym has not set a preferred package yet. Choose any plan below.</p>
            @endif
        </section>
    @endif
    <div class="plans">
        @foreach ($packages as $package)
            <article class="plan {{ $package->slug === 'monthly-plan' ? 'featured' : '' }}">
                <div class="soft-icon">{{ strtoupper(substr($package->name, 0, 1)) }}</div>
                <h2>{{ $package->name }}</h2>
                <div class="price">KES {{ number_format($package->price) }}</div>
                <p class="muted">{{ $package->duration_count }} {{ $package->duration_unit }} access - {{ ucfirst($package->access_level) }}</p>
                <p>{{ $package->trainer_access ? 'Trainer access included' : 'Self-guided access' }}</p>
                @foreach (($package->benefits ?? []) as $benefit)
                    <span>+ {{ $benefit }}</span>
                @endforeach
                <a class="btn" href="{{ route('client.trainers', ['package' => $package->id]) }}"><span>+</span> Choose Plan</a>
            </article>
        @endforeach
    </div>
</section>
@endsection
