@extends('layouts.app')

@section('title', 'Choose Package - Fitzone')

@section('content')
<h1>Choose Your Package</h1>
<section class="card" style="margin-bottom:16px">
    <h2>Membership Packages</h2>
    <p class="muted">Pick the access level that fits your schedule. Trainer access and diet plans unlock based on package level.</p>
    <div class="plans">
        @foreach ($packages as $package)
            <article class="plan {{ $package->slug === 'monthly-plan' ? 'featured' : '' }}">
                <h2>{{ $package->name }}</h2>
                <div class="price">KES {{ number_format($package->price) }}</div>
                <p class="muted">{{ $package->duration_count }} {{ $package->duration_unit }} access • {{ ucfirst($package->access_level) }}</p>
                <p>{{ $package->trainer_access ? 'Trainer access included' : 'Self-guided access' }}</p>
                @foreach (($package->benefits ?? []) as $benefit)
                    <span>✓ {{ $benefit }}</span>
                @endforeach
                <a class="btn" href="{{ route('member.trainers', ['package' => $package->id]) }}">Choose Plan</a>
            </article>
        @endforeach
    </div>
</section>
@endsection
