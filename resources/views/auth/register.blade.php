<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Fitzone</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; min-height: 100vh; display: grid; place-items: center; padding: 16px; background: #eef2f7; font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: #0f172a; }
        .auth { width: min(520px, calc(100% - 28px)); background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 28px; box-shadow: 0 20px 50px rgba(15,23,42,.09); }
        h1 { margin: 0 0 6px; }
        p { color: #64748b; margin: 0 0 20px; }
        label { display: grid; gap: 7px; margin-bottom: 14px; font-weight: 800; font-size: 13px; }
        input, select { width: 100%; border: 1px solid #e2e8f0; border-radius: 7px; padding: 12px; font: inherit; }
        button { border: 0; border-radius: 7px; padding: 12px 16px; background: #1263e6; color: #fff; font-weight: 900; cursor: pointer; text-align: center; }
        .row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 8px; flex-wrap: wrap; }
        .error { color: #b91c1c; background: #fee2e2; border-radius: 7px; padding: 10px 12px; margin-bottom: 14px; }
        @media (max-width: 430px) { .auth { width: 100%; padding: 20px; } h1 { font-size: 24px; } .row, button { width: 100%; } }
    </style>
</head>
<body>
    <form class="auth" method="POST" action="{{ route('register.store') }}">
        @csrf
        <h1>Create Fitzone Account</h1>
        <p>New accounts start as clients. Package and trainer selection comes next.</p>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <label>Name <input name="name" value="{{ old('name') }}" required></label>
        <label>Email <input name="email" type="email" value="{{ old('email') }}" required></label>
        <label>Password <input name="password" type="password" required></label>
        <label>Confirm Password <input name="password_confirmation" type="password" required></label>
        <div class="row">
            <button type="submit">Register</button>
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </form>
</body>
</html>
