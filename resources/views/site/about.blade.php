@extends('site.layout')

@section('title', 'About Fitzone - Modern Gym Management')

@section('content')
<header class="page-title">
    <video class="hero-video" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1534367610401-9f5ed68180aa?auto=format&fit=crop&w=1600&q=80">
        <source src="https://videos.pexels.com/video-files/5528012/5528012-hd_1080_1920_25fps.mp4" type="video/mp4">
    </video>
    <div class="section-inner">
        <h1>About Fitzone</h1>
        <p>A modern gym experience built for members who want training, payments, attendance, nutrition, and communication all in one intelligent platform.</p>
    </div>
</header>

<section>
    <div class="section-inner grid two">
        <article class="card">
            <span class="badge green">Our Mission</span>
            <h2>Fitness without operational confusion.</h2>
            <p class="muted">Fitzone combines a serious training environment with a smart dashboard that keeps members, trainers, and admins perfectly aligned. No hidden payment statuses, no unclear approvals, no lost receipts.</p>
            <ul class="benefits-list">
                <li>Clear payment tracking and history</li>
                <li>Real-time member status updates</li>
                <li>Instant M-PESA processing</li>
                <li>Automated membership activation</li>
            </ul>
        </article>
        <article class="card image-card">
            <img src="https://images.unsplash.com/photo-1534367610401-9f5ed68180aa?auto=format&fit=crop&w=1000&q=80" alt="Gym interior">
            <div>
                <h3>Designed for Daily Discipline</h3>
                <p class="muted">From first visit to renewal, Fitzone's system supports the real habits that keep members consistent, motivated, and accountable.</p>
            </div>
        </article>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">Our Core Values</h2>
        <div class="grid three">
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #dbeafe, #bfdbfe); color:var(--blue)">🎯</div>
                <h3>Clarity First</h3>
                <p class="muted">Every member sees their status, payments, workouts, and progress. No confusion, no surprises.</p>
            </article>
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #dcfce7, #bbf7d0); color:var(--green)">⚡</div>
                <h3>Speed & Efficiency</h3>
                <p class="muted">M-PESA processes instantly, activations happen automatically, and member access opens immediately after payment.</p>
            </article>
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #ffedd5, #fed7aa); color:var(--amber)">🤝</div>
                <h3>Community Support</h3>
                <p class="muted">Direct chat with trainers, approachable admin, and a fitness family that keeps you accountable and motivated.</p>
            </article>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <h2 style="margin-bottom: 40px;">Why Fitzone?</h2>
        <div class="grid two">
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Payment-First Access</h3>
                        <p class="muted">Nothing unlocks until payment is confirmed by instant M-PESA callback or staff approval.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Trainer Accountability</h3>
                        <p class="muted">Assigned trainers see their clients' workouts, track progress, and maintain direct communication.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Attendance Visibility</h3>
                        <p class="muted">Members and staff follow participation through QR codes and manual tracking for complete records.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Diet & Nutrition</h3>
                        <p class="muted">Personalized meal plans and nutrition guidance tailored to your fitness goals and preferences.</p>
                    </div>
                </div>
            </div>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Smart Cafe System</h3>
                        <p class="muted">Browse menu, order meals, and pay with member wallet balance or M-PESA instantly.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Multiple Payment Options</h3>
                        <p class="muted">M-PESA instant, PayPal, card payments, bank transfers, and approved cash options.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">OCR Document Processing</h3>
                        <p class="muted">Upload paper records and fitness documents for automatic AI-powered text extraction.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Mobile-First Design</h3>
                        <p class="muted">Responsive, fast, and intuitive interface optimized for smartphones and tablets.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <div class="stats-section">
            <div class="stat-item">
                <strong>24/7</strong>
                <p>Platform Availability</p>
            </div>
            <div class="stat-item">
                <strong>∞</strong>
                <p>Members Supported</p>
            </div>
            <div class="stat-item">
                <strong>5+</strong>
                <p>Payment Methods</p>
            </div>
            <div class="stat-item">
                <strong>100%</strong>
                <p>Secure & Encrypted</p>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">Meet the Team Behind Fitzone</h2>
        <div class="grid three">
            <article class="card" style="text-align:center">
                <div style="width:80px; height:80px; background:linear-gradient(135deg, var(--blue-deep), var(--blue)); border-radius:50%; margin:0 auto 16px;"></div>
                <h3>Fitness Experts</h3>
                <p class="muted">Real trainers and gym managers who understand member needs and operations.</p>
            </article>
            <article class="card" style="text-align:center">
                <div style="width:80px; height:80px; background:linear-gradient(135deg, #162943, var(--blue)); border-radius:50%; margin:0 auto 16px;"></div>
                <h3>Tech Innovators</h3>
                <p class="muted">Developers passionate about creating tools that solve real gym management challenges.</p>
            </article>
            <article class="card" style="text-align:center">
                <div style="width:80px; height:80px; background:linear-gradient(135deg, var(--blue), #3b82f6); border-radius:50%; margin:0 auto 16px;"></div>
                <h3>Support Heroes</h3>
                <p class="muted">Customer success team ready to help your gym maximize Fitzone's potential daily.</p>
            </article>
        </div>
    </div>
</section>

<div class="cta-banner">
    <h2>Join the Fitzone Community Today</h2>
    <p>Experience the future of gym management with smart memberships, instant payments, and member engagement.</p>
    <div class="hero-actions" style="justify-content: center;">
        <a class="btn" style="background:#fff; color:var(--blue)" href="{{ auth()->check() ? route('client.dashboard') : route('login') }}">Get Started</a>
        <a class="btn ghost" style="background:rgba(255,255,255,.2); color:#fff" href="{{ route('site.contact') }}">Contact Sales</a>
    </div>
</div>
@endsection
