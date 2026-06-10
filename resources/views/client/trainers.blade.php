@extends('layouts.app')

@section('title', 'Select Trainer - Fitzone')

@section('content')
<h1>Select a Trainer</h1>
<section class="card">
    <h2>Trainer Preview</h2>
    <p class="muted">Preview available trainers. Select one during checkout, or continue without a trainer.</p>
    <div class="grid three">
        @foreach ($trainers as $trainer)
            <article class="card" style="box-shadow:none">
                <div class="avatar" style="width:54px;height:54px;margin-bottom:12px">{{ strtoupper(substr($trainer->user->name, 0, 1)) }}</div>
                <h2>{{ $trainer->user->name }}</h2>
                <p><span class="badge green">{{ ucfirst($trainer->category) }}</span></p>
                <p class="muted">{{ $trainer->specialty }}</p>
                <p>Rating {{ $trainer->rating }} - {{ $trainer->experience_years }} years experience</p>
                @if ($package)
                    <a class="btn" href="{{ route('client.checkout', ['package' => $package->id, 'trainer' => $trainer->id]) }}"><span>+</span> Select Trainer</a>
                @endif
            </article>
        @endforeach
    </div>
    <div class="actions" style="justify-content:flex-start">
        @if ($package)
            <a class="btn ghost" href="{{ route('client.checkout', ['package' => $package->id]) }}">Continue Without Trainer</a>
        @else
            <a class="btn" href="{{ route('client.packages') }}">Choose Package First</a>
        @endif
    </div>
</section>
@endsection
