<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Fitzone</title>
    <style>
        * { box-sizing: border-box; }
        :root {
            --ink: #f8fbff;
            --muted: #dbeafe;
            --panel: rgba(91, 130, 190, .54);
            --panel-strong: rgba(229, 239, 255, .72);
            --line: rgba(255,255,255,.22);
            --blue: #236fe8;
            --cyan: #35d6ff;
            --lime: #13a047;
            --coral: #f43f46;
        }
        html { min-width: 320px; }
        body {
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 50% 8%, rgba(77, 120, 210, .34), transparent 30%),
                radial-gradient(circle at 12% 84%, rgba(46, 140, 255, .18), transparent 34%),
                linear-gradient(115deg, #132942 0%, #103f8f 42%, #0e1d31 100%);
        }
        body:before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px), linear-gradient(rgba(255,255,255,.028) 1px, transparent 1px);
            background-size: 58px 58px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.65), transparent 76%);
            pointer-events: none;
        }
        a { color: inherit; }
        .page {
            position: relative;
            width: min(1180px, calc(100% - 34px));
            min-height: 100vh;
            margin: 0 auto;
            display: grid;
            grid-template-columns: .82fr 1fr .82fr;
            align-items: center;
            gap: clamp(18px, 3vw, 34px);
            padding: 34px 0;
        }
        .brand {
            position: absolute;
            top: 34px;
            left: 0;
            display: grid;
            gap: 1px;
            font-weight: 950;
            letter-spacing: 0;
            line-height: .9;
            text-shadow: 0 16px 34px rgba(0,0,0,.22);
        }
        .brand strong { font-size: clamp(34px, 4vw, 46px); }
        .brand span { font-size: 11px; color: var(--muted); letter-spacing: 5px; text-transform: uppercase; }
        .phone {
            position: relative;
            min-height: 620px;
            border-radius: 35px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.15);
            box-shadow: 0 30px 76px rgba(0,0,0,.36);
            background: #08111f;
        }
        .phone.small { min-height: 470px; align-self: end; margin-bottom: 58px; }
        .phone.right { min-height: 520px; align-self: center; }
        .photo {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: saturate(1.06) contrast(1.04);
        }
        .photo:after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(3, 10, 23, .12), rgba(6, 14, 31, .52) 46%, rgba(7, 17, 37, .86));
        }
        .photo.one { background-image: url("https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=900&q=80"); }
        .photo.two { background-image: url("https://images.unsplash.com/photo-1518310383802-640c2de311b2?auto=format&fit=crop&w=900&q=80"); }
        .photo.three { background-image: url("https://images.unsplash.com/photo-1548690312-e3b507d8c110?auto=format&fit=crop&w=900&q=80"); }
        .notch {
            position: absolute;
            top: 17px;
            left: 50%;
            width: 82px;
            height: 27px;
            transform: translateX(-50%);
            border-radius: 999px;
            background: rgba(225, 230, 241, .28);
            box-shadow: inset 0 1px 8px rgba(0,0,0,.22);
            z-index: 2;
        }
        .phone-content {
            position: relative;
            z-index: 2;
            min-height: inherit;
            padding: 32px 30px;
            display: flex;
            flex-direction: column;
        }
        .phone-title {
            margin: 0;
            font-size: 28px;
            line-height: 1;
            font-weight: 950;
            text-shadow: 0 8px 22px rgba(0,0,0,.28);
        }
        .glass {
            margin-top: auto;
            border: 1px solid var(--line);
            border-radius: 26px;
            background: linear-gradient(145deg, rgba(224, 238, 255, .72), rgba(93, 123, 164, .6));
            backdrop-filter: blur(20px);
            color: #f8fbff;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.22), 0 20px 44px rgba(0,0,0,.22);
        }
        .login-card {
            padding: 42px 30px 30px;
            position: relative;
        }
        .login-card:before {
            content: "";
            position: absolute;
            top: 13px;
            left: 50%;
            width: 102px;
            height: 5px;
            transform: translateX(-50%);
            border-radius: 999px;
            background: rgba(255,255,255,.92);
        }
        h1 {
            margin: 0 0 9px;
            font-size: clamp(31px, 4vw, 42px);
            line-height: .98;
            letter-spacing: 0;
        }
        p { margin: 0; color: rgba(248,251,255,.83); line-height: 1.35; }
        .form {
            display: grid;
            gap: 12px;
            margin-top: 22px;
        }
        label {
            display: grid;
            gap: 7px;
            font-size: 12px;
            font-weight: 850;
            color: rgba(255,255,255,.9);
        }
        input {
            width: 100%;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 14px;
            padding: 13px 14px;
            background: rgba(9, 20, 42, .38);
            color: #fff;
            font: inherit;
            outline: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
        }
        input:focus {
            border-color: rgba(53, 214, 255, .82);
            box-shadow: 0 0 0 4px rgba(53, 214, 255, .14);
        }
        .actions {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: center;
            margin-top: 8px;
        }
        button {
            border: 0;
            border-radius: 999px;
            min-height: 52px;
            padding: 0 9px 0 24px;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            color: #07101f;
            background: #fff;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(0,0,0,.18);
        }
        .arrow {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #fff;
            background: #02050b;
            font-size: 24px;
            line-height: 1;
        }
        .link-pill {
            min-height: 52px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            padding: 0 18px;
            text-decoration: none;
            color: rgba(255,255,255,.86);
            background: rgba(20, 37, 64, .42);
        }
        .error {
            margin-top: 16px;
            padding: 11px 13px;
            border-radius: 13px;
            color: #fff;
            background: rgba(255, 105, 105, .72);
            border: 1px solid rgba(255,255,255,.2);
        }
        .home-dot {
            position: absolute;
            top: 28px;
            right: 23px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,.36);
            border: 1px solid rgba(255,255,255,.24);
            backdrop-filter: blur(12px);
            z-index: 3;
        }
        .mini-panel {
            margin-top: auto;
            display: grid;
            gap: 14px;
        }
        .stat-panel {
            border-radius: 22px;
            padding: 17px;
            background: rgba(225, 237, 255, .57);
            border: 1px solid rgba(255,255,255,.22);
            backdrop-filter: blur(16px);
        }
        .icon-row { display: flex; gap: 8px; margin-top: 10px; }
        .round {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,.28);
            font-size: 13px;
        }
        .task-grid {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 12px;
            align-items: center;
        }
        .ring {
            width: 66px;
            height: 66px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: conic-gradient(var(--lime) 0 60%, rgba(255,255,255,.2) 60% 100%);
            color: #fff;
            font-weight: 950;
        }
        .ring span {
            width: 50px;
            height: 50px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(18, 29, 52, .9);
            font-size: 12px;
        }
        .progress-card {
            margin-top: auto;
            padding: 18px;
        }
        .progress-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }
        .gauge {
            height: 150px;
            display: grid;
            place-items: center;
            margin: 8px 0;
            border-radius: 20px;
            background: radial-gradient(circle at 50% 84%, rgba(255,255,255,.2), transparent 30%), repeating-conic-gradient(from 240deg, rgba(255,255,255,.62) 0deg 2deg, transparent 2deg 5deg);
            mask-image: radial-gradient(circle, black 0 68%, transparent 69%);
        }
        .gauge strong {
            font-size: 32px;
            text-shadow: 0 8px 20px rgba(0,0,0,.22);
        }
        .metric {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,.22);
        }
        .smart {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 18px;
            padding: 12px 16px;
            background: rgba(20, 37, 64, .42);
        }
        .toggle {
            width: 48px;
            height: 28px;
            border-radius: 999px;
            padding: 4px;
            background: rgba(255,255,255,.3);
        }
        .toggle:before {
            content: "";
            display: block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--lime);
            box-shadow: 0 0 18px var(--lime);
        }
        .copyright {
            position: absolute;
            right: 0;
            bottom: 28px;
            color: rgba(255,255,255,.78);
            font-size: 14px;
        }
        @media (max-width: 1040px) {
            .page { grid-template-columns: minmax(0, 1fr); width: min(520px, calc(100% - 28px)); padding-top: 96px; }
            .brand { left: 4px; }
            .phone.small, .phone.right, .copyright { display: none; }
            .phone { min-height: 690px; }
        }
        @media (max-width: 520px) {
            .page { width: min(100% - 20px, 430px); padding-top: 88px; }
            .brand strong { font-size: 32px; }
            .phone { min-height: 650px; border-radius: 28px; }
            .phone-content { padding: 28px 18px 18px; }
            .login-card { padding: 38px 18px 18px; border-radius: 23px; }
            .actions { grid-template-columns: minmax(0, 1fr); }
            .link-pill { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="brand">
            <strong>Fitzone</strong>
            <span>Smart Gym</span>
        </div>

        <section class="phone small" aria-hidden="true">
            <div class="photo one"></div>
            <span class="notch"></span>
            <span class="home-dot">⌂</span>
            <div class="phone-content">
                <div>
                    <h2 class="phone-title">Hi Dany,<br>Welcome!</h2>
                </div>
                <div class="mini-panel">
                    <h3 style="margin:0;font-size:24px">My Health</h3>
                    <div class="stat-panel">
                        <strong>Weekly Stats</strong>
                        <div class="icon-row">
                            <span class="round">⌁</span>
                            <span class="round">◷</span>
                            <span class="round">♨</span>
                            <span class="round">☆</span>
                        </div>
                    </div>
                    <div class="stat-panel task-grid">
                        <div>
                            <strong>Running</strong>
                            <p style="margin-top:22px;font-size:13px">3.8 km<br>Outdoor Walk</p>
                        </div>
                        <div class="ring"><span>60%</span></div>
                        <div class="ring"><span>70%</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="phone">
            <div class="photo two"></div>
            <span class="notch"></span>
            <div class="phone-content">
                <h2 class="phone-title">FitPulse</h2>
                <form class="glass login-card" method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <h1>Sign in for Fitness Success</h1>
                    <p>Fuel your gym, wallet, attendance, café orders, and trainer workflow from one dashboard.</p>

                    @if ($errors->any())
                        <div class="error">{{ $errors->first() }}</div>
                    @endif

                    <div class="form">
                        <label>Email
                            <input name="email" type="email" value="{{ old('email', 'member@fitzone.test') }}" required autofocus autocomplete="email">
                        </label>
                        <label>Password
                            <input name="password" type="password" value="123456789" required autocomplete="current-password">
                        </label>
                        <div class="actions">
                            <button type="submit">LOG IN <span class="arrow">→</span></button>
                            <a class="link-pill" href="{{ route('register') }}">Sign up</a>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="phone right" aria-hidden="true">
            <div class="photo three"></div>
            <span class="notch"></span>
            <span class="home-dot">⌂</span>
            <div class="phone-content">
                <div style="margin-top:auto">
                    <p style="font-weight:900;margin-bottom:10px">Today</p>
                    <div class="glass progress-card">
                        <div class="progress-title">
                            <h3 style="margin:0;font-size:23px">Running</h3>
                            <div class="icon-row" style="margin:0">
                                <span class="round">⌁</span>
                                <span class="round">◷</span>
                                <span class="round">♨</span>
                            </div>
                        </div>
                        <p style="font-weight:800">Track Record</p>
                        <div class="gauge"><strong>50%</strong></div>
                        <div class="metric"><span>Duration</span><strong>45.56 mins</strong></div>
                        <div class="metric"><span>Distance</span><strong>4.2 km</strong></div>
                        <div class="smart"><span class="toggle"></span><strong>Smart Tracking</strong></div>
                    </div>
                </div>
            </div>
        </section>

        <div class="copyright">© fitzone.test | smart gym operating system</div>
    </main>
</body>
</html>
