@extends('layouts.app')

@section('title', 'Trainer Chat - Fitzone')

@section('content')
<h1>Trainer Chat</h1>

@if ($pendingCalls->isNotEmpty())
    <section class="card" style="margin-bottom:16px;border-color:#fbbf24">
        <h2>Incoming Calls</h2>
        @foreach ($pendingCalls as $call)
            <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:10px">
                <p><strong>{{ $call->caller->name }}</strong> wants to talk now.</p>
                <div style="display:flex;gap:8px">
                    <form method="POST" action="{{ route('trainer.calls.accept', $call) }}">@csrf<button class="btn" type="submit">Accept</button></form>
                    <form method="POST" action="{{ route('trainer.calls.decline', $call) }}">@csrf<button class="btn ghost" type="submit">Decline</button></form>
                </div>
            </div>
        @endforeach
    </section>
@endif

<div class="chat-shell">
    <aside class="chat-room">
        @forelse ($chats as $chat)
            <a class="chat-tab {{ $activeChat?->id === $chat->id ? 'active' : '' }}" href="{{ route('trainer.chat', ['chat' => $chat->id]) }}">
                <span class="soft-icon">M</span>
                <span><strong>{{ $chat->member->name }}</strong><br><small class="muted">{{ $chat->messages->count() }} messages</small></span>
            </a>
        @empty
            <section class="card">Clients will appear here after they select you and start a trainer chat.</section>
        @endforelse
    </aside>

    <section class="card">
        @if ($activeChat)
            <h2>{{ $activeChat->title }}</h2>
            <p class="muted">Direct conversation with {{ $activeChat->member->name }}.</p>
            <div class="message-list">
                @forelse ($activeChat->messages as $message)
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
                            <span class="message-meta"><strong>Fitzone</strong><small>System - Ready now</small></span>
                        </div>
                        <div class="message-body">No messages yet.</div>
                    </article>
                @endforelse
            </div>
            <form class="composer" method="POST" action="{{ route('trainer.chat.messages') }}">
                @csrf
                <input type="hidden" name="chat_id" value="{{ $activeChat->id }}">
                <input name="body" placeholder="Reply to client..." required maxlength="1000">
                <button class="btn" type="submit">Send</button>
            </form>
        @else
            <h2>No chat selected</h2>
        @endif
    </section>
</div>
@endsection
