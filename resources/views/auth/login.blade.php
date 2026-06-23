<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Fitzone</title>
    <style>
        :root {
            --bg-dark: #081422;
            --panel: #0f1c34;
            --panel-soft: rgba(255,255,255,.08);
            --text: #eef2ff;
            --muted: #a3b8d7;
            --accent: #236fe8;
            --border: rgba(255,255,255,.12);
        }
        * { box-sizing: border-box; }
        html { min-width: 320px; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background: radial-gradient(circle at 12% 18%, rgba(35,111,232,.18), transparent 18%),
                        radial-gradient(circle at 92% 20%, rgba(244,63,70,.12), transparent 16%),
                        linear-gradient(180deg, #081422 0%, #07111f 48%, #06101a 100%);
            overflow-x: hidden;
        }
        body:before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px), linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px);
            background-size: 64px 64px;
            pointer-events: none;
        }
        body:after {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 20% 20%, rgba(35,111,232,.14), transparent 26%),
                        radial-gradient(circle at 80% 26%, rgba(244,63,70,.16), transparent 24%);
            pointer-events: none;
        }
        a { color: inherit; text-decoration: none; }
        .page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            position: relative;
        }
        .login-box {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-radius: 26px;
            overflow: hidden;
            background: rgba(9,18,36,.95);
            border: 1px solid rgba(255,255,255,.12);
            box-shadow: 0 30px 90px rgba(0,0,0,.4);
        }
        @media (max-width: 920px) {
            .login-box { grid-template-columns: 1fr; }
        }
        .form-panel {
            padding: 42px 40px;
            display: grid;
            gap: 24px;
            position: relative;
            background: linear-gradient(180deg, rgba(12,22,42,.98), rgba(10,16,30,.95));
        }
        .form-panel:before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(35,111,232,.14), transparent 24%);
            pointer-events: none;
        }
        .brand-row {
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 1;
        }
        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: var(--accent);
            color: #fff;
            font-weight: 900;
        }
        .brand-text strong {
            display: block;
            font-size: 18px;
            letter-spacing: -0.02em;
        }
        .brand-text small {
            display: block;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .3em;
            font-size: 10px;
        }
        .title-block h1 {
            margin: 0;
            font-size: clamp(34px, 4vw, 46px);
            line-height: 1.02;
            letter-spacing: -0.03em;
            z-index: 1;
        }
        .subtitle {
            margin: 12px 0 0;
            max-width: 420px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.75;
            z-index: 1;
        }
        .form-card {
            display: grid;
            gap: 18px;
            position: relative;
            z-index: 1;
        }
        .input-group {
            display: grid;
            gap: 10px;
        }
        .input-group label {
            color: var(--text);
            font-size: 13px;
            font-weight: 900;
        }
        .input-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 14px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
        }
        .input-row span {
            color: var(--muted);
            font-size: 16px;
            width: 24px;
            display: grid;
            place-items: center;
        }
        .input-row input {
            width: 100%;
            border: 0;
            background: transparent;
            color: var(--text);
            font: inherit;
            outline: none;
        }
        .input-row.password {
            justify-content: space-between;
        }
        .actions-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 6px;
            z-index: 1;
        }
        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 13px;
        }
        .checkbox-wrap input {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
        }
        .forgot-link {
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
        }
        .btn-primary,
        .btn-secondary {
            width: 100%;
            border: 0;
            border-radius: 16px;
            min-height: 54px;
            padding: 0 20px;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #3b82f6);
            color: #fff;
            box-shadow: 0 18px 32px rgba(35,111,232,.28);
        }
        .btn-secondary {
            background: rgba(255,255,255,.08);
            color: #fff;
            border: 1px solid rgba(255,255,255,.14);
        }
        .google-mark {
            width: 24px;
            height: 24px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: #fff;
            color: #000;
            font-size: 12px;
            font-weight: 700;
        }
        .or-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            color: var(--muted);
            font-size: 13px;
            margin: 0;
        }
        .or-divider::before,
        .or-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,.12);
        }
        .alt-link {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            text-align: left;
        }
        .alt-link a {
            color: var(--accent);
            font-weight: 700;
        }
        .error {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(244,67,54,.14);
            border: 1px solid rgba(244,67,54,.28);
            color: #ffe8e5;
        }
        .image-panel {
            position: relative;
            background: url('https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
            display: grid;
            align-items: end;
            min-height: 620px;
        }
        .image-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(4,10,24,.12), rgba(4,10,24,.82));
        }
        .image-content {
            position: relative;
            z-index: 1;
            padding: 40px;
            color: #fff;
            display: grid;
            gap: 18px;
        }
        .image-badge {
            width: fit-content;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .22em;
        }
        .image-title {
            margin: 0;
            font-size: clamp(30px, 4vw, 38px);
            line-height: 1.05;
        }
        .image-copy {
            margin: 0;
            max-width: 320px;
            color: rgba(255,255,255,.84);
            line-height: 1.75;
            font-size: 15px;
        }
        @media (max-width: 680px) {
            .form-panel { padding: 30px 24px; }
            .image-panel { display: none; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="login-box">
            <div class="form-panel">
                <div class="brand-row">
                    <div class="brand-mark">F</div>
                    <div class="brand-text">
                        <strong>Fitzone</strong>
                        <small>Smart Gym</small>
                    </div>
                </div>
                <div class="title-block">
                    <h1>FitPulse</h1>
                    <p class="subtitle">Sign in to your account and manage gym access, payments, attendance, café orders, and trainer workflows in one place.</p>
                </div>

                @if ($errors->any())
                    <div class="error">{{ $errors->first() }}</div>
                @endif

                <div class="form-card">
                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div class="input-group">
                            <label for="email">Email</label>
                            <div class="input-row">
                                <span>✉</span>
                                <input id="email" name="email" type="email" value="{{ old('email', 'member@fitzone.test') }}" required autofocus autocomplete="email" placeholder="Enter your email">
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="password">Password</label>
                            <div class="input-row password">
                                <span>🔒</span>
                                <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter your password">
                                <span>👁</span>
                            </div>
                        </div>
                        <div class="actions-row">
                            <label class="checkbox-wrap"><input type="checkbox" name="remember"> Remember me</label>
                            @if (Route::has('password.request'))
                                <a class="forgot-link" href="{{ route('password.request') }}">Forgot Password?</a>
                            @endif
                        </div>
                        <button type="submit" class="btn-primary">Sign in</button>
                    </form>
                    <p class="or-divider">OR</p>
                    <button type="button" class="btn-secondary"><span class="google-mark">G</span>Sign in with Google</button>
                </div>
                <p class="alt-link">Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
            </div>
            <div class="image-panel">
                <div class="image-content">
                    <span class="image-badge">High Intensity</span>
                    <h2 class="image-title">Train harder with FitPulse.</h2>
                    <p class="image-copy">A modern, responsive login experience that fits the look of your gym management platform.</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
