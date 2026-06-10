@extends('layouts.app')

@section('title', 'Search Members - Fitzone')

@section('content')
<h1>Search Members</h1>

<section class="card" style="margin-bottom:16px">
    <h2>Find a Member</h2>
    <form method="GET" action="{{ route('member-search.index') }}" class="form-grid">
        <label>Search
            <input name="q" value="{{ $query }}" placeholder="Name, email, phone, or GYM-0001" autofocus>
        </label>
        <label style="align-self:end">
            <button class="btn" type="submit">Search</button>
        </label>
    </form>
</section>

<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px;flex-wrap:wrap">
        <h2>Results</h2>
        @if ($query !== '')
            <span class="badge green">{{ $members->count() }} found</span>
        @endif
    </div>

    @if ($query === '')
        <p class="muted">Type a member name, email, phone, or member number to search.</p>
    @else
        <div class="table-scroll">
            <table>
                <thead><tr><th>Member</th><th>Member No.</th><th>Package</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse ($members as $member)
                        @php $membership = $member->memberships->first(); @endphp
                        <tr>
                            <td>
                                <div style="display:flex;gap:10px;align-items:center">
                                    @if ($member->profile_photo_url)
                                        <img class="message-avatar image" src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}">
                                    @else
                                        <span class="message-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                    @endif
                                    <span>
                                        <strong>{{ $member->name }}</strong><br>
                                        <small class="muted">{{ $member->email }}</small>
                                    </span>
                                </div>
                            </td>
                            <td>{{ $member->member_number ?? '-' }}</td>
                            <td>{{ $membership?->package?->name ?? 'No package' }}</td>
                            <td><span class="badge {{ $member->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($member->status) }}</span></td>
                            <td>
                                @if ($member->id === auth()->id())
                                    <span class="muted">You</span>
                                @else
                                    <form method="POST" action="{{ route('member-search.chat', $member) }}">
                                        @csrf
                                        <button class="btn ghost" type="submit">Chat</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No members matched "{{ $query }}".</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
