@extends('layouts.app')

@section('title', 'Café Orders - Fitzone')

@section('content')
<h1>Café Dashboard</h1>

<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">!</div><div><small>Pending Orders</small><strong>{{ $pendingCount }}</strong><span class="up">Refreshes automatically</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">POS</div><div><small>Live Queue</small><strong>{{ $orders->count() }}</strong><span class="up">Accept, prepare, complete</span></div></article>
</section>

<section class="card" style="margin-bottom:16px">
    <h2>Live Orders</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Order</th><th>Client</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->member->name }}<br><small class="muted">{{ $order->member->member_number }}</small></td>
                        <td>{{ $order->items->map(fn ($item) => $item->quantity.'x '.$item->product->name)->join(', ') }}</td>
                        <td>KES {{ number_format($order->total_amount, 2) }}</td>
                        <td><span class="badge amber">{{ ucfirst($order->status) }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('cafe.orders.update', $order) }}" style="display:grid;gap:8px">
                                @csrf
                                <select name="status">
                                    <option value="accepted">Accept</option>
                                    <option value="preparing">Preparing</option>
                                    <option value="completed">Complete</option>
                                    <option value="cancelled">Cancel</option>
                                </select>
                                <button class="btn" type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No active café orders.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>Recent Orders</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Order</th><th>Client</th><th>Status</th><th>Total</th><th>Time</th></tr></thead>
            <tbody>
                @foreach ($recentOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->member->name }}</td>
                        <td><span class="badge {{ $order->status === 'completed' ? 'green' : ($order->status === 'cancelled' ? 'red' : 'amber') }}">{{ ucfirst($order->status) }}</span></td>
                        <td>KES {{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<script>
    const pendingCount = {{ $pendingCount }};
    if (pendingCount > 0 && !sessionStorage.getItem('fitzoneCafeAlerted'+pendingCount)) {
        const audio = new Audio('data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAESsAACJWAAACABAAZGF0YQAAAAA=');
        audio.play().catch(() => {});
        if ('speechSynthesis' in window) {
            speechSynthesis.speak(new SpeechSynthesisUtterance('New cafe order received'));
        }
        sessionStorage.setItem('fitzoneCafeAlerted'+pendingCount, '1');
    }
    setTimeout(() => window.location.reload(), 15000);
</script>
@endsection
