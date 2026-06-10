@extends('layouts.app')

@section('title', 'Member Chat - Fitzone')

@section('content')
<h1>Direct Chat</h1>

<div class="chat-shell">
    <aside class="chat-room">
        <section class="card">
            <h2>{{ $otherUser?->name ?? 'Member' }}</h2>
            <p class="muted">{{ ucfirst($otherUser?->role ?? 'member') }} conversation</p>
            @if ($otherUser?->role === 'member')
                <p style="margin-top:12px"><span class="badge green">{{ $otherUser->member_number }}</span></p>
                <div class="actions" style="justify-content:flex-start">
                    <a class="btn ghost" href="{{ route('members.qr.show', $otherUser->qr_token) }}" target="_blank">View Card</a>
                </div>
            @endif
        </section>
        <a class="chat-tab" href="{{ route('member-search.index') }}">
            <span class="soft-icon">S</span>
            <span><strong>Search Members</strong><br><small class="muted">Find someone else to message</small></span>
        </a>
    </aside>

    <section class="card">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px;flex-wrap:wrap">
            <div>
                <h2>{{ $chat->title }}</h2>
                <p class="muted">Private direct conversation.</p>
            </div>
            <span class="badge green">Direct</span>
        </div>

        <div class="message-list">
            @forelse ($chat->messages as $message)
                @php
                    $isMine = $message->sender_id === auth()->id();
                    $senderName = $message->sender?->name ?? 'Deleted user';
                    $senderRole = $message->sender?->role ? ucfirst($message->sender->role) : 'User';
                @endphp
                <article class="message {{ $isMine ? 'mine' : '' }}">
                    <div class="message-head">
                        @if ($message->sender?->profile_photo_url)
                            <img class="message-avatar image" src="{{ $message->sender->profile_photo_url }}" alt="{{ $senderName }}">
                        @else
                            <span class="message-avatar">{{ strtoupper(substr($senderName, 0, 1)) }}</span>
                        @endif
                        <span class="message-meta">
                            <strong>{{ $isMine ? 'You' : $senderName }}</strong>
                            <small>{{ $senderRole }} - {{ $message->created_at->diffForHumans() }}</small>
                        </span>
                    </div>
                    <div class="message-body">{{ $message->body }}</div>
                </article>
            @empty
                <article class="message">
                    <div class="message-head">
                        <span class="message-avatar">F</span>
                        <span class="message-meta"><strong>Fitzone</strong><small>System</small></span>
                    </div>
                    <div class="message-body">No messages yet. Start the conversation.</div>
                </article>
            @endforelse
        </div>

        <form class="composer" method="POST" action="{{ route('member-chats.messages', $chat) }}">
            @csrf
            <input name="body" placeholder="Type a message..." required maxlength="1000">
            <button class="btn" type="submit">Send</button>
        </form>
    </section>
</div>
@endsection
