@extends('site.layout')

@section('title', 'Memberships & Pricing - Fitzone Gym')

@section('content')
<header class="page-title">
    <video class="hero-video" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1605296867304-46d5465a13f1?auto=format&fit=crop&w=1600&q=80">
        <source src="https://videos.pexels.com/video-files/5528012/5528012-hd_1080_1920_25fps.mp4" type="video/mp4">
    </video>
    <div class="section-inner">
        <h1>Membership Plans</h1>
        <p>Choose a plan that fits your fitness commitment. All memberships unlock gym access, attendance tracking, chat support, and cafe ordering instantly after payment.</p>
    </div>
</header>

<section>
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">Flexible Plans for Every Lifestyle</h2>
        <div class="grid three">
           
            <article class="card" style="padding: 30px;">
                <span class="badge">Perfect for</span>
                <h3>Daily Pass</h3>
                <div class="price">KES 500</div>
                <p class="muted">24-hour gym access with full facilities. Great for trying us out or single sessions.</p>
                <ul class="benefits-list" style="margin: 18px 0; font-size: 13px;">
                    <li>24-hour access</li>
                    <li>Strength & cardio</li>
                    <li>Equipment access</li>
                    <li>QR check-in</li>
                </ul>
                <a class="btn ghost" style="width:100%; text-align:center; justify-content:center" href="{{ auth()->check() ? route('client.packages') : route('login') }}">Choose Daily</a>
            </article>
            <article class="card" style="padding: 30px; border: 2px solid var(--blue); box-shadow: 0 0 0 4px rgba(35,111,232,.14)">
                <span class="badge green">⭐ Most Popular</span>
                <h3>Weekly Plan</h3>
                <div class="price">KES 2,500</div>
                <p class="muted">7-day gym pass with trainer selection, workouts, and progress tracking included.</p>
                <ul class="benefits-list" style="margin: 18px 0; font-size: 13px;">
                    <li>7 days access</li>
                    <li>Select a trainer</li>
                    <li>Custom workouts</li>
                    <li>Progress tracking</li>
                    <li>Chat support</li>
                </ul>
                <a class="btn green" style="width:100%; text-align:center; justify-content:center" href="{{ auth()->check() ? route('client.packages') : route('login') }}">Choose Weekly</a>
            </article>
            <article class="card" style="padding: 30px;">
                <span class="badge amber">Best Value</span>
                <h3>Monthly Plan</h3>
                <div class="price">KES 8,000</div>
                <p class="muted">Premium 30-day membership with trainer, diet plan, advanced tracking, and cafe credits.</p>
                <ul class="benefits-list" style="margin: 18px 0; font-size: 13px;">
                    <li>30 days unlimited</li>
                    <li>Dedicated trainer</li>
                    <li>Diet plan access</li>
                    <li>Full features</li>
                    <li>Priority support</li>
                </ul>
                <a class="btn ghost" style="width:100%; text-align:center; justify-content:center" href="{{ auth()->check() ? route('client.packages') : route('login') }}">Choose Monthly</a>
            </article>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">What's Included in Every Plan</h2>
        <div class="grid two">
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Full Gym Access</h3>
                        <p class="muted">Strength floor, cardio, conditioning zones, and all equipment during available hours.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Member Dashboard</h3>
                        <p class="muted">Track workouts, attendance, progress, and payments all in one mobile-friendly interface.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Attendance Tracking</h3>
                        <p class="muted">QR code check-ins or manual logging for accurate activity records and consistency insights.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Chat Support</h3>
                        <p class="muted">Direct messaging with trainers and admin for questions, scheduling, and member support.</p>
                    </div>
                </div>
            </div>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Cafe Ordering</h3>
                        <p class="muted">Browse cafe menu and order meals, shakes, and supplements for pickup or delivery.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Payment History</h3>
                        <p class="muted">View all receipts, payment confirmations, and transaction details in your member portal.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Easy Renewal</h3>
                        <p class="muted">Quick re-signup for your next membership with saved payment methods and preferences.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="check">✓</div>
                    <div>
                        <h3 style="margin: 0 0 6px;">Community Access</h3>
                        <p class="muted">Connect with fellow members, share progress, and stay motivated in our fitness community.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">Plan Comparison</h2>
        <div class="card" style="overflow: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--line);">
                        <th style="text-align: left; padding: 16px; font-weight: 950;">Feature</th>
                        <th style="text-align: center; padding: 16px; font-weight: 950;">Daily</th>
                        <th style="text-align: center; padding: 16px; font-weight: 950; color: var(--blue);">Weekly</th>
                        <th style="text-align: center; padding: 16px; font-weight: 950;">Monthly</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px;">Duration</td>
                        <td style="text-align: center; padding: 12px 16px;">24 hours</td>
                        <td style="text-align: center; padding: 12px 16px; color: var(--blue); font-weight: 950;">7 days</td>
                        <td style="text-align: center; padding: 12px 16px;">30 days</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px;">Gym Access</td>
                        <td style="text-align: center; padding: 12px 16px;">✓</td>
                        <td style="text-align: center; padding: 12px 16px; font-weight: 950;">✓</td>
                        <td style="text-align: center; padding: 12px 16px;">✓</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px;">Trainer Selection</td>
                        <td style="text-align: center; padding: 12px 16px;">—</td>
                        <td style="text-align: center; padding: 12px 16px; font-weight: 950;">✓</td>
                        <td style="text-align: center; padding: 12px 16px;">✓</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px;">Custom Workouts</td>
                        <td style="text-align: center; padding: 12px 16px;">—</td>
                        <td style="text-align: center; padding: 12px 16px; font-weight: 950;">✓</td>
                        <td style="text-align: center; padding: 12px 16px;">✓</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px;">Diet Plan</td>
                        <td style="text-align: center; padding: 12px 16px;">—</td>
                        <td style="text-align: center; padding: 12px 16px;">—</td>
                        <td style="text-align: center; padding: 12px 16px;">✓</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px;">Progress Tracking</td>
                        <td style="text-align: center; padding: 12px 16px;">Basic</td>
                        <td style="text-align: center; padding: 12px 16px; font-weight: 950;">Advanced</td>
                        <td style="text-align: center; padding: 12px 16px;">Premium</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px;">Cafe Ordering</td>
                        <td style="text-align: center; padding: 12px 16px;">✓</td>
                        <td style="text-align: center; padding: 12px 16px; font-weight: 950;">✓</td>
                        <td style="text-align: center; padding: 12px 16px;">✓ + Credits</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 16px;">Price</td>
                        <td style="text-align: center; padding: 12px 16px; font-size: 18px; font-weight: 950;">KES 500</td>
                        <td style="text-align: center; padding: 12px 16px; font-size: 18px; font-weight: 950; color: var(--blue);">KES 2,500</td>
                        <td style="text-align: center; padding: 12px 16px; font-size: 18px; font-weight: 950;">KES 8,000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <h2 style="text-align:center; margin-bottom: 40px;">Payment Methods</h2>
        <div class="grid three">
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, #0f766e, #14b8a6); color:#fff; margin:0 auto">📱</div>
                <h3>M-PESA</h3>
                <p class="muted">Instant STK push. Membership activates immediately after payment confirmation.</p>
            </article>
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, #1e40af, #3b82f6); color:#fff; margin:0 auto">💳</div>
                <h3>Card Payment</h3>
                <p class="muted">Visa, Mastercard. Secure processing with instant confirmation and activation.</p>
            </article>
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, #5b21b6, #7c3aed); color:#fff; margin:0 auto">🏦</div>
                <h3>Bank Transfer</h3>
                <p class="muted">Direct account payment. Confirmation pending admin review (24-48 hours).</p>
            </article>
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, #0ea5e9, #06b6d4); color:#fff; margin:0 auto">🌐</div>
                <h3>PayPal</h3>
                <p class="muted">International payments welcome. Quick processing with instant membership access.</p>
            </article>
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, #dc2626, #ef4444); color:#fff; margin:0 auto">💰</div>
                <h3>Cash Payment</h3>
                <p class="muted">Pay at reception. Membership activates after admin verification (same day).</p>
            </article>
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, var(--blue), var(--green)); color:#fff; margin:0 auto">🔄</div>
                <h3>All Methods</h3>
                <p class="muted">Secure encryption for every transaction. Full receipt and payment history tracking.</p>
            </article>
        </div>
    </div>
</section>

<div class="cta-banner">
    <h2>Ready to Start Your Fitness Journey?</h2>
    <p>Join Fitzone today and get instant access to our gym, trainers, workouts, and supportive community.</p>
    <div class="hero-actions" style="justify-content: center;">
        <a class="btn" style="background:#fff; color:var(--blue)" href="{{ auth()->check() ? route('client.packages') : route('login') }}">Choose Your Plan</a>
        <a class="btn ghost" style="background:rgba(255,255,255,.2); color:#fff" href="{{ route('site.contact') }}">Ask Questions</a>
    </div>
</div>
@endsection
