@extends('layouts.app')

@section('title', 'Checkout - Fitzone')

@section('content')
<h1>Payment Checkout</h1>
<div class="grid two">
    <section class="card">
        <h2>Order Summary</h2>
        <table>
            <tbody>
                <tr><td>Package</td><td>{{ $package->name }}</td></tr>
                <tr><td>Duration</td><td>{{ $package->duration_count }} {{ $package->duration_unit }}</td></tr>
                <tr><td>Trainer</td><td>{{ $trainer?->user?->name ?? 'No trainer selected' }}</td></tr>
                <tr><td>Total</td><td><strong>KES {{ number_format($package->price) }}</strong></td></tr>
            </tbody>
        </table>
    </section>
    <section class="card">
        <h2>Payment Method</h2>
        <form method="POST" action="{{ route('member.activate') }}">
            @csrf
            <input type="hidden" name="package_id" value="{{ $package->id }}">
            @if ($trainer)
                <input type="hidden" name="trainer_id" value="{{ $trainer->id }}">
            @endif
            <label>Choose payment method
                <select name="method">
                    <option value="mpesa">M-Pesa</option>
                    <option value="card">Card</option>
                    <option value="bank">Bank</option>
                    <option value="cash">Cash</option>
                </select>
            </label>
            <p class="muted">For this step, payment is approved instantly so we can build and test the client activation flow.</p>
            <div class="actions">
                <a class="btn ghost" href="{{ route('member.packages') }}">Back</a>
                <button class="btn" type="submit">Pay & Activate</button>
            </div>
        </form>
    </section>
</div>
@endsection
