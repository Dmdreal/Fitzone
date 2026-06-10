@extends('layouts.app')

@section('title', 'Wallet - Fitzone')

@section('content')
<h1>Wallet</h1>

@if ($errors->any())
    <section class="card" style="margin-bottom:16px;border-color:#fecaca;background:#fef2f2;box-shadow:none">
        <strong style="color:#991b1b">Top up needs attention</strong>
        <ul style="margin:8px 0 0;color:#991b1b">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </section>
@endif

<section class="grid two">
    <article class="card stat">
        <div class="icon" style="background:#dcfce7;color:var(--green)">KES</div>
        <div>
            <small>Available Balance</small>
            <strong>{{ number_format($wallet->balance, 2) }}</strong>
            <span class="up">Used for café purchases</span>
        </div>
    </article>
    <section class="card">
        <h2>Top Up with M-PESA</h2>
        <form method="POST" action="{{ route('mpesa.stkpush') }}" class="form-grid">
            @csrf
            <input type="hidden" name="payment_context" value="cafe_wallet">
            <label>Amount
                <input type="number" name="amount" min="1" step="1" value="{{ old('amount') }}" placeholder="1000" required>
            </label>
            <label>M-PESA Phone Number
                <input name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="0712345678 or 254712345678" required>
            </label>
            <label style="align-self:end">
                <button class="btn" type="submit">Send STK Prompt</button>
            </label>
        </form>
        <p class="muted" style="margin-top:10px">The balance updates after Safaricom confirms payment to the cafÃ© till.</p>
    </section>
</section>

<section class="card">
    <h2>M-PESA Top Ups</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Reference</th><th>Status</th><th>Amount</th><th>Receipt</th><th>Date</th></tr></thead>
            <tbody>
                @forelse ($topUps as $topUp)
                    <tr>
                        <td>{{ $topUp->reference }}</td>
                        <td><span class="badge {{ $topUp->status === 'paid' ? 'green' : ($topUp->status === 'failed' ? 'red' : 'amber') }}">{{ ucfirst($topUp->status) }}</span></td>
                        <td>KES {{ number_format($topUp->amount, 2) }}</td>
                        <td>{{ $topUp->receipt ?? '-' }}</td>
                        <td>{{ $topUp->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No M-PESA wallet top ups yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>Café Wallet Activity</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Order</th><th>Status</th><th>Items</th><th>Total</th><th>Date</th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td><span class="badge {{ $order->status === 'completed' ? 'green' : ($order->status === 'cancelled' ? 'red' : 'amber') }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->items->sum('quantity') }}</td>
                        <td>KES {{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No café orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
