@extends('layouts.app')

@section('title', 'Payments - Fitzone')

@section('content')
<h1>Payments</h1>
<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <div>
            <h2>Payment Verification</h2>
            <p class="muted" style="margin:0">Approve only after the money is confirmed. Pending members stay locked.</p>
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Member</th><th>Plan</th><th>Method</th><th>Amount</th><th>Status</th><th>Reference</th><th>Action</th></tr></thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->member?->name ?? 'Unknown' }}</td>
                        <td>{{ $payment->membership?->package?->name ?? 'Membership' }}</td>
                        <td>{{ strtoupper($payment->method) }}</td>
                        <td>KES {{ number_format($payment->amount, 2) }}</td>
                        <td><span class="badge {{ $payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : 'amber') }}">{{ ucfirst($payment->status) }}</span></td>
                        <td>{{ $payment->receipt ?? $payment->reference ?? 'Pending' }}</td>
                        <td>
                            @if ($payment->status === 'pending')
                                <div style="display:flex;gap:8px;flex-wrap:wrap">
                                    <form method="POST" action="{{ route('payments.approve', $payment) }}">@csrf<button class="btn" type="submit">Approve</button></form>
                                    <form method="POST" action="{{ route('payments.reject', $payment) }}">@csrf<button class="btn ghost" type="submit">Reject</button></form>
                                </div>
                            @else
                                <span class="muted">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No payments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
