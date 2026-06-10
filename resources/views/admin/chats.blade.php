@extends('layouts.app')

@section('title', 'All Chats - Fitzone')

@section('content')
<h1>All Chats</h1>

<div class="chat-shell">
    <aside class="chat-room">
        @forelse ($chats as $chat)
            <a class="chat-tab {{ $activeChat?->id === $chat->id ? 'active' : '' }}" href="{{ route('admin.chats', ['chat' => $chat->id]) }}">
                <span class="soft-icon">{{ strtoupper(substr($chat->type, 0, 1)) }}</span>
                <span>
                    <strong>{{ $chat->title }}</strong><br>
                    <small class="muted">
                        {{ str_replace('_', ' ', ucfirst($chat->type)) }}
                        @if ($chat->member) - {{ $chat->member->name }} @endif
                    </small>
                </span>
            </a>
        @empty
            <section class="card">No chats have been created yet.</section>
        @endforelse
    </aside>

    <section class="card">
        @if ($activeChat)
            <h2>{{ $activeChat->title }}</h2>
            <p class="muted">Admin view of this conversation. Messages sent here appear inside the same room.</p>
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
            <form class="composer" method="POST" action="{{ route('admin.chats.messages', $activeChat) }}">
                @csrf
                <input name="body" placeholder="Type as admin..." required maxlength="1000">
                <button class="btn" type="submit">Send</button>
            </form>
        @else
            <h2>No chat selected</h2>
        @endif
    </section>
</div>
@endsection
