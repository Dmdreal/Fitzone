@extends('layouts.app')

@section('title', 'Trainer Dashboard - Fitzone')

@section('content')
<h1>Trainer Dashboard</h1>
<section class="grid stats">
    <article class="card stat"><div class="icon" style="background:#dbeafe;color:var(--blue)">M</div><div><small>Assigned Members</small><strong>{{ $memberships->count() }}</strong><span class="up">Clients who chose you</span></div></article>
    <article class="card stat"><div class="icon" style="background:#dcfce7;color:var(--green)">C</div><div><small>Direct Chats</small><strong>{{ $chatCount }}</strong><span class="up">Reply anytime</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ffedd5;color:var(--amber)">R</div><div><small>Ringing Calls</small><strong>{{ $pendingCalls->count() }}</strong><span class="up">Awaiting response</span></div></article>
    <article class="card stat"><div class="icon" style="background:#ede9fe;color:var(--violet)">A</div><div><small>Attendance</small><strong>{{ $memberships->sum(fn ($m) => $m->member->attendances->where('status', 'present')->count()) }}</strong><span class="up">Client records</span></div></article>
</section>

@if ($pendingCalls->isNotEmpty())
    <section class="card" style="margin-bottom:16px;border-color:#fbbf24">
        <h2>Incoming Calls</h2>
        @foreach ($pendingCalls as $call)
            <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:10px">
                <p><strong>{{ $call->caller->name }}</strong> is calling you.</p>
                <div style="display:flex;gap:8px">
                    <form method="POST" action="{{ route('trainer.calls.accept', $call) }}">@csrf<button class="btn" type="submit">Accept</button></form>
                    <form method="POST" action="{{ route('trainer.calls.decline', $call) }}">@csrf<button class="btn ghost" type="submit">Decline</button></form>
                </div>
            </div>
        @endforeach
    </section>
@endif

@if ($adminMessages->isNotEmpty())
    <section class="card" style="margin-bottom:16px">
        <h2>Admin Messages</h2>
        @foreach ($adminMessages as $message)
            <article class="message" style="width:100%;margin-bottom:10px">
                <div class="message-head">
                    @if ($message->admin->profile_photo_url)
                        <img class="message-avatar image" src="{{ $message->admin->profile_photo_url }}" alt="{{ $message->admin->name }}">
                    @else
                        <span class="message-avatar">{{ strtoupper(substr($message->admin->name, 0, 1)) }}</span>
                    @endif
                    <span class="message-meta">
                        <strong>{{ $message->admin->name }}</strong>
                        <small>Admin - {{ $message->created_at->diffForHumans() }}</small>
                    </span>
                </div>
                <div class="message-body">{{ $message->body }}</div>
            </article>
        @endforeach
    </section>
@endif

<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <h2>Clients Who Chose You</h2>
        <a class="btn ghost" href="{{ route('trainer.chat') }}">Open Chat</a>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Name</th><th>Package</th><th>Membership</th><th>Attendance</th><th>Action</th></tr></thead>
            <tbody>
                @forelse ($memberships as $membership)
                    <tr>
                        <td>{{ $membership->member->name }}</td>
                        <td>{{ $membership->package->name }}</td>
                        <td>{{ $membership->starts_at->format('d M Y') }} - {{ $membership->ends_at->format('d M Y') }}</td>
                        <td>{{ $membership->member->attendances->where('status', 'present')->count() }} present</td>
                        <td><a class="btn ghost" href="{{ route('trainer.chat') }}">Reply</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">No client has selected you yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
