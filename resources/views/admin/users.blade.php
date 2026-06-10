@extends('layouts.app')

@section('title', 'Manage Users - Fitzone')

@section('content')
<h1>Manage Users</h1>

@if (session('status'))
    <section class="card" style="border-color:#86efac;margin-bottom:16px">{{ session('status') }}</section>
@endif

<div class="grid two">
    <section class="card">
        <h2>Add Trainer</h2>
        <p class="muted">Trainer accounts are now managed from the dedicated Trainers page.</p>
        <a class="btn" href="{{ route('admin.trainers') }}">Open Trainers</a>
    </section>

    <section class="card">
        <h2>Admin Control</h2>
        <p class="muted">Admins can see clients, memberships, payments, attendance, diet and workout records, trainer assignments, chats, and call activity.</p>
        <p><span class="badge green">Trainer</span> Can see clients who selected them and reply in direct chat.</p>
        <p><span class="badge amber">Client</span> Can select a trainer, chat, and request a voice call.</p>
    </section>
</div>

<section class="card">
    <h2>All Users</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Package</th><th>Trainer</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                    @php $membership = $user->memberships->first(); @endphp
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $membership?->package?->name ?? ($user->trainerProfile?->specialty ?? '-') }}</td>
                        <td>{{ $membership?->trainer?->name ?? '-' }}</td>
                        <td><span class="badge {{ $user->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($user->status) }}</span></td>
                        <td>
                            @if ($user->role === 'member')
                                <a class="btn ghost" href="{{ route('admin.members.show', $user) }}">Details</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
