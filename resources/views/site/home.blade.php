@extends('site.layout')

@section('title', 'Ironside - Your Goals. Our Mission.')

@section('content')
<main class="hero">
    <video class="hero-video" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1800&q=80">
        <source src="https://videos.pexels.com/video-files/5528012/5528012-hd_1080_1920_25fps.mp4" type="video/mp4">
    </video>
    <div class="hero-inner">
        <div class="hero-content">
            <span class="eyebrow">🏋️ No Excuses. Just Results.</span>
            <h1>Your Goals. Our Mission.</h1>
            <p>Personalized coaching designed around your goals, lifestyle, and fitness level. Start anytime, train with purpose, and build a stronger tomorrow.</p>
            <div class="hero-actions">
                <a class="btn green" href="{{ auth()->check() ? route('client.dashboard') : route('login') }}">{{ auth()->check() ? 'Open Your Dashboard' : 'Start Your Transformation' }}</a>
                <a class="btn ghost" href="{{ route('site.memberships') }}">View Training Plans</a>
            </div>
            <div class="hero-strip">
                <div><strong>1-ON-1</strong><span>personal coaching</span></div>
                <div><strong>ALL LEVELS</strong><span>welcome to train</span></div>
                <div><strong>REAL</strong><span>progress and results</span></div>
            </div>
        </div>
    </div>
</main>

<section>
    <div class="section-inner">
        <div class="section-head">
            <div>
                <span class="badge">Personal Fitness Training</span>
                <h2>Consistency today. Stronger tomorrow.</h2>
            </div>
            <p>One-on-one coaching, custom workouts, nutrition guidance, progress tracking, and support built around the person you want to become.</p>
        </div>
        <div class="grid three">
            <article class="card stat-card">
                <div class="icon rotating-icon" data-icons="📦,🎒,💼" style="background:linear-gradient(135deg, #dbeafe, #bfdbfe); color:var(--blue)">📦</div>
                <h3>Custom Workouts</h3>
                <p class="muted">Personalized plans built around your goals, current fitness level, and lifestyle.</p>
            </article>
            <article class="card stat-card">
                <div class="icon rotating-icon" data-icons="✅,✔️,🟢" style="background:linear-gradient(135deg, #dcfce7, #bbf7d0); color:var(--green)">✅</div>
                <h3>Progress Tracking</h3>
                <p class="muted">Stay accountable with clear milestones, regular support, and visible progress over time.</p>
            </article>
            <article class="card stat-card">
                <div class="icon rotating-icon" data-icons="🔓,🔑,🛡️" style="background:linear-gradient(135deg, #ffedd5, #fed7aa); color:var(--amber)">🔓</div>
                <h3>Real Results</h3>
                <p class="muted">Train for weight loss, muscle building, conditioning, endurance, mobility, and lasting confidence.</p>
            </article>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <div class="section-head">
            <div>
                <span class="badge green">What I Offer</span>
                <h2>Training that meets you where you are.</h2>
            </div>
            <p>Choose focused coaching for every fitness level, whether your goal is fat loss, strength, fitness, or high performance.</p>
        </div>
        <div class="grid three">
            <article class="card image-card">
                <iframe class="video-frame" src="https://www.youtube.com/embed/4DB6910HGr4?autoplay=1&mute=1&loop=1&playlist=4DB6910HGr4&controls=0&rel=0&modestbranding=1&playsinline=1" allow="autoplay; encrypted-media" style="width:100%; height:260px; border:0; background:transparent; pointer-events:none;"></iframe>
                <div>
                    <h3>💪 Muscle Building</h3>
                    <p class="muted">Strength training, body conditioning, and progressive workouts for measurable gains.</p>
                </div>
            </article>
            <article class="card image-card">
                <iframe class="video-frame" src="https://www.youtube.com/embed/VqXLFffiU2I?autoplay=1&mute=1&loop=1&playlist=VqXLFffiU2I&controls=0&rel=0&modestbranding=1&playsinline=1" allow="autoplay; encrypted-media" style="width:100%; height:260px; border:0; background:transparent; pointer-events:none;"></iframe>
                <div>
                    <h3>🤸 Fitness & Conditioning</h3>
                    <p class="muted">Cardio, HIIT, aerobics, endurance, mobility, and core training for a stronger body.</p>
                </div>
            </article>
            <article class="card image-card">
                <iframe class="video-frame" src="https://www.youtube.com/embed/TbY_5mOBZlU?autoplay=1&mute=1&loop=1&playlist=TbY_5mOBZlU&controls=0&rel=0&modestbranding=1&playsinline=1" allow="autoplay; encrypted-media" style="width:100%; height:260px; border:0; background:transparent; pointer-events:none;"></iframe>
                <div>
                    <h3>👨‍🏫 1-on-1 Coaching</h3>
                    <p class="muted">Personal guidance, lifestyle support, and a plan that moves with your progress.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <div class="section-head">
            <h2 style="margin-bottom: 28px;">Your Path to Real Results</h2>
        </div>
        <div class="steps-grid">
            <div class="card" style="text-align: center; padding: 30px 16px;">
                <div class="step-badge" style="margin: 0 auto 12px;">1</div>
                <h3 style="margin: 0 0 8px;">Set Your Goal</h3>
                <p class="muted" style="font-size: 13px;">Tell us what you want to change and where you want to go.</p>
            </div>
            <div class="arrow">→</div>
            <div class="card" style="text-align: center; padding: 30px 16px;">
                <div class="step-badge" style="margin: 0 auto 12px;">2</div>
                <h3 style="margin: 0 0 8px;">Get Your Plan</h3>
                <p class="muted" style="font-size: 13px;">Receive a customized workout and nutrition approach for your level.</p>
            </div>
            <div class="arrow">→</div>
            <div class="card" style="text-align: center; padding: 30px 16px;">
                <div class="step-badge" style="margin: 0 auto 12px;">3</div>
                <h3 style="margin: 0 0 8px;">Commit Today</h3>
                <p class="muted" style="font-size: 13px;">Start anytime with flexible schedules that fit your lifestyle.</p>
            </div>
        </div>
        <div style="text-align: center; margin-top: 16px;">
            <div class="card" style="text-align: center; padding: 30px 16px; display: inline-block;">
                <div class="step-badge" style="margin: 0 auto 12px;">4</div>
                <h3 style="margin: 0 0 8px;">Transform Forever</h3>
                <p class="muted" style="font-size: 13px; margin: 0;">Train consistently, track your progress, and become stronger tomorrow.</p>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <div class="section-head">
            <h2>Everything You Need to Succeed</h2>
            <p>Support, structure, and guidance for every step of your transformation.</p>
        </div>
        <div class="grid two">
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon">📱</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Personalized Plans</h3>
                        <p class="muted">Beginner-friendly or high-performance plans tailored to your assessment.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon">💬</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Coaching Support</h3>
                        <p class="muted">Stay connected with guidance, encouragement, and accountability when you need it.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon">🍽️</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Nutrition Guidance</h3>
                        <p class="muted">Build better habits with practical nutrition and lifestyle direction.</p>
                    </div>
                </div>
            </div>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon">📊</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Weight Loss</h3>
                        <p class="muted">Fat-loss workouts, cardio, HIIT, and sustainable lifestyle support.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon">🎯</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Muscle & Strength</h3>
                        <p class="muted">Progressive training for power, strength, body conditioning, and endurance.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon">🔐</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Flexible Schedules</h3>
                        <p class="muted">Train at home, in the gym, or outdoors with a schedule that works for you.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <div class="section-head">
            <h2>Real People. Real Progress.</h2>
        </div>
        <div class="grid three">
            <article class="card testimonial">
                <p><strong>"I finally feel consistent."</strong> The plan fits my lifestyle, the support keeps me accountable, and I can see the difference.</p>
                <div class="author">— Sarah M., Client</div>
            </article>
            <article class="card testimonial">
                <p><strong>"The workouts make sense."</strong> Every session has a purpose, and the coaching helps me keep improving without guesswork.</p>
                <div class="author">— James K., Client</div>
            </article>
            <article class="card testimonial">
                <p><strong>"I am stronger than before."</strong> The combination of training, nutrition, and progress tracking keeps me moving forward.</p>
                <div class="author">— Mary N., Client</div>
            </article>
        </div>
    </div>
</section>

<div class="cta-banner">
    <h2>Ready to Transform Your Fitness?</h2>
    <p>Start your first session today. No excuses, just progress, expert coaching, and real results.</p>
    <div class="hero-actions" style="justify-content: center;">
        <a class="btn" style="background:#fff; color:var(--blue)" href="{{ auth()->check() ? route('client.dashboard') : route('login') }}">Book Your First Session</a>
        <a class="btn ghost" style="background:rgba(255,255,255,.2); color:#fff" href="{{ route('site.contact') }}">Call / WhatsApp</a>
    </div>
</div>
@endsection
