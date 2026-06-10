@extends('layouts.app')

@section('title', 'Café - Fitzone')

@section('content')
<h1>Smart Café</h1>

<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">KES</div><div><small>Wallet</small><strong>{{ number_format($wallet->balance, 2) }}</strong><span class="up">Auto-deducts after order</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">ID</div><div><small>Member Number</small><strong>{{ auth()->user()->member_number }}</strong><span class="up">Primary identifier</span></div></article>
</section>

<section class="card" style="margin-bottom:16px">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px;flex-wrap:wrap">
        <h2>Order Food & Drinks</h2>
        <a class="btn ghost" href="{{ route('client.wallet') }}">Top Up Wallet</a>
    </div>
    <form method="POST" action="{{ route('client.cafe.orders') }}">
        @csrf
        <div class="plans">
            @foreach ($products as $product)
                <article class="plan">
                    <div style="display:flex;justify-content:space-between;gap:10px">
                        <div>
                            <h3>{{ $product->name }}</h3>
                            <p class="muted" style="margin:0">{{ ucfirst($product->category) }}</p>
                        </div>
                        <strong>KES {{ number_format($product->price, 2) }}</strong>
                    </div>
                    <span class="badge {{ $product->stock_status === 'ok' ? 'green' : ($product->stock_status === 'low' ? 'amber' : 'red') }}">
                        {{ $product->stock_quantity }} in stock
                    </span>
                    <label>Qty
                        <input type="number" name="items[{{ $product->id }}]" min="0" max="{{ $product->stock_quantity }}" value="0" {{ $product->stock_quantity < 1 ? 'disabled' : '' }}>
                    </label>
                </article>
            @endforeach
        </div>
        <div class="actions">
            <button class="btn" type="submit">Send Order</button>
        </div>
    </form>
</section>

<section class="card">
    <h2>My Café Orders</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Order</th><th>Status</th><th>Total</th><th>Items</th><th>Time</th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td><span class="badge {{ $order->status === 'completed' ? 'green' : ($order->status === 'cancelled' ? 'red' : 'amber') }}">{{ ucfirst($order->status) }}</span></td>
                        <td>KES {{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->items->map(fn ($item) => $item->quantity.'x '.$item->product->name)->join(', ') }}</td>
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No café orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
