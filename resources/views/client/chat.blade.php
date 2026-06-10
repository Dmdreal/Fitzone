@extends('layouts.app')

@section('title', 'Client Chat - Fitzone')

@section('content')
@php
    $currentChat = match ($activeChat) {
        'trainer' => $trainerChat,
        'member' => $memberChat,
        default => $groupChat,
    };
@endphp

<h1>Client Chat</h1>

@if (! $membership)
    <section class="card">
        @if ($latestMembership?->status === 'expired')
            <h2>Your package days are over</h2>
            <p class="muted">Recharge your package to unlock member rooms, trainer chat, member chats, and calls again.</p>
            <a class="btn" href="{{ route('client.packages') }}">Recharge Package</a>
        @else
            <h2>Activate a package to start chatting</h2>
            <p class="muted">Member group rooms and trainer chats unlock after package activation.</p>
            <a class="btn" href="{{ route('client.packages') }}">Choose Package</a>
        @endif
    </section>
@else
    <div class="chat-shell">
        <aside class="chat-room">
            <a class="chat-tab {{ $activeChat === 'group' ? 'active' : '' }}" href="{{ route('client.chat', ['room' => 'group']) }}">
                <span class="soft-icon">G</span>
                <span><strong>{{ $membership->package->name }} group</strong><br><small class="muted">Chat with members on similar plans</small></span>
            </a>
            <a class="chat-tab {{ $activeChat === 'trainer' ? 'active' : '' }}" href="{{ route('client.chat', ['room' => 'trainer']) }}">
                <span class="soft-icon">T</span>
                <span><strong>{{ $membership->trainer?->name ?? 'No trainer selected' }}</strong><br><small class="muted">Direct trainer support</small></span>
            </a>
            <a class="chat-tab {{ $activeChat === 'member' ? 'active' : '' }}" href="{{ route('client.members') }}">
                <span class="soft-icon">M</span>
                <span><strong>Member chats</strong><br><small class="muted">Find weekly, monthly, or yearly members</small></span>
            </a>

            <section class="card" style="box-shadow:none">
                <h2>Choose another trainer</h2>
                <p class="muted">Switch trainers anytime. Your new trainer chat opens immediately.</p>
                <form method="POST" action="{{ route('client.trainer.switch') }}">
                    @csrf
                    <label>Trainer
                        <select name="trainer_profile_id">
                            @foreach ($trainers as $trainer)
                                <option value="{{ $trainer->id }}" @selected($membership->trainer_id === $trainer->user_id)>
                                    {{ $trainer->user->name }} - {{ ucfirst($trainer->category) }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <div class="actions">
                        <button class="btn" type="submit">Switch Trainer</button>
                    </div>
                </form>
            </section>
        </aside>

        <section class="card">
            <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
                <div>
                    <h2>{{ $currentChat?->title ?? 'No chat selected' }}</h2>
                    <p class="muted">
                        @if ($activeChat === 'trainer')
                            Private conversation with your selected trainer.
                        @elseif ($activeChat === 'member')
                            Private conversation with another member.
                        @else
                            Group conversation for members in your package level.
                        @endif
                    </p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;justify-content:flex-end">
                    @if ($activeChat === 'trainer' && $trainerChat)
                        @if ($activeCall)
                            <a class="btn" href="{{ route('calls.show', $activeCall) }}">{{ ucfirst($activeCall->status) }} Call</a>
                        @else
                            <form method="POST" action="{{ route('client.calls.store') }}">
                                @csrf
                                <button class="btn" type="submit">Call Trainer</button>
                            </form>
                        @endif
                    @endif
                    <span class="badge green">Live room</span>
                </div>
            </div>

            @if ($currentChat)
                <div class="message-list">
                    @forelse ($currentChat->messages as $message)
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
                            <div class="message-body">No messages yet. Start the conversation.</div>
                        </article>
                    @endforelse
                </div>

                <form class="composer" method="POST" action="{{ route('client.chat.messages') }}">
                    @csrf
                    <input type="hidden" name="chat_id" value="{{ $currentChat->id }}">
                    <input type="hidden" name="room" value="{{ $activeChat }}">
                    <input name="body" placeholder="Type a message..." required maxlength="1000">
                    <button class="btn" type="submit">Send</button>
                </form>
            @endif
        </section>
    </div>
@endif
@endsection
