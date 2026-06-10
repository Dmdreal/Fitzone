@extends('site.layout')

@section('title', 'Our Trainers - Fitzone Gym')

@section('content')
<header class="page-title">
    <video class="hero-video" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1571019613914-85f342c6a11e?auto=format&fit=crop&w=1600&q=80">
        <source src="https://videos.pexels.com/video-files/5528012/5528012-hd_1080_1920_25fps.mp4" type="video/mp4">
    </video>
    <div class="section-inner">
        <h1>Expert Trainers</h1>
        <p>Get matched with trainers who specialize in your fitness goals. Choose support during signup and stay connected with direct chat and custom workouts.</p>
    </div>
</header>

<section>
    <div class="section-inner">
        <h2 style="margin-bottom: 40px;">Specializations & Coaching Styles</h2>
        <div class="grid three">
            <article class="card image-card">
                <img src="https://images.unsplash.com/photo-1594381898411-846e7d193883?auto=format&fit=crop&w=900&q=80" alt="Strength coach">
                <div>
                    <h3>💪 Strength & Powerlifting</h3>
                    <p class="muted">Expert coaches in compound lifting, form correction, progressive overload, and structured strength building.</p>
                    <ul class="benefits-list" style="margin: 12px 0; font-size: 13px;">
                        <li>Barbell technique</li>
                        <li>Program design</li>
                        <li>Progressive periodization</li>
                    </ul>
                </div>
            </article>
            `  <article class="card image-card">
                <img src="https://images.unsplash.com/photo-1549476464-37392f717541?auto=format&fit=crop&w=900&q=80" alt="Conditioning trainer">
                <div>
                    <h3>⚡ Conditioning & HIIT</h3>
                    <p class="muted">Energetic trainers specializing in cardio, HIIT routines, fat loss, endurance, and explosive fitness improvements.</p>
                    <ul class="benefits-list" style="margin: 12px 0; font-size: 13px;">
                        <li>HIIT workouts</li>
                        <li>Cardio training</li>
                        <li>Fat loss strategies</li>
                    </ul>
                </div>
            </article>
            <article class="card image-card">
                <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=900&q=80" alt="Wellness coach">
                <div>
                    <h3>🧘 Wellness & Mobility</h3>
                    <p class="muted">Holistic coaches focused on nutrition habits, flexibility, recovery, mental health, and sustainable lifestyle changes.</p>
                    <ul class="benefits-list" style="margin: 12px 0; font-size: 13px;">
                        <li>Nutrition guidance</li>
                        <li>Mobility & yoga</li>
                        <li>Habit building</li>
                    </ul>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">How Trainer Selection Works</h2>
        <div style="max-width: 900px; margin: 0 auto;">
            <div class="grid" style="grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div class="card" style="text-align: center; padding: 24px;">
                    <div class="step-badge" style="margin: 0 auto 12px;">1</div>
                    <h3 style="margin: 0 0 8px; font-size: 16px;">Join</h3>
                    <p class="muted" style="font-size: 13px; margin: 0;">Create your member account.</p>
                </div>
                <div style="display:flex; align-items:center; justify-content:center; font-size:20px; color:var(--blue);">→</div>
                <div class="card" style="text-align: center; padding: 24px;">
                    <div class="step-badge" style="margin: 0 auto 12px;">2</div>
                    <h3 style="margin: 0 0 8px; font-size: 16px;">Choose</h3>
                    <p class="muted" style="font-size: 13px; margin: 0;">Select a trainer during checkout.</p>
                </div>
                <div style="display:flex; align-items:center; justify-content:center; font-size:20px; color:var(--blue);">→</div>
            </div>
            <div class="grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px;">
                <div class="card" style="text-align: center; padding: 24px;">
                    <div class="step-badge" style="margin: 0 auto 12px;">3</div>
                    <h3 style="margin: 0 0 8px; font-size: 16px;">Connect</h3>
                    <p class="muted" style="font-size: 13px; margin: 0;">Start chat with your trainer.</p>
                </div>
                <div style="display:flex; align-items:center; justify-content:center; font-size:20px; color:var(--blue);">→</div>
                <div class="card" style="text-align: center; padding: 24px;">
                    <div class="step-badge" style="margin: 0 auto 12px;">4</div>
                    <h3 style="margin: 0 0 8px; font-size: 16px;">Transform</h3>
                    <p class="muted" style="font-size: 13px; margin: 0;">Follow custom plans and progress.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">What Your Trainer Provides</h2>
        <div class="grid two">
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #dbeafe, #bfdbfe); color:var(--blue)">📋</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Custom Workout Plans</h3>
                        <p class="muted">Personalized programs based on your goals, experience level, and available equipment.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #dcfce7, #bbf7d0); color:var(--green)">💬</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Direct Communication</h3>
                        <p class="muted">Text your trainer anytime with form questions, schedule adjustments, or progress updates.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #ffedd5, #fed7aa); color:var(--amber)">📞</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Video Call Coaching</h3>
                        <p class="muted">Schedule calls for form review, technique correction, and real-time guidance during workouts.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #ede9fe, #ddd6fe); color:var(--blue)">📊</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Progress Tracking</h3>
                        <p class="muted">Trainers monitor your workouts, weight changes, and consistency for accountability.</p>
                    </div>
                </div>
            </div>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #fecaca, #fca5a5); color:var(--red)">🍎</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Nutrition Guidance</h3>
                        <p class="muted">Meal planning advice and nutrition tips aligned with your fitness goals and lifestyle.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #86efac, #4ade80); color:var(--green)">🎯</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Goal Setting</h3>
                        <p class="muted">Help define realistic milestones and create actionable plans to reach them faster.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #60a5fa, #3b82f6); color:var(--blue)">🏆</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Motivation & Accountability</h3>
                        <p class="muted">Regular check-ins, celebration of wins, and support during tough training weeks.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon" style="background:linear-gradient(135deg, #a78bfa, #7c3aed); color:var(--blue)">💪</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Program Adjustments</h3>
                        <p class="muted">Modify workouts based on your feedback, injuries, or evolving fitness goals.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">Why Work with a Trainer?</h2>
        <div class="stats-section">
            <div class="stat-item">
                <strong>300%</strong>
                <p>More consistent with a trainer</p>
            </div>
            <div class="stat-item">
                <strong>5x</strong>
                <p>Faster progress with guidance</p>
            </div>
            <div class="stat-item">
                <strong>0</strong>
                <p>Wasted workouts or wrong form</p>
            </div>
            <div class="stat-item">
                <strong>100%</strong>
                <p>Confidence in your routine</p>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">Member Success Stories</h2>
        <div class="grid three">
            <article class="card testimonial">
                <p><strong>"My trainer changed my entire routine."</strong> I didn't know proper form before. Now my lifts are stronger and I'm injury-free.</p>
                <div class="author">— Michael J., Strength Training</div>
            </article>
            <article class="card testimonial">
                <p><strong>"Accountability made all the difference."</strong> My trainer checks in, I don't want to disappoint them, and I show up consistently.</p>
                <div class="author">— Lisa M., Consistency Goal</div>
            </article>
            <article class="card testimonial">
                <p><strong>"Customized nutrition was key."</strong> Combining trainer workouts with their diet advice got me results in 8 weeks.</p>
                <div class="author">— David K., Fat Loss</div>
            </article>
        </div>
    </div>
</section>

<div class="cta-banner">
    <h2>Ready to Work with a Trainer?</h2>
    <p>Choose a trainer during your membership signup and start your transformation this week.</p>
    <div class="hero-actions" style="justify-content: center;">
        <a class="btn" style="background:#fff; color:var(--blue)" href="{{ auth()->check() ? route('client.packages') : route('login') }}">Choose Membership & Trainer</a>
        <a class="btn ghost" style="background:rgba(255,255,255,.2); color:#fff" href="{{ route('site.contact') }}">Ask About Trainers</a>
    </div>
</div>
@endsection
