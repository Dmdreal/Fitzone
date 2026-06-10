@extends('layouts.app')

@section('title', 'Attendance - Fitzone')

@section('content')
<h1>Attendance</h1>
@if ($membership)
    <section class="grid two">
        <article class="card stat">
            <div class="icon" style="background:#dbeafe;color:var(--blue)">ID</div>
            <div><small>Member Number</small><strong>{{ $member->member_number }}</strong><span class="up">Manual fallback identifier</span></div>
        </article>
        <section class="card">
            <h2>QR Token</h2>
            <div style="display:grid;grid-template-columns:auto 1fr;gap:14px;align-items:center">
                <img src="{{ route('members.qr.svg', $member) }}" alt="QR code for {{ $member->name }}" style="width:160px;aspect-ratio:1;background:#fff;border:10px solid #fff;border-radius:8px;box-shadow:0 0 0 1px #e2e8f0">
                <div>
                    <p class="muted" style="overflow-wrap:anywhere;margin-bottom:10px">{{ route('members.qr.show', $member->qr_token) }}</p>
                    <a class="btn ghost" href="{{ route('members.qr.svg', $member) }}" target="_blank">Open QR</a>
                </div>
            </div>
        </section>
    </section>
@endif
<section class="card">
    @if (! $membership)
        @if ($latestMembership?->status === 'expired')
            <h2>Your package days are over</h2>
            <p class="muted">Recharge your package to unlock attendance records again.</p>
            <a class="btn" href="{{ route('client.packages') }}">Recharge Package</a>
        @else
            <h2>Attendance locked</h2>
            <p class="muted">Activate a package to unlock attendance records.</p>
            <a class="btn" href="{{ route('client.packages') }}">Choose Package</a>
        @endif
    @elseif ($attendances->isNotEmpty())
        <h2>Recent Attendance</h2>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Date</th><th>Status</th><th>Check In</th><th>Check Out</th><th>QR Code</th></tr></thead>
                <tbody>
                    @foreach ($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                            <td><span class="badge {{ $attendance->status === 'present' ? 'green' : 'red' }}">{{ ucfirst($attendance->status) }}</span></td>
                            <td>{{ $attendance->check_in_at ?? '-' }}</td>
                            <td>{{ $attendance->check_out_at ?? '-' }}</td>
                            <td>{{ $attendance->qr_code ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <h2>Recent Attendance</h2>
        <p class="muted">No attendance records yet.</p>
    @endif
</section>
@endsection
