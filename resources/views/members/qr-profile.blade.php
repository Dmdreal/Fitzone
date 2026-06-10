<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $member->name }} - Fitzone Member Card</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-width: 320px;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 18px;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #f8fafc;
            background: radial-gradient(circle at top, rgba(37, 99, 235, .35), transparent 30%), linear-gradient(135deg, #111827, #1f2937 48%, #0f172a);
        }
        .card {
            width: min(520px, 100%);
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 18px;
            overflow: hidden;
            background: rgba(15, 23, 42, .82);
            box-shadow: 0 28px 70px rgba(0,0,0,.32);
        }
        .hero {
            padding: 28px;
            background: linear-gradient(135deg, rgba(37, 99, 235, .72), rgba(14, 165, 233, .35));
        }
        .avatar {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #fff;
            color: #1d4ed8;
            font-size: 34px;
            font-weight: 950;
            margin-bottom: 14px;
        }
        h1 { margin: 0 0 6px; font-size: 30px; line-height: 1.05; }
        p { margin: 0; color: #cbd5e1; line-height: 1.45; }
        .body { padding: 22px; display: grid; gap: 14px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; }
        .item {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 12px;
            padding: 14px;
            background: rgba(255,255,255,.06);
        }
        small { display: block; color: #94a3b8; font-size: 12px; font-weight: 800; margin-bottom: 6px; }
        strong { font-size: 17px; overflow-wrap: anywhere; }
        .badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 900;
            background: #dcfce7;
            color: #166534;
        }
        .badge.red { background: #fee2e2; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        td { padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.1); color: #dbeafe; }
        td:last-child { text-align: right; color: #fff; }
        .footer { padding: 0 22px 22px; color: #94a3b8; font-size: 12px; }
    </style>
</head>
<body>
    <main class="card">
        <section class="hero">
            <div class="avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
            <h1>{{ $member->name }}</h1>
            <p>{{ $member->member_number }} · Fitzone member profile</p>
        </section>

        <section class="body">
            <div>
                @if ($activeMembership)
                    <span class="badge">Active membership</span>
                @else
                    <span class="badge red">No active membership</span>
                @endif
            </div>

            <div class="grid">
                <div class="item">
                    <small>Package</small>
                    <strong>{{ $membership?->package?->name ?? 'No package' }}</strong>
                </div>
                <div class="item">
                    <small>Trainer</small>
                    <strong>{{ $membership?->trainer?->name ?? 'Not assigned' }}</strong>
                </div>
                <div class="item">
                    <small>Valid Until</small>
                    <strong>{{ $membership?->ends_at?->format('d M Y') ?? '-' }}</strong>
                </div>
                <div class="item">
                    <small>Account Status</small>
                    <strong>{{ ucfirst($member->status) }}</strong>
                </div>
            </div>

            <div class="item">
                <small>Recent Attendance</small>
                <table>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                                <td>{{ $attendance->check_in_at ?? '-' }} / {{ $attendance->check_out_at ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">No attendance records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <div class="footer">This card is generated from the member's unique QR code.</div>
    </main>
</body>
</html>
