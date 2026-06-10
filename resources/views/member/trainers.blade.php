@extends('layouts.app')

@section('title', 'Select Trainer - Fitzone')

@section('content')
<h1>Select a Trainer</h1>
<section class="card" style="margin-bottom:16px">
    <h2>Trainer Preview</h2>
    <p class="muted">Choose a trainer or continue without one. You can add a trainer later if your package supports it.</p>
    <div class="grid three">
        @foreach ($trainers as $trainer)
            <article class="card" style="box-shadow:none">
                <div class="avatar" style="width:54px;height:54px;margin-bottom:12px">{{ strtoupper(substr($trainer->user->name, 0, 1)) }}</div>
                <h2>{{ $trainer->user->name }}</h2>
                <p><span class="badge green">{{ ucfirst($trainer->category) }}</span></p>
                <p class="muted">{{ $trainer->specialty }}</p>
                <p>Rating {{ $trainer->rating }} • {{ $trainer->experience_years }} years</p>
                @if ($package)
                    <a class="btn" href="{{ route('member.checkout', ['package' => $package->id, 'trainer' => $trainer->id]) }}">Select Trainer</a>
                @endif
            </article>
        @endforeach
    </div>
    @if ($package)
        <div class="actions" style="justify-content:flex-start">
            <a class="btn ghost" href="{{ route('member.checkout', ['package' => $package->id]) }}">Continue Without Trainer</a>
        </div>
    @else
        <p class="muted">Choose a package first to continue into checkout.</p>
        <a class="btn" href="{{ route('member.packages') }}">Choose Package</a>
    @endif
</section>
@endsection
