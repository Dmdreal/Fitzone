@extends('layouts.app')

@section('title', 'Members Directory - Fitzone')

@section('content')
<h1>Members Directory</h1>

<section class="card" style="margin-bottom:16px">
    <h2>Choose a package section</h2>
    <p class="muted">Browse members by package. The weekly section shows everyone currently active on the Weekly Plan.</p>
    <div class="actions" style="justify-content:flex-start;flex-wrap:wrap">
        @foreach ($packages as $section)
            <a class="btn {{ $package->id === $section->id ? '' : 'ghost' }}" href="{{ route('client.members', ['package' => $section->slug]) }}">
                {{ $section->name }}
            </a>
        @endforeach
    </div>
</section>

<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <div>
            <h2>{{ $package->name }} Members</h2>
            <p class="muted">Pick someone from this section and open a private member chat.</p>
        </div>
        <span class="badge green">{{ $members->count() }} active</span>
    </div>

    @if ($members->isNotEmpty())
        <div class="grid three">
            @foreach ($members as $member)
                @php $memberMembership = $member->memberships->first(); @endphp
                <article class="card" style="box-shadow:none">
                    <div class="avatar" style="width:54px;height:54px;margin-bottom:12px">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                    <h2>{{ $member->name }}</h2>
                    <p><span class="badge green">Active {{ $package->name }}</span></p>
                    <p class="muted">Joined {{ $memberMembership?->starts_at?->format('d M Y') ?? 'recently' }}</p>
                    <form method="POST" action="{{ route('client.members.chat', ['member' => $member->id]) }}">
                        @csrf
                        <button class="btn" type="submit">Chat Member</button>
                    </form>
                </article>
            @endforeach
        </div>
    @else
        <p class="muted">No other active members are in this section yet.</p>
    @endif
</section>
@endsection
