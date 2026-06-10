@extends('layouts.app')

@section('title', 'Attendance Scan - Fitzone')

@section('content')
<h1>Attendance Scanner</h1>

<section class="card" style="margin-bottom:16px">
    <h2>QR Scan / Manual Fallback</h2>
    <p class="muted">Scan the client's QR code, paste the scanned member-card URL, or enter the member number exactly, for example GYM-1024.</p>
    <form method="POST" action="{{ route('trainer.attendance.mark') }}" class="form-grid">
        @csrf
        <label>Member Number or QR Token
            <input name="identifier" autofocus required placeholder="GYM-0004">
        </label>
        <label style="align-self:end">
            <button class="btn" type="submit">Record In / Out</button>
        </label>
    </form>
</section>

<section class="card" style="margin-bottom:16px">
    <h2>Paperwork Scan Import</h2>
    <p class="muted">Use phone OCR, scanner text, TXT, CSV, image, or scanned PDF. Each row should include a member number or full name plus time in and optional time out.</p>
    <p class="muted" style="margin-top:8px"><strong>OCR:</strong> {{ $ocrStatus }}</p>
    <form method="POST" action="{{ route('trainer.attendance.paperwork') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <label>Attendance Date
                <input type="date" name="attendance_date" value="{{ now()->toDateString() }}" required>
            </label>
            <label>Upload Sheet / Scan
                <input type="file" name="sheet_file" accept=".txt,.csv,.jpg,.jpeg,.png,.webp,.bmp,.tif,.tiff,.pdf,text/plain,text/csv,image/*,application/pdf">
            </label>
        </div>
        <label style="margin-top:12px">Scanned Paperwork Text
            <textarea name="scan_text" placeholder="GYM-0026 Amit Verma 07:15 08:30&#10;Priya Singh 6:00 AM 7:20 AM&#10;GYM-0028 Kevin Otieno in 18:10 out 19:05"></textarea>
        </label>
        <div class="actions">
            <button class="btn" type="submit">Import Attendance</button>
        </div>
    </form>
</section>

@if (! empty($importResults))
    <section class="card" style="margin-bottom:16px">
        <h2>Import Results</h2>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Status</th><th>Result</th><th>Original Line</th></tr></thead>
                <tbody>
                    @foreach ($importResults as $result)
                        <tr>
                            <td><span class="badge {{ in_array($result['status'], ['Created', 'Updated'], true) ? 'green' : 'amber' }}">{{ $result['status'] }}</span></td>
                            <td>{{ $result['message'] }}</td>
                            <td>{{ $result['line'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif

<section class="card">
    <h2>Today</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Member</th><th>Member No.</th><th>Check In</th><th>Check Out</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($todayAttendances as $attendance)
                    <tr>
                        <td>{{ $attendance->member->name }}</td>
                        <td>{{ $attendance->member->member_number }}</td>
                        <td>{{ $attendance->check_in_at ?? '-' }}</td>
                        <td>{{ $attendance->check_out_at ?? '-' }}</td>
                        <td><span class="badge green">{{ ucfirst($attendance->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5">No attendance marked today.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
