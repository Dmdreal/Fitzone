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
        .brand-mark { width: 56px; height: 42px; border-radius: 8px; background: var(--red); color: #fff; display: grid; place-items: center; font-size: 20px; transition: transform .3s ease, opacity .2s ease; overflow: hidden; position: relative; }
        .brand-mark:hover { transform: scale(1.05); }
        .brand-mark .brand-icon { position: absolute; inset: 0; display: grid; place-items: center; font-size: 20px; opacity: 0; animation: logoCycle 6s infinite ease-in-out; }
        .brand-mark .brand-icon:nth-child(1) { animation-delay: 0s; }
        .brand-mark .brand-icon:nth-child(2) { animation-delay: 2s; }
        .brand-mark .brand-icon:nth-child(3) { animation-delay: 4s; }
        @keyframes logoCycle { 0%, 16.66% { opacity: 1; transform: translateY(0); } 25%, 100% { opacity: 0; transform: translateY(-8px); } }
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
        /* Reveal / scroll animations */
        .reveal { opacity: 0; transform: translateY(18px) scale(.995); transition: opacity .6s cubic-bezier(.2,.9,.3,1), transform .6s cubic-bezier(.2,.9,.3,1); will-change: opacity, transform; }
        .reveal.visible { opacity: 1; transform: none; }
        .reveal.fade { transform: none; }
        .reveal.slow { transition-duration: .9s; }
        .reveal.scale { transform: translateY(6px) scale(.98); }
        .reveal.inline { display: inline-block; }
        .steps-grid { display: grid; gap: 18px; align-items: stretch; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .steps-grid .arrow { display: flex; align-items: center; justify-content: center; font-size: 28px; color: var(--blue); }
        .three { grid-template-columns: repeat(3, minmax(260px, 1fr)); }
        .two { grid-template-columns: repeat(2, minmax(260px, 1fr)); }
        .four { grid-template-columns: repeat(4, minmax(220px, 1fr)); }
        .flow-bottom { display: flex; justify-content: center; }
        .flow-bottom .card { max-width: 640px; width: 100%; }
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
        .footer { padding: 56px 0 32px; color: #cbd5e1; background: #08152f; border-top: 1px solid rgba(255,255,255,.06); }
        .footer-inner { width: min(1180px, calc(100% - 48px)); margin: 0 auto; display: grid; grid-template-columns: minmax(240px, 320px) repeat(3, minmax(180px, 1fr)); gap: 28px; padding-bottom: 28px; }
        .footer-brand { display: grid; gap: 18px; }
        .footer-brand p { max-width: 280px; line-height: 1.8; color: #d1d9ea; }
        .footer-social { display: flex; gap: 10px; }
        .footer-social a { width: 38px; height: 38px; border-radius: 50%; display: grid; place-items: center; background: rgba(255,255,255,.06); color: #fff; text-decoration: none; font-weight: 700; transition: background .2s; }
        .footer-social a:hover { background: rgba(255,255,255,.14); }
        .footer-section { display: grid; gap: 12px; }
        .footer-section h3 { color: #fff; font-weight: 950; margin: 0; font-size: 14px; }
        .footer-section p, .footer-section a { font-size: 14px; line-height: 1.8; color: #cbd5e1; }
        .footer-section a { text-decoration: none; transition: color .2s; }
        .footer-section a:hover { color: #fff; }
        .footer-section .link-item { display: flex; align-items: center; gap: 10px; position: relative; }
        .footer-section .link-item::before { content: '›'; color: #3b82f6; font-size: 12px; }
        .footer-bottom { width: min(1180px, calc(100% - 48px)); margin: 0 auto; display: grid; grid-template-columns: minmax(240px, 1fr) minmax(280px, 1fr) minmax(220px, 1fr); gap: 24px; align-items: center; padding-top: 24px; border-top: 1px solid rgba(255,255,255,.08); font-size: 13px; }
        .footer-badge { width: 44px; height: 44px; border-radius: 14px; display: grid; place-items: center; background: rgba(59,130,246,.12); color: #93c5fd; font-weight: 800; }
        .footer-bottom-claim { display: grid; gap: 6px; }
        .footer-bottom-claim strong { color: #fff; font-weight: 700; }
        .footer-payments { display: flex; gap: 12px; justify-content: flex-end; flex-wrap: wrap; }
        .payment-card { border: 1px solid rgba(255,255,255,.08); border-radius: 10px; padding: 10px 16px; background: rgba(255,255,255,.03); color: #fff; font-size: 13px; font-weight: 700; letter-spacing: .4px; }
        .footer-copyright { text-align: center; color: #94a3b8; }
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
        .hamburger { display: none; flex-direction: column; gap: 6px; cursor: pointer; background: none; border: none; padding: 8px; margin-right: 8px; }
        .hamburger span { width: 24px; height: 3px; background: var(--ink); border-radius: 2px; transition: all 0.3s ease; display: block; }
        .hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(10px, 10px); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(7px, -7px); }
        .mobile-menu { display: none; position: fixed; top: 76px; left: 0; right: 0; background: rgba(255,255,255,.98); backdrop-filter: blur(10px); border-bottom: 1px solid var(--line); flex-direction: column; gap: 0; max-height: calc(100vh - 76px); overflow-y: auto; z-index: 19; box-shadow: 0 10px 28px rgba(13,31,54,.1); }
        .mobile-menu.active { display: flex; }
        .mobile-menu a { padding: 16px 24px; color: #334155; font-weight: 700; font-size: 15px; border-bottom: 1px solid var(--line); text-decoration: none; transition: background 0.2s; }
        .mobile-menu a:hover { background: var(--soft); }
        .mobile-menu .btn { width: calc(100% - 48px); margin: 12px 24px; }
        .nav-inner { padding: 0 16px; }
        .brand span small { font-size: 10px; line-height: 1.2; }
        @media (max-width: 900px) {
            .links { display: none; }
            .hamburger { display: flex; }
            .hero-strip, .two, .four, .form-grid, .stats-section { grid-template-columns: repeat(2, minmax(240px, 1fr)); }
            .steps-grid { grid-template-columns: 1fr; }
            .steps-grid .arrow { display: none; }
            .section-head { display: grid; }
            .hero { min-height: auto; }
            .feature-item { gap: 12px; }
            .footer-inner { display: grid; grid-template-columns: 1fr; gap: 28px; }
            .nav-inner { gap: 12px; padding: 0 12px; }
            .btn { padding: 10px 14px; font-size: 13px; }
            html { min-width: 320px; }
            body { margin: 0; padding: 0; }
            section { padding: 48px 0; }
            .section-inner { width: min(100%, calc(100% - 24px)); padding: 0 12px; }
            .footer-bottom { grid-template-columns: 1fr; gap: 16px; }
            .flow-bottom { padding: 0 12px; }
        }
        @media (max-width: 640px) {
            .three, .two, .four, .feature-grid, .stats-section { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .nav-inner { min-height: 70px; }
            .brand { gap: 8px; font-size: 14px; }
            .brand-mark { width: 36px; height: 36px; font-size: 16px; }
            h1 { font-size: clamp(28px, 6vw, 48px); }
            .hero-content { padding: 40px 0 50px; }
            .cta-banner { padding: 24px; }
            .cta-banner h2 { font-size: 24px; }
            .stats-section { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .form-grid { grid-template-columns: 1fr; }
            section { padding: 40px 0; }
            .contact-card { padding: 16px; }
        }
        @media (max-width: 480px) {
            .nav-inner { min-height: 64px; gap: 8px; }
            .btn { padding: 8px 12px; font-size: 12px; }
            .hero-strip { grid-template-columns: 1fr; }
            .hero-actions { gap: 8px; }
            .stats-section { gap: 12px; }
            section { padding: 32px 0; }
            h1 { font-size: clamp(24px, 5vw, 36px); }
            .section-head h2, .page-title h1 { font-size: clamp(24px, 5vw, 36px); }
        }
    </style>
</head>
<body>
    <header class="site-nav">
        <div class="nav-inner">
            <a class="brand" href="{{ route('site.home') }}">
                <span class="brand-mark">
                    <span class="brand-icon">🏋️</span>
                    <span class="brand-icon">🥇</span>
                    <span class="brand-icon">💪</span>
                </span>
                <span>Fitzone<br><small>Smart Gym</small></span>
            </a>
            <nav class="links">
                <a href="{{ route('site.home') }}">Home</a>
                <a href="{{ route('site.about') }}">About</a>
                <a href="{{ route('site.services') }}">Services</a>
                <a href="{{ route('site.memberships') }}">Memberships</a>
                <a href="{{ route('site.trainers') }}">Trainers</a>
                <a href="{{ route('site.contact') }}">Contact</a>
            </nav>
            <a class="btn green" href="{{ $dashboardRoute }}">{{ auth()->check() ? 'Dashboard' : 'Login Now' }}</a>
            <button class="hamburger" aria-label="Toggle navigation menu" id="hamburgerBtn">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        <nav class="mobile-menu" id="mobileMenu">
            <a href="{{ route('site.home') }}" onclick="closeMobileMenu()">Home</a>
            <a href="{{ route('site.about') }}" onclick="closeMobileMenu()">About</a>
            <a href="{{ route('site.services') }}" onclick="closeMobileMenu()">Services</a>
            <a href="{{ route('site.memberships') }}" onclick="closeMobileMenu()">Memberships</a>
            <a href="{{ route('site.trainers') }}" onclick="closeMobileMenu()">Trainers</a>
            <a href="{{ route('site.contact') }}" onclick="closeMobileMenu()">Contact</a>
            <a class="btn green" href="{{ $dashboardRoute }}" onclick="closeMobileMenu()">{{ auth()->check() ? 'Dashboard' : 'Login Now' }}</a>
        </nav>
    </header>
    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        function toggleMobileMenu() {
            hamburgerBtn.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
        }

        function closeMobileMenu() {
            hamburgerBtn.classList.remove('active');
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        }

        hamburgerBtn.addEventListener('click', toggleMobileMenu);

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.site-nav') && mobileMenu.classList.contains('active')) {
                closeMobileMenu();
            }
        });

        // Close menu on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 900) {
                closeMobileMenu();
            }
        });

    </script>

    <script>
        // Lightweight reveal-on-scroll and on-load animations
        (function () {
            const selectors = [
                '.hero-content', '.hero-strip', '.section-head', '.card', '.image-card', '.feature-item', '.testimonial', '.step-badge', '.feature-grid > *', '.stat-item', '.cta-banner'
            ].join(', ');

            let nodes = [];
            let observer;

            function initReveal() {
                nodes = Array.from(document.querySelectorAll(selectors));

                if (!nodes.length) {
                    return;
                }

                nodes.forEach(el => el.classList.add('reveal'));

                observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const el = entry.target;
                            const delay = el.dataset.delay ? Number(el.dataset.delay) : 0;
                            setTimeout(() => el.classList.add('visible'), delay);
                            observer.unobserve(el);
                        }
                    });
                }, { root: null, rootMargin: '0px 0px -12% 0px', threshold: 0.08 });

                nodes.forEach(el => observer.observe(el));
                revealInitial();
            }

            function revealInitial() {
                const initial = nodes.filter(el => el.getBoundingClientRect().top < window.innerHeight * 0.94);
                initial.forEach((el, i) => {
                    const delay = el.dataset.delay ? Number(el.dataset.delay) : i * 50;
                    setTimeout(() => el.classList.add('visible'), delay);
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(initReveal, 80);

                const rotatingIcons = Array.from(document.querySelectorAll('.rotating-icon'));

                rotatingIcons.forEach(icon => {
                    const frames = icon.dataset.icons?.split(',')?.map(item => item.trim()).filter(Boolean) || [];
                    if (!frames.length) {
                        return;
                    }

                    let index = 0;
                    setInterval(() => {
                        index = (index + 1) % frames.length;
                        icon.textContent = frames[index];
                    }, 2000);
                });
            });
        })();
    </script>

    @yield('content')

    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <a class="brand" href="{{ route('site.home') }}"><span class="brand-mark">F</span><span>Fitzone<br><small>Smart Gym</small></span></a>
                <p>Smart gym management for training, payments, attendance, and member engagement.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Instagram">i</a>
                    <a href="#" aria-label="Twitter">t</a>
                    <a href="#" aria-label="LinkedIn">l</a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a class="link-item" href="{{ route('site.home') }}">Home</a>
                <a class="link-item" href="{{ route('site.services') }}">Services</a>
                <a class="link-item" href="{{ route('site.memberships') }}">Memberships</a>
                <a class="link-item" href="{{ route('site.trainers') }}">Trainers</a>
            </div>
            <div class="footer-section">
                <h3>Support</h3>
                <a class="link-item" href="{{ route('site.about') }}">About</a>
                <a class="link-item" href="{{ route('site.contact') }}">Contact</a>
                <a class="link-item" href="mailto:fitzone@gmail.com">Email Us</a>
                <a class="link-item" href="#">FAQs</a>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>+254746899732<br>Fitzone@gmail.com<br>123 Fitness Street, Nairobi, Kenya</p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-bottom-claim">
                <div class="footer-badge">✓</div>
                <div>
                    <strong>Your data is safe with us.</strong>
                    <p>We value your privacy and security.</p>
                </div>
            </div>
            <p class="footer-copyright">&copy; 2026 Fitzone Gym. All rights reserved. | Smart gym platform built for modern fitness clubs.</p>
            <div class="footer-payments">
                <span class="payment-card">VISA</span>
                <span class="payment-card">Mastercard</span>
                <span class="payment-card">M-PESA</span>
            </div>
        </div>
    </footer>
</body>
</html>
