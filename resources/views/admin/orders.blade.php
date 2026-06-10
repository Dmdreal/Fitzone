@extends('layouts.app')

@section('title', 'Orders - Fitzone')

@section('content')
<h1>Café Orders</h1>

<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">KES</div><div><small>Completed Revenue</small><strong>{{ number_format($revenue, 2) }}</strong><span class="up">Café POS</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">O</div><div><small>Total Orders</small><strong>{{ $orders->count() }}</strong><span class="up">All statuses</span></div></article>
</section>

<section class="card">
    <h2>All Orders</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Order</th><th>Member</th><th>Items</th><th>Total</th><th>Status</th><th>Handled By</th><th>Date</th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->member->name }}<br><small class="muted">{{ $order->member->member_number }}</small></td>
                        <td>{{ $order->items->map(fn ($item) => $item->quantity.'x '.$item->product->name)->join(', ') }}</td>
                        <td>KES {{ number_format($order->total_amount, 2) }}</td>
                        <td><span class="badge {{ $order->status === 'completed' ? 'green' : ($order->status === 'cancelled' ? 'red' : 'amber') }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->handler?->name ?? '-' }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
