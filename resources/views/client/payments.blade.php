@extends('layouts.app')

@section('title', 'Payments - Fitzone')

@section('content')
<h1>Payments</h1>
<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <h2>Payment History</h2>
        <a class="btn" href="{{ route('client.packages') }}">Pay for Package</a>
    </div>
    @if ($payments->isNotEmpty())
        <div class="table-scroll">
            <table>
                <thead><tr><th>Date</th><th>Plan</th><th>Amount</th><th>Method</th><th>Status</th><th>Receipt</th></tr></thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td>{{ $payment->membership?->package?->name ?? 'Package payment' }}</td>
                            <td>KES {{ number_format($payment->amount) }}</td>
                            <td>{{ strtoupper($payment->method) }}</td>
                            <td><span class="badge {{ $payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : 'amber') }}">{{ ucfirst($payment->status) }}</span></td>
                            <td><button class="btn ghost" type="button">{{ $payment->receipt ?? $payment->reference ?? 'Receipt' }}</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="muted">No payments yet.</p>
    @endif
</section>
@endsection
