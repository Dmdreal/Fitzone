@extends('layouts.app')

@section('title', 'Manage Trainers - Fitzone')

@section('content')
<h1>Manage Trainers</h1>

@if (session('status'))
    <section class="card" style="border-color:#86efac;margin-bottom:16px">{{ session('status') }}</section>
@endif

<div class="grid two">
    <section class="card">
        <h2>Add Trainer Login</h2>
        <p class="muted">The email and password added here become the trainer login. After login, the trainer opens their own dashboard automatically.</p>
        <form method="POST" action="{{ route('admin.trainers.store') }}">
            @csrf
            <div class="form-grid">
                <label>Name <input name="name" value="{{ old('name') }}" required></label>
                <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
                <label>Password <input type="password" name="password" required minlength="8"></label>
                <label>Specialty <input name="specialty" value="{{ old('specialty') }}" required></label>
                <label>Category <input name="category" value="{{ old('category', 'strength') }}" required></label>
                <label>Experience Years <input type="number" name="experience_years" value="{{ old('experience_years', 1) }}" min="0" max="60" required></label>
                <label>County
                    <select name="county_id">
                        <option value="">Select county</option>
                        @foreach(\App\Models\County::orderBy('name')->get() as $county)
                            <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>{{ $county->display_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Town <input name="town" value="{{ old('town') }}"></label>
                <label>Latitude <input name="latitude" value="{{ old('latitude') }}"></label>
                <label>Longitude <input name="longitude" value="{{ old('longitude') }}"></label>
                <label>Rating <input type="number" name="rating" value="{{ old('rating', 5) }}" min="1" max="5" step="0.1"></label>
            </div>
            <label style="margin-top:12px">Bio <textarea name="bio">{{ old('bio') }}</textarea></label>
            @if ($errors->any())
                <p class="muted" style="color:var(--red)">{{ $errors->first() }}</p>
            @endif
            <div class="actions"><button class="btn" type="submit">Add Trainer</button></div>
        </form>
    </section>

    <section class="card">
        <h2>Trainer Login Rule</h2>
        <p><span class="badge green">Checked</span> Laravel checks the email and password against the users table.</p>
        <p class="muted">If that user has role <strong>trainer</strong>, they are sent to the trainer dashboard. If the trainer is removed, that login no longer works.</p>
    </section>
</div>

<section class="card">
    <h2>Trainer Records</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Specialty</th><th>Clients</th><th>Status</th><th>Message / Remove</th></tr></thead>
            <tbody>
                @forelse ($trainers as $trainer)
                    <tr>
                        <td>{{ $trainer->name }}</td>
                        <td>{{ $trainer->email }}</td>
                        <td>{{ $trainer->trainerProfile?->specialty ?? '-' }}</td>
                        <td>{{ $trainer->assignedMemberships->count() }}</td>
                        <td><span class="badge {{ $trainer->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($trainer->status) }}</span></td>
                        <td style="min-width:360px">
                            <form method="POST" action="{{ route('admin.trainers.messages', $trainer) }}" style="display:grid;grid-template-columns:1fr auto;gap:8px;margin-bottom:8px">
                                @csrf
                                <input name="body" placeholder="Text this trainer..." required maxlength="1000">
                                <button class="btn" type="submit">Send</button>
                            </form>
                            <form method="POST" action="{{ route('admin.trainers.destroy', $trainer) }}" onsubmit="return confirm('Remove this trainer and their login?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn ghost" type="submit">Remove Trainer</button>
                            </form>
                        </td>
                    </tr>
                    @if ($trainer->trainerAdminMessages->isNotEmpty())
                        <tr>
                            <td colspan="6">
                                <strong>Latest admin message:</strong>
                                {{ $trainer->trainerAdminMessages->sortByDesc('created_at')->first()->body }}
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6">No trainers yet. Add one above.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
