@extends('layouts.app')

@section('title', 'Choose Package - Fitzone')

@section('content')
<h1>Choose Your Package</h1>
<section class="card">
    <h2>Membership Packages</h2>
    <p class="muted">Pick your access level. You can continue with or without a trainer depending on your plan.</p>
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
