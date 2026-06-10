@php
    $name = auth()->user()->name ?? 'Guest';
    $role = auth()->user()->role ?? 'member';
    $dashboardHref = match ($role) {
        'admin' => route('admin.dashboard'),
        'trainer' => route('trainer.dashboard'),
        'cafe' => route('cafe.dashboard'),
        default => route('client.dashboard'),
    };
    $nav = match ($role) {
        'admin' => [
            ['Dashboard', route('admin.dashboard'), 'D'],
            ['Payments', route('admin.payments'), '$'],
            ['Trainers', route('admin.trainers'), 'T'],
            ['Cafe Staff', route('admin.cafe-staff'), 'POS'],
            ['Users', route('admin.users'), 'U'],
            ['Orders', route('admin.orders'), 'O'],
            ['Inventory', route('admin.inventory'), 'I'],
            ['Café', route('cafe.dashboard'), 'POS'],
            ['All Chats', route('admin.chats'), 'C'],
            ['Profile', route('profile.edit'), 'Me'],
        ],
        'trainer' => [
            ['Dashboard', route('trainer.dashboard'), 'D'],
            ['Payments', route('trainer.payments'), '$'],
            ['Attendance', route('trainer.attendance'), 'QR'],
            ['Client Chat', route('trainer.chat'), 'C'],
            ['Profile', route('profile.edit'), 'Me'],
        ],
        'cafe' => [
            ['Café Orders', route('cafe.dashboard'), 'POS'],
            ['Profile', route('profile.edit'), 'Me'],
        ],
        default => [
            ['Dashboard', route('client.dashboard'), 'D'],
            ['Today', route('client.today'), 'TD'],
            ['Choose Package', route('client.packages'), 'P'],
            ['Select Trainer', route('client.trainers'), 'T'],
            ['Workout Plan', route('client.workout'), 'W'],
            ['Diet Plan', route('client.diet'), 'N'],
            ['Attendance', route('client.attendance'), 'A'],
            ['Payments', route('client.payments'), '$'],
            ['Wallet', route('client.wallet'), 'W'],
            ['Café', route('client.cafe'), 'POS'],
            ['Chat', route('client.chat'), 'C'],
            ['Members', route('client.members'), 'M'],
            ['Profile', route('profile.edit'), 'Me'],
        ],
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Fitzone')</title>
    <style>
        :root {
            --bg: #edf3f9;
            --panel: #fff;
            --ink: #0b1324;
            --muted: #637794;
            --line: #e1e8f2;
            --nav: #12243b;
            --blue: #236fe8;
            --blue-deep: #0f3f8f;
            --green: #13a047;
            --red: #f43f46;
            --amber: #f7a31a;
            --violet: #7c3aed;
        }
        * { box-sizing: border-box; }
        html { min-width: 320px; }
        body { margin: 0; overflow-x: hidden; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: var(--ink); background: var(--bg); }
        a { color: inherit; text-decoration: none; }
        img, svg, video, canvas, audio { max-width: 100%; }
        .shell { min-height: 100vh; display: grid; grid-template-columns: 230px minmax(0, 1fr); }
        .menu-toggle { display: none; border: 0; width: 42px; height: 42px; border-radius: 8px; background: #fff; color: var(--ink); cursor: pointer; box-shadow: 0 8px 22px rgba(15, 23, 42, .08); }
        .menu-toggle span { display: block; width: 20px; height: 2px; margin: 5px auto; border-radius: 999px; background: currentColor; }
        .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, .46); z-index: 18; }
        .sidebar { background: linear-gradient(180deg, #162943, #0e1d31); color: #dbeafe; padding: 24px 18px; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; color: #fff; font-weight: 900; line-height: 1.1; }
        .brand span:last-child { min-width: 0; }
        .brand-mark { width: 38px; height: 38px; border-radius: 8px; background: var(--red); display: grid; place-items: center; }
        .nav-link { display: flex; align-items: center; gap: 10px; padding: 11px 12px; border-radius: 8px; margin-bottom: 6px; color: #cbd5e1; font-size: 14px; transition: transform .18s ease, background .18s ease, color .18s ease; }
        .nav-link:hover, .nav-link.active { background: linear-gradient(90deg, #1f6feb, #3b82f6); color: #fff; transform: translateX(4px); }
        .nav-icon { width: 26px; height: 26px; flex: 0 0 26px; border-radius: 7px; display: grid; place-items: center; background: rgba(255,255,255,.12); color: #fff; font-size: 12px; font-weight: 900; }
        .main { min-width: 0; width: 100%; max-width: 1540px; padding: 24px; }
        .topbar { display: grid; grid-template-columns: auto minmax(180px, 430px) auto; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 22px; }
        .search-form { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 8px; width: 100%; min-width: 0; }
        .search { width: 100%; min-width: 0; border: 1px solid var(--line); background: #fff; border-radius: 8px; padding: 12px 14px; color: var(--muted); }
        .search-btn { border: 0; border-radius: 7px; width: 44px; min-height: 42px; padding: 0; background: var(--blue); color: #fff; font-weight: 900; cursor: pointer; }
        .profile { min-width: 0; display: flex; align-items: center; gap: 10px; background: #fff; border: 1px solid var(--line); border-radius: 999px; padding: 8px 12px; font-size: 13px; }
        .profile-link { min-width: 0; display: flex; align-items: center; gap: 10px; }
        .profile-link strong { max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .avatar { width: 34px; height: 34px; flex: 0 0 34px; border-radius: 50%; background: linear-gradient(135deg, var(--amber), var(--red)); color: #fff; display: grid; place-items: center; font-weight: 900; object-fit: cover; }
        .logout { border: 0; background: transparent; color: var(--muted); cursor: pointer; font-weight: 700; }
        h1 { margin: 0 0 18px; font-size: 25px; overflow-wrap: anywhere; }
        h2 { margin: 0 0 14px; font-size: 18px; overflow-wrap: anywhere; }
        h3 { margin: 0 0 8px; font-size: 15px; }
        .grid { display: grid; gap: 16px; }
        .stats { grid-template-columns: repeat(auto-fit, minmax(min(100%, 190px), 1fr)); margin-bottom: 16px; }
        .two { grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr); margin-bottom: 16px; }
        .three { grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); margin-bottom: 16px; }
        .card { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; padding: 18px; box-shadow: 0 18px 44px rgba(13, 31, 54, .08); animation: riseIn .35s ease both; transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 22px 54px rgba(13, 31, 54, .12); border-color: #bfd0e7; }
        .stat { min-width: 0; display: flex; align-items: center; gap: 14px; }
        .icon { width: 48px; height: 48px; flex: 0 0 48px; border-radius: 14px; display: grid; place-items: center; font-weight: 900; }
        .stat small, .muted { color: var(--muted); }
        .stat strong { display: block; font-size: 25px; margin: 2px 0; overflow-wrap: anywhere; }
        .up { color: #16a34a; font-size: 12px; font-weight: 800; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #edf2f7; text-align: left; }
        th { color: #475569; font-size: 12px; }
        .badge { padding: 5px 9px; border-radius: 999px; font-size: 12px; font-weight: 800; display: inline-block; }
        .badge.green { background: #dcfce7; color: #15803d; }
        .badge.red { background: #fee2e2; color: #b91c1c; }
        .badge.amber { background: #fef3c7; color: #b45309; }
        .btn { border: 0; border-radius: 7px; padding: 10px 14px; font-weight: 900; color: #fff; background: var(--blue); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: transform .18s ease, filter .18s ease, box-shadow .18s ease; box-shadow: 0 8px 18px rgba(18, 99, 230, .16); text-align: center; }
        .btn:hover { transform: translateY(-1px); filter: brightness(1.04); box-shadow: 0 12px 24px rgba(18, 99, 230, .22); }
        .btn.ghost { background: #f1f5f9; color: #334155; box-shadow: none; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 14px; flex-wrap: wrap; }
        label { display: grid; gap: 6px; font-size: 12px; font-weight: 800; color: #334155; }
        input, select, textarea { border: 1px solid var(--line); border-radius: 7px; padding: 11px 12px; font: inherit; width: 100%; background: #fff; color: #334155; }
        textarea { min-height: 110px; resize: vertical; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr)); gap: 12px; }
        .chart { height: 230px; border-radius: 8px; background: repeating-linear-gradient(to top, transparent 0 45px, #edf2f7 46px), linear-gradient(to top, rgba(18,99,230,.12), transparent); position: relative; overflow: hidden; }
        .chart svg { position: absolute; inset: 18px 18px 26px; width: calc(100% - 36px); height: calc(100% - 44px); }
        .donut { width: 160px; aspect-ratio: 1; border-radius: 50%; background: conic-gradient(var(--green) 0 77%, var(--red) 77% 89%, var(--amber) 89% 100%); display: grid; place-items: center; }
        .donut span { width: 94px; aspect-ratio: 1; border-radius: 50%; background: #fff; display: grid; place-items: center; text-align: center; font-weight: 900; }
        .plans { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 14px; }
        .plan { min-width: 0; border: 1px solid var(--line); border-radius: 8px; padding: 16px; display: grid; gap: 10px; }
        .plan.featured { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(35,111,232,.14); }
        .price { font-size: 23px; font-weight: 900; }
        .calendar { display: grid; grid-template-columns: repeat(7, minmax(28px, 1fr)); gap: 8px; }
        .day { min-height: 34px; border-radius: 7px; display: grid; place-items: center; background: #f8fafc; font-size: 12px; }
        .present { background: #dcfce7; color: #15803d; font-weight: 900; }
        .absent { background: #fee2e2; color: #b91c1c; font-weight: 900; }
        .flow { display: grid; place-items: center; gap: 10px; text-align: center; }
        .flow-row { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .flow-box { border: 1px solid #93c5fd; background: #eff6ff; border-radius: 8px; padding: 10px 16px; font-weight: 900; min-width: 130px; }
        .connector { width: 2px; height: 18px; background: #94a3b8; }
        .friendly-hero { position: relative; overflow: hidden; background: linear-gradient(135deg, #132942 0%, #103f8f 48%, #1f66dc 100%); color: #fff; }
        .friendly-hero .muted { color: #dbeafe; }
        .friendly-hero:after { content: ""; position: absolute; right: -80px; top: -80px; width: 220px; height: 220px; border: 28px solid rgba(255,255,255,.09); border-radius: 50%; animation: breathe 3.4s ease-in-out infinite; }
        .friendly-hero > * { position: relative; z-index: 1; }
        .step-chip { display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 8px; background: rgba(255,255,255,.11); color: #fff; font-weight: 900; }
        .step-icon, .soft-icon { width: 34px; height: 34px; border-radius: 9px; display: grid; place-items: center; background: rgba(255,255,255,.18); font-weight: 900; }
        .soft-icon { background: #eff6ff; color: var(--blue); }
        .pulse-dot { width: 9px; height: 9px; border-radius: 999px; background: var(--green); box-shadow: 0 0 0 0 rgba(34,197,94,.5); animation: pulseRing 1.8s infinite; }
        .table-scroll { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .chat-shell { display: grid; grid-template-columns: minmax(240px, 300px) minmax(0, 1fr); gap: 16px; align-items: start; }
        .chat-room { min-width: 0; display: grid; gap: 10px; }
        .chat-tab { display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid var(--line); border-radius: 8px; background: #fff; }
        .chat-tab span:last-child { min-width: 0; overflow-wrap: anywhere; }
        .chat-tab.active { border-color: #93c5fd; background: #eff6ff; }
        .message-list { display: flex; flex-direction: column-reverse; gap: 12px; max-height: 470px; overflow-y: auto; padding-right: 4px; }
        .message { width: min(82%, 660px); border: 1px solid var(--line); border-radius: 8px; padding: 10px 12px; background: #f8fafc; overflow-wrap: anywhere; display: grid; gap: 7px; }
        .message.mine { margin-left: auto; background: #dbeafe; border-color: #bfdbfe; }
        .message-head { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .message.mine .message-head { justify-content: flex-end; }
        .message-avatar { width: 30px; height: 30px; flex: 0 0 30px; border-radius: 50%; background: #e2e8f0; color: #334155; display: grid; place-items: center; font-size: 12px; font-weight: 900; }
        .message-avatar.image { object-fit: cover; }
        .message.mine .message-avatar { order: 2; background: #1263e6; color: #fff; }
        .message-meta { min-width: 0; display: grid; gap: 1px; }
        .message.mine .message-meta { text-align: right; }
        .message strong { display: block; font-size: 13px; line-height: 1.15; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .message small { color: var(--muted); font-size: 11px; }
        .message-body { line-height: 1.45; }
        .message.mine .message-body { text-align: left; }
        .profile-photo-xl { width: 118px; height: 118px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 12px 30px rgba(15,23,42,.16); }
        .profile-photo-xl.fallback { display: grid; place-items: center; background: linear-gradient(135deg, var(--amber), var(--red)); color: #fff; font-size: 42px; font-weight: 900; }
        .composer { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 10px; margin-top: 14px; }
        @keyframes riseIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes breathe { 0%, 100% { transform: scale(1); opacity: .75; } 50% { transform: scale(1.06); opacity: 1; } }
        @keyframes pulseRing { 70% { box-shadow: 0 0 0 10px rgba(34,197,94,0); } 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); } }
        @media (min-width: 1400px) { .main { padding: 32px; } .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        @media (max-width: 1100px) {
            .shell { grid-template-columns: minmax(0, 1fr); }
            .menu-toggle { display: inline-block; }
            .sidebar { position: fixed; inset: 0 auto 0 0; width: min(280px, 86vw); height: 100vh; z-index: 20; transform: translateX(-100%); transition: transform .2s ease; }
            body.menu-open .sidebar { transform: translateX(0); }
            body.menu-open .sidebar-backdrop { display: block; }
            .two { grid-template-columns: minmax(0, 1fr); }
            .chat-shell { grid-template-columns: minmax(0, 1fr); }
            .chat-room { grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); }
        }
        @media (max-width: 720px) {
            .main { padding: 14px; }
            .topbar { grid-template-columns: auto 1fr; align-items: stretch; }
            .search-form { grid-column: 1 / -1; order: 3; }
            .profile { justify-self: end; max-width: 100%; border-radius: 8px; }
            .profile .alerts-label { display: none; }
            .card { padding: 14px; }
            .form-grid, .composer { grid-template-columns: minmax(0, 1fr); }
            .composer .btn, .actions .btn, .actions form, .actions form .btn { width: 100%; }
            .actions { justify-content: stretch; }
            .message { width: 100%; }
            .message strong { white-space: normal; }
            table { min-width: 620px; }
            .card[style*="grid-template-columns"], .friendly-hero { grid-template-columns: minmax(0, 1fr) !important; }
            .calendar { gap: 5px; }
            .day { min-height: 30px; font-size: 11px; }
        }
        @media (max-width: 430px) {
            .main { padding: 10px; }
            h1 { font-size: 22px; }
            h2 { font-size: 16px; }
            .card { padding: 12px; }
            .stat { align-items: flex-start; }
            .icon { width: 40px; height: 40px; flex-basis: 40px; border-radius: 10px; }
            .stat strong { font-size: 20px; }
            .profile { gap: 7px; padding: 7px 9px; }
            .profile-link strong { max-width: 92px; }
            .btn { width: 100%; padding: 11px 12px; }
            .flow-box { width: 100%; min-width: 0; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="sidebar-backdrop" data-close-menu></div>
        <aside class="sidebar">
            <a class="brand" href="{{ $dashboardHref }}"><span class="brand-mark">G</span><span>GYM<br><small>FITNESS</small></span></a>
            @foreach ($nav as [$label, $href, $icon])
                <a class="nav-link {{ request()->url() === $href ? 'active' : '' }}" href="{{ $href }}"><span class="nav-icon">{{ $icon }}</span>{{ $label }}</a>
            @endforeach
        </aside>
        <main class="main">
            <div class="topbar">
                <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" data-menu-toggle>
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <form class="search-form" method="GET" action="{{ route('member-search.index') }}">
                    <input class="search" name="q" value="{{ request('q') }}" placeholder="Search members by name, email, phone, or member no.">
                    <button class="search-btn" type="submit" aria-label="Search members">Go</button>
                </form>
                <div class="profile">
                    <span class="pulse-dot"></span>
                    <span class="alerts-label">Alerts</span>
                    <a class="profile-link" href="{{ route('profile.edit') }}">
                        @if (auth()->user()->profile_photo_url)
                            <img class="avatar" src="{{ auth()->user()->profile_photo_url }}" alt="{{ $name }}">
                        @else
                            <span class="avatar">{{ strtoupper(substr($name, 0, 1)) }}</span>
                        @endif
                        <strong>{{ $name }}</strong>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout" type="submit">Logout</button>
                    </form>
                </div>
            </div>
            @if (session('warning'))
                <section class="card" style="margin-bottom:16px;border-color:#fbbf24;background:#fffbeb;box-shadow:none">
                    <p style="margin:0;color:#92400e;font-weight:800">{{ session('warning') }}</p>
                </section>
            @endif
            @if (session('status'))
                <section class="card" style="margin-bottom:16px;border-color:#86efac;background:#f0fdf4;box-shadow:none">
                    <p style="margin:0;color:#166534;font-weight:800">{{ session('status') }}</p>
                </section>
            @endif
            @yield('content')
        </main>
    </div>
    <script>
        const menuButton = document.querySelector('[data-menu-toggle]');
        const closeTargets = document.querySelectorAll('[data-close-menu], .nav-link');

        function setMenu(open) {
            document.body.classList.toggle('menu-open', open);
            menuButton?.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        menuButton?.addEventListener('click', () => {
            setMenu(!document.body.classList.contains('menu-open'));
        });

        closeTargets.forEach((target) => {
            target.addEventListener('click', () => setMenu(false));
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setMenu(false);
            }
        });
    </script>
</body>
</html>
