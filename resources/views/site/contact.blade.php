@extends('site.layout')

@section('title', 'Contact Fitzone - Get in Touch')

@section('content')
<header class="page-title">
    <video class="hero-video" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1548690312-e3b507d8c110?auto=format&fit=crop&w=1600&q=80">
        <source src="https://videos.pexels.com/video-files/5528012/5528012-hd_1080_1920_25fps.mp4" type="video/mp4">
    </video>
    <div class="section-inner">
        <h1>Get in Touch</h1>
        <p>Questions about memberships, trainers, payments, or gym services? We're here to help and respond quickly.</p>
    </div>
</header>

<section>
    <div class="section-inner grid two">
        <article class="card">
            <h2 style="display: flex; gap: 10px; align-items: center;">📍 Visit Fitzone Gym</h2>
            <p class="muted" style="margin: 16px 0;">Fitzone Gym, Nairobi<br>Open Monday-Sunday for members and staff-managed training sessions.</p>
            
            <div class="grid" style="margin-top: 24px; gap: 20px;">
                <div>
                    <strong style="display: block; margin-bottom: 6px;">📞 Phone</strong>
                    <p class="muted" style="margin: 0;">+254 700 000 000<br><small>Available 7am-9pm daily</small></p>
                </div>
                <div>
                    <strong style="display: block; margin-bottom: 6px;">📧 Email</strong>
                    <p class="muted" style="margin: 0;">hello@fitzone.test<br><small>Responded within 2 hours</small></p>
                </div>
            </div>

            <div style="margin-top: 24px; padding: 16px; background: var(--soft); border-radius: 8px;">
                <strong style="display: block; margin-bottom: 8px;">💳 Payment Methods Available</strong>
                <p class="muted" style="font-size: 13px; margin: 0;">M-PESA, PayPal, Card (Visa/MC), Bank Transfer, and Cash Approval. All secure and encrypted.</p>
            </div>

            <div style="margin-top: 24px; padding: 16px; background: #dbeafe; border-radius: 8px; border-left: 4px solid var(--blue);">
                <strong style="display: block; margin-bottom: 8px; color: #1e40af;">✓ Quick Response Time</strong>
                <p class="muted" style="font-size: 13px; margin: 0; color: #1e40af;">Most inquiries answered within hours. Member support is our priority.</p>
            </div>
        </article>

        <article class="card contact-card">
            <h2>Send a Message</h2>
            @if (session('status'))
                <div style="padding: 12px; background: #dbeafe; border: 1px solid #93c5fd; border-radius: 6px; margin-bottom: 16px; color: #1e40af; font-size: 14px;">
                    ✓ {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('site.contact.store') }}" class="grid" style="margin-top: 16px;">
                @csrf
                <div class="form-grid">
                    <label>Full Name <input name="name" value="{{ old('name') }}" required placeholder="Enter your name"></label>
                    <label>Email <input type="email" name="email" value="{{ old('email') }}" required placeholder="your@email.com"></label>
                </div>
                <label>What's this about?
                    <select name="topic" style="margin-top: 7px;">
                        <option value="">Select a topic</option>
                        <option value="Membership">Membership Plans & Pricing</option>
                        <option value="M-PESA Payment">M-PESA Payment Help</option>
                        <option value="Trainer">Trainer Selection</option>
                        <option value="Corporate Plan">Corporate/Group Plans</option>
                        <option value="Technical">Technical Support</option>
                        <option value="Cafe">Cafe Menu & Orders</option>
                        <option value="Other">Other Question</option>
                    </select>
                </label>
                <label>Your Message <textarea name="message" required placeholder="Tell us what you'd like to know..." style="margin-top: 7px;">{{ old('message') }}</textarea></label>
                <button class="btn green" type="submit" style="width: 100%;">Send Message</button>
            </form>
            <p class="muted" style="font-size: 12px; margin: 12px 0 0;">We'll review your message and respond within 24 hours. Check your email for our response.</p>
        </article>
    </div>
</section>

<section class="soft">
    <div class="section-inner">
        <h2 style="text-align: center; margin-bottom: 40px;">Frequently Asked Questions</h2>
        <div class="grid two">
            <div style="display: grid; gap: 16px;">
                <article class="card">
                    <h3 style="margin: 0 0 8px;">How quickly does M-PESA activate?</h3>
                    <p class="muted" style="margin: 0; font-size: 14px;">M-PESA membership activates instantly after the STK is accepted and callback is received. No waiting time!</p>
                </article>
                <article class="card">
                    <h3 style="margin: 0 0 8px;">Can I change trainers?</h3>
                    <p class="muted" style="margin: 0; font-size: 14px;">Yes! You can switch trainers anytime by contacting admin. We want you matched with the best fit for your goals.</p>
                </article>
                <article class="card">
                    <h3 style="margin: 0 0 8px;">What if I need to pause my membership?</h3>
                    <p class="muted" style="margin: 0; font-size: 14px;">Contact us and we can pause for up to 30 days. Your membership will resume at the same level when ready.</p>
                </article>
                <article class="card">
                    <h3 style="margin: 0 0 8px;">Do I need a trainer?</h3>
                    <p class="muted" style="margin: 0; font-size: 14px;">No, trainers are optional. All memberships include gym access. Trainers enhance the experience but aren't required.</p>
                </article>
            </div>
            <div style="display: grid; gap: 16px;">
                <article class="card">
                    <h3 style="margin: 0 0 8px;">What payment methods work internationally?</h3>
                    <p class="muted" style="margin: 0; font-size: 14px;">PayPal, Card, and Bank Transfer work globally. M-PESA is Kenya-only. Choose what works for you.</p>
                </article>
                <article class="card">
                    <h3 style="margin: 0 0 8px;">How is my payment information secured?</h3>
                    <p class="muted" style="margin: 0; font-size: 14px;">All payments are encrypted with SSL. We never store full card details. Your data is safe with us.</p>
                </article>
                <article class="card">
                    <h3 style="margin: 0 0 8px;">Can I get a receipt for my payment?</h3>
                    <p class="muted" style="margin: 0; font-size: 14px;">Absolutely! All receipts and payment history are stored in your member dashboard for easy access anytime.</p>
                </article>
                <article class="card">
                    <h3 style="margin: 0 0 8px;">What if I have technical issues?</h3>
                    <p class="muted" style="margin: 0; font-size: 14px;">Email support@fitzone.test or call our tech team. We respond quickly to app issues and login problems.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="section-inner">
        <h2 style="text-align: center; margin-bottom: 40px;">Different Ways to Reach Us</h2>
        <div class="grid three">
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, #dbeafe, #bfdbfe); color:var(--blue); margin:0 auto">📧</div>
                <h3>Email</h3>
                <p class="muted">hello@fitzone.test</p>
                <p style="font-size: 12px; color: var(--muted);">Response within 2 hours</p>
            </article>
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, #dcfce7, #bbf7d0); color:var(--green); margin:0 auto">📱</div>
                <h3>Phone</h3>
                <p class="muted">+254 700 000 000</p>
                <p style="font-size: 12px; color: var(--muted);">7am - 9pm daily</p>
            </article>
            <article class="card" style="text-align: center;">
                <div class="icon" style="background:linear-gradient(135deg, #ffedd5, #fed7aa); color:var(--amber); margin:0 auto">📍</div>
                <h3>Visit</h3>
                <p class="muted">Fitzone Gym, Nairobi</p>
                <p style="font-size: 12px; color: var(--muted);">Walk-ins welcome daily</p>
            </article>
        </div>
    </div>
</section>

<div class="cta-banner">
    <h2>Need Help Getting Started?</h2>
    <p>Reach out to our support team today. We're happy to answer questions and help you choose the right membership plan.</p>
    <div class="hero-actions" style="justify-content: center;">
        <a class="btn" style="background:#fff; color:var(--blue)" href="{{ auth()->check() ? route('client.dashboard') : route('login') }}">Join Fitzone</a>
        <a class="btn ghost" style="background:rgba(255,255,255,.2); color:#fff" href="tel:+254700000000">Call Us</a>
    </div>
</div>
@endsection
