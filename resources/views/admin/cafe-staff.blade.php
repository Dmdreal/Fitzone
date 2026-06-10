@extends('layouts.app')

@section('title', 'Manage Cafe Staff - Fitzone')

@section('content')
<h1>Manage Cafe Staff</h1>

@if (session('status'))
    <section class="card" style="border-color:#86efac;margin-bottom:16px">{{ session('status') }}</section>
@endif

<div class="grid two">
    <section class="card">
        <h2>Add Cafe Staff Login</h2>
        <p class="muted">The email and password added here become the cafe staff login. After login, the staff member opens the cafe dashboard automatically to manage orders.</p>
        <form method="POST" action="{{ route('admin.cafe-staff.store') }}">
            @csrf
            <div class="form-grid">
                <label>Full Name <input name="name" value="{{ old('name') }}" placeholder="e.g., John Mwangi" required></label>
                <label>Email <input type="email" name="email" value="{{ old('email') }}" placeholder="cafe@fitzone.test" required></label>
                <label>Password <input type="password" name="password" required minlength="8" placeholder="Min 8 characters"></label>
            </div>
            @if ($errors->any())
                <p class="muted" style="color:var(--red)">{{ $errors->first() }}</p>
            @endif
            <div class="actions"><button class="btn green" type="submit">+ Add Cafe Staff</button></div>
        </form>
    </section>

    <section class="card">
        <h2>Cafe Staff Access</h2>
        <p><span class="badge green">Automatic</span> Laravel checks the email and password against the users table.</p>
        <p class="muted">If that user has role <strong>cafe</strong>, they are sent to the cafe dashboard. They can:</p>
        <ul class="benefits-list" style="margin-top:12px; font-size:13px">
            <li>View pending cafe orders</li>
            <li>Accept and prepare orders</li>
            <li>Mark orders as completed</li>
            <li>Cancel orders if needed</li>
        </ul>
        <p class="muted" style="font-size:12px; margin-top:12px;">If a staff member is removed, that login no longer works.</p>
    </section>
</div>

<section class="card">
    <h2>Cafe Staff Records</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Created</th><th>Action</th></tr></thead>
            <tbody>
                @forelse ($cafeStaff as $staff)
                    <tr>
                        <td><strong>{{ $staff->name }}</strong></td>
                        <td>{{ $staff->email }}</td>
                        <td><span class="badge {{ $staff->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($staff->status) }}</span></td>
                        <td>{{ $staff->created_at->format('d M Y, H:i') }}</td>
                        <td style="min-width:220px">
                            <form method="POST" action="{{ route('admin.cafe-staff.destroy', $staff) }}" onsubmit="return confirm('Remove this cafe staff member? Their login will no longer work.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn ghost" type="submit" style="width:100%;">Remove Staff</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; padding:24px; color:var(--muted)">No cafe staff yet. Add one above to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card" style="margin-top:24px">
    <h2>How Cafe Staff Works</h2>
    <div class="grid two">
        <div>
            <h3 style="font-size:14px; margin:0 0 12px; color:var(--blue);">1. Create Account</h3>
            <p class="muted" style="font-size:13px; margin:0">Add cafe staff name, email, and password using the form above.</p>
        </div>
        <div>
            <h3 style="font-size:14px; margin:0 0 12px; color:var(--blue);">2. Share Credentials</h3>
            <p class="muted" style="font-size:13px; margin:0">Give the email and password to your cafe staff member securely.</p>
        </div>
        <div>
            <h3 style="font-size:14px; margin:0 0 12px; color:var(--green);">3. Staff Logs In</h3>
            <p class="muted" style="font-size:13px; margin:0">They visit /login and enter their cafe credentials.</p>
        </div>
        <div>
            <h3 style="font-size:14px; margin:0 0 12px; color:var(--green);">4. Access Dashboard</h3>
            <p class="muted" style="font-size:13px; margin:0">They're automatically redirected to /cafe/dashboard to manage orders.</p>
        </div>
    </div>
</section>
@endsection
