@extends('site.layout')

@section('title', 'Services - Fitzone Gym')

@section('content')
<header class="page-title">
    <video class="hero-video" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=1600&q=80">
        <source src="https://videos.pexels.com/video-files/5528012/5528012-hd_1080_1920_25fps.mp4" type="video/mp4">
    </video>
    <div class="section-inner">
        <h1>Our Services</h1>
        <p>Everything you need for fitness success: training, nutrition, payments, attendance, cafe, and member support all in one intelligent platform.</p>
    </div>
</header>

<section>
    <div class="section-inner">
        <h2 style="margin-bottom: 40px;">Comprehensive Fitness Services</h2>
        <div class="grid three">
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #dbeafe, #bfdbfe); color:var(--blue)">🏋️</div>
                <h3>Gym Access</h3>
                <p class="muted">24/7 access to strength floor, cardio equipment, conditioning zones, and flexible membership packages from daily to monthly.</p>
                <ul class="benefits-list" style="margin-top:12px; font-size:13px;">
                    <li>Unlimited gym floor access</li>
                    <li>Modern equipment & facilities</li>
                    <li>Flexible membership durations</li>
                </ul>
            </article>
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #dcfce7, #bbf7d0); color:var(--green)">👨‍🏫</div>
                <h3>Personal Training</h3>
                <p class="muted">Choose your trainer during signup and maintain ongoing communication. Trainers create custom workouts, provide form guidance, and track your progress.</p>
                <ul class="benefits-list" style="margin-top:12px; font-size:13px;">
                    <li>Expert trainer selection</li>
                    <li>Custom workout plans</li>
                    <li>Direct communication</li>
                </ul>
            </article>
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #ffedd5, #fed7aa); color:var(--amber)">🍽️</div>
                <h3>Nutrition & Diet Plans</h3>
                <p class="muted">Premium members unlock personalized diet plans and meal guidance customized to your fitness goals and dietary preferences.</p>
                <ul class="benefits-list" style="margin-top:12px; font-size:13px;">
                    <li>Custom meal plans</li>
                    <li>Nutritionist guidance</li>
                    <li>Goal-based recommendations</li>
                </ul>
            </article>
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #ede9fe, #ddd6fe); color:var(--blue)">📱</div>
                <h3>Smart Attendance</h3>
                <p class="muted">QR code scanning and trainer-managed check-ins provide accurate activity records. Track visits, consistency, and participation history anytime.</p>
                <ul class="benefits-list" style="margin-top:12px; font-size:13px;">
                    <li>QR code check-in</li>
                    <li>Manual attendance tracking</li>
                    <li>Visit history & reports</li>
                </ul>
            </article>
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #fecaca, #fca5a5); color:var(--red)">☕</div>
                <h3>Smart Cafe System</h3>
                <p class="muted">Browse our cafe menu, order meals and supplements with your wallet balance or M-PESA. Quick checkout, fast delivery to your location.</p>
                <ul class="benefits-list" style="margin-top:12px; font-size:13px;">
                    <li>Digital menu browsing</li>
                    <li>Quick ordering</li>
                    <li>Multiple payment options</li>
                </ul>
            </article>
            <article class="card stat-card">
                <div class="icon" style="background:linear-gradient(135deg, #86efac, #4ade80); color:var(--green)">📄</div>
                <h3>OCR Paperwork</h3>
                <p class="muted">Upload paper attendance records, forms, and fitness documents. AI-powered OCR extracts text automatically for digital record management.</p>
                <ul class="benefits-list" style="margin-top:12px; font-size:13px;">
                    <li>Document scanning</li>
                    <li>AI text extraction</li>
                    <li>Digital archiving</li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <h2 style="margin-bottom: 40px;">Payment & Membership</h2>
        <div class="grid two">
            <article class="card">
                <h3 style="display:flex; gap:10px; align-items:center;">💳 Flexible Payment Options</h3>
                <p class="muted">Choose the payment method that works best for you.</p>
                <ul class="benefits-list">
                    <li><strong>M-PESA:</strong> Instant STK push activation</li>
                    <li><strong>PayPal:</strong> Secure international payments</li>
                    <li><strong>Card:</strong> Visa, Mastercard instant processing</li>
                    <li><strong>Bank Transfer:</strong> Direct account payment</li>
                    <li><strong>Cash:</strong> Manual approval by admin</li>
                </ul>
            </article>
            <article class="card">
                <h3 style="display:flex; gap:10px; align-items:center;">📦 Membership Packages</h3>
                <p class="muted">Choose the plan that fits your commitment level.</p>
                <ul class="benefits-list">
                    <li><strong>Daily Pass:</strong> Perfect for single sessions</li>
                    <li><strong>Weekly Plan:</strong> Popular for consistency</li>
                    <li><strong>Monthly Package:</strong> Best value for progress</li>
                    <li><strong>Trainer Selection:</strong> Add coaching to any plan</li>
                    <li><strong>Quick Renewal:</strong> Auto or manual re-ups</li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <h2 style="margin-bottom: 40px;">Member Support & Community</h2>
        <div class="grid two">
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #dbeafe, #bfdbfe); color:var(--blue)">💬</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Direct Trainer Chat</h3>
                        <p class="muted">Message your assigned trainer anytime with questions about your workout or nutrition.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #dcfce7, #bbf7d0); color:var(--green)">📞</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Audio/Video Calls</h3>
                        <p class="muted">Schedule consultation calls with trainers for form review and personalized guidance.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #ffedd5, #fed7aa); color:var(--amber)">📊</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Progress Tracking</h3>
                        <p class="muted">Log workouts, weight changes, and measurements to visualize your fitness journey.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #ede9fe, #ddd6fe); color:var(--blue)">🤝</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Community Access</h3>
                        <p class="muted">Connect with other members, share wins, and stay motivated as part of the Fitzone family.</p>
                    </div>
                </div>
            </div>
            <article class="card">
                <h3>Why Members Love Fitzone</h3>
                <blockquote class="testimonial">
                    <p><strong>"Everything is clear."</strong> I know exactly when my membership ends, when payments are approved, and what my trainer is planning. No surprises.</p>
                    <div class="author">— Alex P.</div>
                </blockquote>
                <blockquote class="testimonial">
                    <p><strong>"M-PESA makes it easy."</strong> Payment goes through instantly and I get immediate access. Much better than waiting for approvals.</p>
                    <div class="author">— Chris N.</div>
                </blockquote>
                <blockquote class="testimonial">
                    <p><strong>"Best trainer experience."</strong> I can text my trainer whenever, get custom workouts, and track my progress all in one app.</p>
                    <div class="author">— Diana M.</div>
                </blockquote>
            </article>
        </div>
    </div>
</section>

<div class="cta-banner">
    <h2>Start Using Fitzone Today</h2>
    <p>Access all services immediately after payment confirmation and begin your fitness transformation.</p>
    <div class="hero-actions" style="justify-content: center;">
        <a class="btn" style="background:#fff; color:var(--blue)" href="{{ auth()->check() ? route('client.packages') : route('login') }}">Choose a Plan</a>
        <a class="btn ghost" style="background:rgba(255,255,255,.2); color:#fff" href="{{ route('site.memberships') }}">View Pricing</a>
    </div>
</div>
@endsection
