@extends('layouts.app')

@section('title', 'Payment Approvals - Fitzone')

@section('content')
<h1>Payment Approvals</h1>
<section class="card">
    <h2>Assigned Client Payments</h2>
    <p class="muted">Approve only when the payment has been confirmed. Members remain locked while status is pending.</p>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Client</th><th>Plan</th><th>Method</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->member?->name ?? 'Unknown' }}</td>
                        <td>{{ $payment->membership?->package?->name ?? 'Membership' }}</td>
                        <td>{{ strtoupper($payment->method) }}</td>
                        <td>KES {{ number_format($payment->amount, 2) }}</td>
                        <td><span class="badge {{ $payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : 'amber') }}">{{ ucfirst($payment->status) }}</span></td>
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
                    <tr><td colspan="6">No assigned payment requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
