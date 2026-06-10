@extends('layouts.app')

@section('title', 'Profile - Fitzone')

@section('content')
<h1>Profile</h1>

@if (session('status'))
    <section class="card" style="border-color:#86efac;margin-bottom:16px">{{ session('status') }}</section>
@endif

<div class="grid two">
    <section class="card">
        <h2>Your Profile</h2>
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:16px">
            @if ($user->profile_photo_url)
                <img class="profile-photo-xl" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
            @else
                <div class="profile-photo-xl fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
            <div>
                <h2 style="margin-bottom:4px">{{ $user->name }}</h2>
                <p class="muted">{{ ucfirst($user->role) }} - {{ $user->headline ?? 'No headline yet' }}</p>
                @if ($user->profile_photo_path)
                    <form method="POST" action="{{ route('profile.photo.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn ghost" type="submit">Remove Photo</button>
                    </form>
                @endif
            </div>
        </div>

        @if ($errors->any())
            <p class="muted" style="color:var(--red)">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <label>Name <input name="name" value="{{ old('name', $user->name) }}" required></label>
                <label>Email <input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
                <label>Phone <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+254..."></label>
                <label>Headline <input name="headline" value="{{ old('headline', $user->headline) }}" placeholder="Strength trainer, premium member..."></label>
            </div>
            <label style="margin-top:12px">Profile Photo <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/webp"></label>
            <label style="margin-top:12px">Bio <textarea name="bio" placeholder="Write something about yourself...">{{ old('bio', $user->bio) }}</textarea></label>
            <div class="form-grid" style="margin-top:12px">
                <label>New Password <input type="password" name="password" autocomplete="new-password"></label>
                <label>Confirm Password <input type="password" name="password_confirmation" autocomplete="new-password"></label>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Save Profile</button>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>How It Appears</h2>
        <article class="message" style="width:100%">
            <div class="message-head">
                @if ($user->profile_photo_url)
                    <img class="message-avatar image" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                @else
                    <span class="message-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
                <span class="message-meta">
                    <strong>{{ $user->name }}</strong>
                    <small>{{ ucfirst($user->role) }} - shown in chats</small>
                </span>
            </div>
            <div class="message-body">{{ $user->bio ?: 'Your bio and profile image help people recognize you, like on Telegram or LinkedIn.' }}</div>
        </article>

        @if ($user->role === 'member')
            <div style="height:16px"></div>
            <h2>Your Member QR</h2>
            <div style="display:grid;gap:14px">
                <img src="{{ route('members.qr.svg', $user) }}" alt="QR code for {{ $user->name }}" style="width:min(260px,100%);aspect-ratio:1;background:#fff;border:12px solid #fff;border-radius:8px;box-shadow:0 0 0 1px #e2e8f0">
                <div>
                    <p class="muted" style="overflow-wrap:anywhere;margin-bottom:10px">{{ route('members.qr.show', $user->qr_token) }}</p>
                    <p class="muted" style="margin-bottom:12px">Scan this with any phone camera to open your member details. Trainers can also use it for attendance.</p>
                    <div class="actions" style="justify-content:flex-start">
                        <a class="btn" href="{{ route('members.qr.svg', ['member' => $user, 'download' => 1]) }}">Download QR</a>
                        <a class="btn ghost" href="{{ route('members.qr.show', $user->qr_token) }}" target="_blank">Preview Card</a>
                    </div>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
