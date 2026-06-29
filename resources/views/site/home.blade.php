@extends('site.layout')

@section('title', 'Fitzone Gym - Train Smarter, Pay Faster')

@section('content')
<main class="hero">
    <video class="hero-video" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1800&q=80">
        <source src="https://videos.pexels.com/video-files/5528012/5528012-hd_1080_1920_25fps.mp4" type="video/mp4">
    </video>
    <div class="hero-inner">
        <div class="hero-content">
            <span class="eyebrow">🏋️ Nairobi's Smart Fitness Club</span>
            <h1>Train Hard. Pay Fast. Track Everything.</h1>
            <p>Fitzone brings gym membership, trainers, workouts, diet plans, attendance tracking, cafe orders, M-PESA checkout, and payment approvals into one polished member experience.</p>
            <div class="hero-actions">
                <a class="btn green" href="{{ auth()->check() ? route('client.dashboard') : route('login') }}">{{ auth()->check() ? 'Go to Dashboard' : 'Get Started Now' }}</a>
                <a class="btn ghost" href="{{ route('site.memberships') }}">View Memberships</a>
            </div>
            <div class="hero-strip">
                <div><strong>24/7</strong><span>digital member access</span></div>
                <div><strong>M-PESA</strong><span>instant STK checkout</span></div>
                <div><strong>AI/OCR</strong><span>smart document import</span></div>
            </div>
        </div>
    </div>
</main>

<section>
    <div class="section-inner">
        <div class="section-head">
            <div>
                <span class="badge">Why Choose Fitzone</span>
                <h2>A gym that feels organized from the first tap.</h2>
            </div>
            <p>Members get a clear path: choose a plan, pick a trainer, pay with M-PESA, unlock workouts, track attendance, and stay connected with support.</p>
        </div>
        <div class="grid three">
            <article class="card stat-card">
                <div class="icon rotating-icon" data-icons="📦,🎒,💼" style="background:linear-gradient(135deg, #dbeafe, #bfdbfe); color:var(--blue)">📦</div>
                <h3>Simple Onboarding</h3>
                <p class="muted">Package selection → Trainer choice → Payment → Instant activation. No confusion, no delays.</p>
            </article>
            <article class="card stat-card">
                <div class="icon rotating-icon" data-icons="✅,✔️,🟢" style="background:linear-gradient(135deg, #dcfce7, #bbf7d0); color:var(--green)">✅</div>
                <h3>Verified Payments</h3>
                <p class="muted">M-PESA auto-confirms instantly. Cash, bank, PayPal, and card payments wait for admin approval.</p>
            </article>
            <article class="card stat-card">
                <div class="icon rotating-icon" data-icons="🔓,🔑,🛡️" style="background:linear-gradient(135deg, #ffedd5, #fed7aa); color:var(--amber)">🔓</div>
                <h3>Instant Unlocks</h3>
                <p class="muted">After payment confirmation, workouts, diet plans, chat, attendance, and member areas activate automatically.</p>
            </article>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <div class="section-head">
            <div>
                <span class="badge green">Inside the Club</span>
                <h2>Built for real gym operations.</h2>
            </div>
            <p>From membership management to daily operations, Fitzone handles the details so you focus on training.</p>
        </div>
        <div class="grid three">
            <article class="card image-card">
                <iframe class="video-frame" src="https://www.youtube.com/embed/4DB6910HGr4?autoplay=1&mute=1&loop=1&playlist=4DB6910HGr4&controls=0&rel=0&modestbranding=1&playsinline=1" allow="autoplay; encrypted-media" style="width:100%; height:260px; border:0; background:transparent; pointer-events:none;"></iframe>
                <div>
                    <h3>💪 Strength Floor</h3>
                    <p class="muted">Free weights, machines, and coach-led routines designed for every experience level.</p>
                </div>
            </article>
            <article class="card image-card">
                <iframe class="video-frame" src="https://www.youtube.com/embed/VqXLFffiU2I?autoplay=1&mute=1&loop=1&playlist=VqXLFffiU2I&controls=0&rel=0&modestbranding=1&playsinline=1" allow="autoplay; encrypted-media" style="width:100%; height:260px; border:0; background:transparent; pointer-events:none;"></iframe>
                <div>
                    <h3>🤸 Fitness Classes</h3>
                    <p class="muted">Conditioning, mobility, core work, and focused group sessions daily.</p>
                </div>
            </article>
            <article class="card image-card">
                <iframe class="video-frame" src="https://www.youtube.com/embed/TbY_5mOBZlU?autoplay=1&mute=1&loop=1&playlist=TbY_5mOBZlU&controls=0&rel=0&modestbranding=1&playsinline=1" allow="autoplay; encrypted-media" style="width:100%; height:260px; border:0; background:transparent; pointer-events:none;"></iframe>
                <div>
                    <h3>👨‍🏫 Personal Training</h3>
                    <p class="muted">Choose your trainer during signup and maintain direct communication anytime.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <div class="section-head">
            <h2 style="margin-bottom: 28px;">How Fitzone Works</h2>
        </div>
        <div class="steps-grid">
            <div class="card" style="text-align: center; padding: 30px 16px;">
                <div class="step-badge" style="margin: 0 auto 12px;">1</div>
                <h3 style="margin: 0 0 8px;">Sign Up</h3>
                <p class="muted" style="font-size: 13px;">Create your account with email or phone number.</p>
            </div>
            <div class="arrow">→</div>
            <div class="card" style="text-align: center; padding: 30px 16px;">
                <div class="step-badge" style="margin: 0 auto 12px;">2</div>
                <h3 style="margin: 0 0 8px;">Choose Plan</h3>
                <p class="muted" style="font-size: 13px;">Pick daily, weekly, or monthly membership packages.</p>
            </div>
            <div class="arrow">→</div>
            <div class="card" style="text-align: center; padding: 30px 16px;">
                <div class="step-badge" style="margin: 0 auto 12px;">3</div>
                <h3 style="margin: 0 0 8px;">Pay Now</h3>
                <p class="muted" style="font-size: 13px;">M-PESA instant or manual payment approval.</p>
            </div>
        </div>
        <div style="text-align: center; margin-top: 16px;">
            <div class="card" style="text-align: center; padding: 30px 16px; display: inline-block;">
                <div class="step-badge" style="margin: 0 auto 12px;">4</div>
                <h3 style="margin: 0 0 8px;">Start Training</h3>
                <p class="muted" style="font-size: 13px; margin: 0;">Access workouts, diet plans, attendance, cafe, and trainer support.</p>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <div class="section-head">
            <h2>Complete Member Benefits</h2>
            <p>Everything needed for fitness success in one platform.</p>
        </div>
        <div class="grid two">
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon">📱</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Member Dashboard</h3>
                        <p class="muted">Real-time access to membership, workouts, attendance, and payment history.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon">💬</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Chat & Calls</h3>
                        <p class="muted">Direct communication with trainers, coaches, and Fitzone admin team.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon">🍽️</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Smart Cafe</h3>
                        <p class="muted">Browse and order meals, shakes, and gym supplements with wallet balance.</p>
                    </div>
                </div>
            </div>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon">📊</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Progress Tracking</h3>
                        <p class="muted">Monitor attendance, weight changes, and workout consistency over time.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon">🎯</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Personalized Diet</h3>
                        <p class="muted">Nutrition guidance and meal plans customized to your fitness goals.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon">🔐</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Secure Payments</h3>
                        <p class="muted">M-PESA, PayPal, card, bank transfer, and approved cash options available.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <div class="section-head">
            <h2>What Members Say</h2>
        </div>
        <div class="grid three">
            <article class="card testimonial">
                <p><strong>"Best gym app I've used."</strong> The onboarding is super clear, M-PESA works instantly, and I can track everything. Highly recommend.</p>
                <div class="author">— Sarah M., Member</div>
            </article>
            <article class="card testimonial">
                <p><strong>"Makes management easy."</strong> As a trainer, I can see my clients' workouts, chat with them, and stay organized without paperwork.</p>
                <div class="author">— James K., Trainer</div>
            </article>
            <article class="card testimonial">
                <p><strong>"Payment approval is seamless."</strong> M-PESA is instant, cash options are transparent, and the system handles everything automatically. Love it!</p>
                <div class="author">— Mary N., Admin</div>
            </article>
        </div>
    </div>
</section>

<div class="cta-banner">
    <h2>Ready to transform your fitness journey?</h2>
    <p>Join Fitzone today and unlock access to training, expert coaches, nutrition guidance, and a supportive fitness community.</p>
    <div class="hero-actions" style="justify-content: center;">
        <a class="btn" style="background:#fff; color:var(--blue)" href="{{ auth()->check() ? route('client.dashboard') : route('login') }}">Get Started</a>
        <a class="btn ghost" style="background:rgba(255,255,255,.2); color:#fff" href="{{ route('site.contact') }}">Ask a Question</a>
    </div>
</div>
@endsection
