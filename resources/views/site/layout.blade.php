@php
    $dashboardRoute = auth()->check()
        ? match (auth()->user()->role) {
            'admin' => route('admin.dashboard'),
            'trainer' => route('trainer.dashboard'),
            'cafe' => route('cafe.dashboard'),
            default => route('client.dashboard'),
        }
        : route('login');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Fitzone Gym')</title>
    <style>
        :root {
            --ink: #0b1324;
            --muted: #637794;
            --line: #e1e8f2;
            --green: #13a047;
            --red: #f43f46;
            --blue: #236fe8;
            --blue-deep: #123f8c;
            --amber: #f7a31a;
            --panel: #ffffff;
            --soft: #edf3f9;
        }
        * { box-sizing: border-box; }
        html { min-width: 320px; scroll-behavior: smooth; }
        body { margin: 0; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: var(--ink); background: var(--soft); }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }
        .site-nav { position: sticky; top: 0; z-index: 20; background: rgba(255,255,255,.95); border-bottom: 1px solid var(--line); backdrop-filter: blur(14px); box-shadow: 0 10px 28px rgba(13,31,54,.05); }
        .nav-inner { width: min(1180px, calc(100% - 32px)); margin: 0 auto; min-height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 18px; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 950; }
        .brand-mark { width: 42px; height: 42px; border-radius: 8px; background: var(--red); color: #fff; display: grid; place-items: center; }
        .links { display: flex; align-items: center; gap: 18px; color: #334155; font-weight: 800; font-size: 14px; }
        .btn { border: 0; border-radius: 7px; padding: 12px 16px; font-weight: 950; color: #fff; background: var(--blue); display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; }
        .btn.green { background: var(--blue); }
        .btn.ghost { background: #eef2f7; color: #263449; }
        .hero { min-height: calc(80vh - 76px); display: grid; align-items: end; position: relative; overflow: hidden; color: #fff; background-image: linear-gradient(105deg, rgba(18,41,66,.96), rgba(16,63,143,.78), rgba(35,111,232,.52)), url("https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1800&q=80"); background-size: cover; background-position: center; }
        .hero:before, .page-title:before { content: ""; position: absolute; inset: 0; background: linear-gradient(105deg, rgba(18,41,66,.94), rgba(16,63,143,.76), rgba(35,111,232,.46)); z-index: 1; }
        .hero-video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
        .hero-inner, .section-inner { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
        .hero-inner, .page-title .section-inner { position: relative; z-index: 2; }
        .hero-content { width: min(720px, 100%); padding: 80px 0 70px; }
        .eyebrow { color: #dbeafe; font-weight: 950; text-transform: uppercase; font-size: 12px; letter-spacing: 1.6px; }
        h1 { margin: 10px 0 16px; font-size: clamp(42px, 7vw, 82px); line-height: .94; letter-spacing: 0; }
        .hero p { color: #dbeafe; font-size: 18px; line-height: 1.55; width: min(640px, 100%); }
        .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; }
        .hero-strip { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1px; background: rgba(255,255,255,.18); margin-top: 54px; border: 1px solid rgba(255,255,255,.18); border-radius: 8px; overflow: hidden; }
        .hero-strip div { padding: 18px; background: rgba(2,6,23,.4); }
        .hero-strip strong { display: block; font-size: 23px; }
        section { padding: 72px 0; background: var(--soft); }
        .soft { background: var(--soft); }
        .section-head { display: flex; justify-content: space-between; align-items: end; gap: 18px; margin-bottom: 28px; }
        .section-head h2, .page-title h1 { margin: 0; font-size: clamp(30px, 4vw, 48px); line-height: 1; }
        .section-head p, .page-title p, .muted { color: var(--muted); line-height: 1.55; }
        .grid { display: grid; gap: 16px; }
        .three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .card { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; padding: 20px; box-shadow: 0 18px 44px rgba(13,31,54,.08); transition: all 0.3s ease; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 24px 54px rgba(13,31,54,.13); border-color: var(--blue); }
        .image-card { overflow: hidden; padding: 0; position: relative; }
        .image-card img { width: 100%; height: 260px; object-fit: cover; transition: transform 0.3s ease; }
        .image-card:hover img { transform: scale(1.05); }
        .image-card div { padding: 20px; }
        .icon { width: 52px; height: 52px; border-radius: 10px; display: grid; place-items: center; color: #fff !important; background: linear-gradient(135deg, var(--blue-deep), var(--blue)) !important; font-weight: 950; margin-bottom: 14px; font-size: 18px; }
        .price { font-size: 34px; font-weight: 950; margin: 10px 0; color: var(--blue); }
        .badge { display: inline-block; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 950; background: #dbeafe; color: #1e40af; }
        .badge.green, .badge.amber { background: #dbeafe; color: #1e40af; }
        .stat-card { background: linear-gradient(135deg, rgba(35,111,232,.09), rgba(18,63,140,.07)); border: 1px solid rgba(35,111,232,.2); }
        .stat-card:hover { border-color: var(--blue); background: linear-gradient(135deg, rgba(35,111,232,.13), rgba(18,63,140,.1)); }
        .feature-grid { display: grid; gap: 20px; }
        .feature-item { display: flex; gap: 16px; align-items: flex-start; }
        .feature-item .icon { flex-shrink: 0; margin-bottom: 0; width: 40px; height: 40px; font-size: 16px; }
        .check { color: var(--blue); font-weight: 950; }
        .testimonial { border-left: 4px solid var(--blue); padding-left: 16px; }
        .testimonial .author { color: var(--muted); font-size: 13px; margin-top: 10px; }
        .step-badge { background: linear-gradient(135deg, var(--blue-deep), var(--blue)); color: #fff; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; font-weight: 950; font-size: 18px; }
        .page-title { position: relative; overflow: hidden; padding: 76px 0; background: linear-gradient(135deg, #132942 0%, #103f8f 48%, #1f66dc 100%); color: #fff; }
        .page-title p { color: #dbeafe; max-width: 720px; }
        .contact-card input, .contact-card textarea, .contact-card select { width: 100%; border: 1px solid var(--line); border-radius: 7px; padding: 12px; font: inherit; }
        .contact-card textarea { min-height: 130px; resize: vertical; }
        label { display: grid; gap: 7px; font-size: 12px; font-weight: 900; color: #334155; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .footer { padding: 36px 0; color: #cbd5e1; background: #0e1d31; border-top: 1px solid rgba(226,232,240,.1); }
        .footer-inner { width: min(1180px, calc(100% - 32px)); margin: 0 auto; display: flex; justify-content: space-between; gap: 18px; flex-wrap: wrap; }
        .footer-section { min-width: 180px; }
        .footer-section h3 { color: #fff; font-weight: 950; margin-bottom: 12px; }
        .footer-section a { display: block; font-size: 13px; color: #cbd5e1; margin: 6px 0; transition: color 0.2s; }
        .footer-section a:hover { color: #fff; }
        .cta-banner { background: linear-gradient(135deg, #132942, #236fe8); color: #fff; border-radius: 12px; padding: 40px; text-align: center; margin: 40px 0; }
        .cta-banner h2 { margin: 0 0 12px; font-size: 32px; }
        .cta-banner p { max-width: 600px; margin: 0 auto 20px; opacity: 0.95; }
        .stats-section { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin: 40px 0; }
        .stat-item { text-align: center; }
        .stat-item strong { display: block; font-size: 32px; color: var(--blue); margin-bottom: 4px; }
        .stat-item p { color: var(--muted); font-size: 14px; }
        .benefits-list { list-style: none; padding: 0; margin: 16px 0; }
        .benefits-list li { padding: 10px 0; border-bottom: 1px solid var(--line); display: flex; gap: 10px; align-items: center; }
        .benefits-list li:last-child { border-bottom: none; }
        .benefits-list li:before { content: "✓"; color: var(--blue); font-weight: 950; font-size: 18px; }
        @media (max-width: 900px) {
            .links { display: none; }
            .hero-strip, .three, .two, .form-grid, .stats-section { grid-template-columns: minmax(0, 1fr); }
            .section-head { display: grid; }
            .hero { min-height: auto; }
            .feature-item { gap: 12px; }
            .footer-inner { flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="site-nav">
        <div class="nav-inner">
            <a class="brand" href="{{ route('site.home') }}"><span class="brand-mark">F</span><span>Fitzone<br><small>Smart Gym</small></span></a>
            <nav class="links">
                <a href="{{ route('site.home') }}">Home</a>
                <a href="{{ route('site.about') }}">About</a>
                <a href="{{ route('site.services') }}">Services</a>
                <a href="{{ route('site.memberships') }}">Memberships</a>
                <a href="{{ route('site.trainers') }}">Trainers</a>
                <a href="{{ route('site.contact') }}">Contact</a>
            </nav>
            <a class="btn green" href="{{ $dashboardRoute }}">{{ auth()->check() ? 'Dashboard' : 'Login Now' }}</a>
        </div>
    </header>

    @yield('content')

    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-section">
                <h3>Fitzone</h3>
                <p style="font-size:13px">Smart gym management for training, payments, attendance, and member engagement.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="{{ route('site.home') }}">Home</a>
                <a href="{{ route('site.services') }}">Services</a>
                <a href="{{ route('site.memberships') }}">Memberships</a>
                <a href="{{ route('site.trainers') }}">Trainers</a>
            </div>
            <div class="footer-section">
                <h3>Support</h3>
                <a href="{{ route('site.about') }}">About</a>
                <a href="{{ route('site.contact') }}">Contact</a>
                <a href="mailto:fitzone@gmail.com">Email Us</a>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p style="font-size:13px">+254746899732<br>Fitzone@gmail.com</p>
            </div>
        </div>
        <div style="width: min(1180px, calc(100% - 32px)); margin: 24px auto 0; padding-top: 24px; border-top: 1px solid rgba(226,232,240,.1); text-align: center; font-size: 12px;">
            <p>&copy; 2026 Fitzone Gym. All rights reserved. | Smart gym platform built for modern fitness clubs.</p>
        </div>
    </footer>
</body>
</html>
