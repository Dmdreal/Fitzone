@extends('layouts.app')

@section('title', 'Admin Dashboard - Fitzone')

@section('content')
<h1>Admin Dashboard</h1>
<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">M</div><div><small>Members</small><strong>{{ $memberCount }}</strong><span class="up">Registered clients</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">T</div><div><small>Trainers</small><strong>{{ $trainerCount }}</strong><span class="up">Can be assigned</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">KES</div><div><small>This Month</small><strong>{{ number_format($monthlyIncome) }}</strong><span class="up">Paid payments</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ede9fe;color:var(--violet)">A</div><div><small>Active Plans</small><strong>{{ $activeMemberships }}</strong><span class="up">Current memberships</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">O</div><div><small>Pending Orders</small><strong>{{ $pendingOrders }}</strong><span class="up">Café queue</span></div></article>
    <article class="card stat"><div class="icon" style="background:#fee2e2;color:var(--red)">I</div><div><small>Low Stock</small><strong>{{ $lowStockProducts }}</strong><span class="up">Inventory alerts</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">POS</div><div><small>Café Revenue</small><strong>{{ number_format($cafeRevenue) }}</strong><span class="up">Completed this month</span></div></article>
</section>

<div class="grid two">
    <section class="card">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
            <h2>Recent Members</h2>
            <a class="btn ghost" href="{{ route('admin.users') }}">Manage Users</a>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Name</th><th>Package</th><th>Trainer</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($recentMembers as $member)
                        @php $membership = $member->memberships->first(); @endphp
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $membership?->package?->name ?? 'No plan' }}</td>
                            <td>{{ $membership?->trainer?->name ?? 'Not assigned' }}</td>
                            <td><span class="badge {{ $member->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($member->status) }}</span></td>
                            <td><a class="btn ghost" href="{{ route('admin.members.show', $member) }}">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No members yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="card">
        <h2>Recent Payments</h2>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Member</th><th>Plan</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($recentPayments as $payment)
                        <tr>
                            <td>{{ $payment->member->name }}</td>
                            <td>{{ $payment->membership?->package?->name ?? 'Membership' }}</td>
                            <td>KES {{ number_format($payment->amount) }}</td>
                            <td><span class="badge {{ $payment->status === 'paid' ? 'green' : 'amber' }}">{{ ucfirst($payment->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<section class="card" style="margin-bottom:16px">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <h2>Recent Café Orders</h2>
        <a class="btn ghost" href="{{ route('admin.orders') }}">View Orders</a>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Order</th><th>Client</th><th>Status</th><th>Total</th><th></th></tr></thead>
            <tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->member->name }}</td>
                        <td><span class="badge {{ $order->status === 'completed' ? 'green' : ($order->status === 'cancelled' ? 'red' : 'amber') }}">{{ ucfirst($order->status) }}</span></td>
                        <td>KES {{ number_format($order->total_amount, 2) }}</td>
                        <td><a class="btn ghost" href="{{ route('cafe.dashboard') }}">POS</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">No café orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <h2>Call Activity</h2>
        <a class="btn ghost" href="{{ route('admin.chats') }}">Open Chats</a>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Client</th><th>Trainer</th><th>Status</th><th>Started</th><th></th></tr></thead>
            <tbody>
                @forelse ($recentCalls as $call)
                    <tr>
                        <td>{{ $call->caller->name }}</td>
                        <td>{{ $call->trainer->name }}</td>
                        <td><span class="badge {{ $call->status === 'accepted' ? 'green' : ($call->status === 'ringing' ? 'amber' : 'red') }}">{{ ucfirst($call->status) }}</span></td>
                        <td>{{ $call->created_at->diffForHumans() }}</td>
                        <td><a class="btn ghost" href="{{ route('calls.show', $call) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">No call activity yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
