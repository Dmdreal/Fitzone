@extends('layouts.app')

@section('title', 'Checkout - Fitzone')

@section('content')
<style>
    .checkout-wrap { display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(300px, .75fr); gap: 16px; align-items: start; }
    .pay-grid { display: grid; gap: 14px; }
    .pay-methods { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 150px), 1fr)); gap: 12px; margin-bottom: 16px; }
    .pay-methods.method-selected { display: none; }
    .method-button { min-height: 98px; border: 1px solid var(--line); border-radius: 8px; padding: 12px; background: #fff; cursor: pointer; display: grid; place-items: center; gap: 8px; color: #334155; transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
    .method-button:hover, .method-button.active { border-color: var(--blue); box-shadow: 0 10px 24px rgba(18,99,230,.12); transform: translateY(-1px); }
    .method-button.active { background: #eff6ff; }
    .method-button small { font-weight: 850; color: var(--muted); }
    .pay-logo { min-height: 38px; display: inline-grid; place-items: center; font-weight: 950; letter-spacing: 0; line-height: 1; }
    .logo-mpesa { min-width: 100px; border-radius: 6px; padding: 9px 11px; color: #fff; background: linear-gradient(90deg, #159447 0 50%, #d71920 50%); font-size: 17px; }
    .logo-paypal { color: #003087; font-size: 20px; }
    .logo-paypal span { color: #009cde; }
    .logo-card { display: flex; gap: 6px; align-items: center; }
    .logo-card span { border-radius: 5px; padding: 8px 9px; color: #fff; font-size: 13px; }
    .logo-card span:first-child { background: #1a1f71; }
    .logo-card span:last-child { background: #eb001b; }
    .logo-bank { min-width: 88px; border-radius: 6px; padding: 9px 11px; color: #fff; background: #0f172a; font-size: 16px; }
    .logo-cash { min-width: 88px; border-radius: 6px; padding: 9px 11px; color: #14532d; background: #dcfce7; font-size: 16px; }
    .pay-option { border: 1px solid var(--line); border-radius: 8px; padding: 16px; background: #fff; display: none; }
    .pay-option.active { display: block; }
    .payment-details { display: none; }
    .payment-details.active { display: block; }
    .change-method { display: none; margin-top: 10px; }
    .change-method.active { display: inline-flex; }
    .pay-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .pay-brand { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .brand-tile { width: 44px; height: 44px; border-radius: 8px; display: grid; place-items: center; color: #fff; font-weight: 900; flex: 0 0 44px; }
    .mpesa-tile { background: linear-gradient(135deg, #16a34a, #dc2626); }
    .paypal-tile { background: linear-gradient(135deg, #003087, #009cde); }
    .card-tile { background: linear-gradient(135deg, #5b21b6, #e11d48); }
    .bank-tile { background: linear-gradient(135deg, #0f172a, #2563eb); }
    .cash-tile { background: linear-gradient(135deg, #166534, #f59e0b); }
    .brand-title { display: grid; gap: 2px; min-width: 0; }
    .brand-title strong { font-size: 16px; }
    .method-badge { border-radius: 999px; padding: 5px 9px; font-size: 11px; font-weight: 900; background: #f1f5f9; color: #334155; white-space: nowrap; }
    .mpesa-panel { background: linear-gradient(135deg, #0f8f43, #12b05a 50%, #d71920); color: #fff; border: 0; }
    .mpesa-panel .muted { color: #dcfce7; }
    .mpesa-logo { display: inline-flex; align-items: center; gap: 7px; font-weight: 950; letter-spacing: .2px; }
    .mpesa-logo span:first-child { width: 28px; height: 28px; border-radius: 50%; background: #fff; color: #16a34a; display: grid; place-items: center; }
    .card-preview { min-height: 190px; border-radius: 18px; padding: 20px; color: #fff; background: radial-gradient(circle at 85% 15%, rgba(255,255,255,.22), transparent 28%), linear-gradient(135deg, #4f46e5, #be123c); display: grid; align-content: space-between; box-shadow: 0 18px 34px rgba(79,70,229,.24); }
    .card-preview small { color: rgba(255,255,255,.8); }
    .card-number { font-size: 20px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; overflow-wrap: anywhere; }
    .secure-note { display:flex;gap:8px;align-items:center;color:#166534;font-weight:800;font-size:12px;margin-top:10px; }
    .summary-box { position: sticky; top: 20px; }
    .summary-row { display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid #edf2f7; }
    .summary-total { font-size: 24px; font-weight: 950; }
    @media (max-width: 980px) { .checkout-wrap { grid-template-columns: minmax(0, 1fr); } .summary-box { position: static; } }
</style>

<h1>Checkout</h1>

@if ($errors->any())
    <section class="card" style="margin-bottom:16px;border-color:#fecaca;background:#fef2f2;box-shadow:none">
        <strong style="color:#991b1b">Payment needs attention</strong>
        <ul style="margin:8px 0 0;color:#991b1b">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </section>
@endif

<div class="checkout-wrap">
    <section class="pay-grid">
        <section class="card" style="box-shadow:none">
            <h2>Choose Payment Method</h2>
            <p class="muted">Tap a logo to continue with that payment process.</p>
            <div class="pay-methods" data-pay-methods role="tablist" aria-label="Payment methods">
                <button class="method-button" type="button" data-pay-method="mpesa" role="tab" aria-selected="false">
                    <span class="pay-logo logo-mpesa">M-PESA</span>
                    <small>Instant STK</small>
                </button>
                <button class="method-button" type="button" data-pay-method="paypal" role="tab" aria-selected="false">
                    <span class="pay-logo logo-paypal">Pay<span>Pal</span></span>
                    <small>Verification</small>
                </button>
                <button class="method-button" type="button" data-pay-method="card" role="tab" aria-selected="false">
                    <span class="pay-logo logo-card"><span>VISA</span><span>MC</span></span>
                    <small>Card</small>
                </button>
                <button class="method-button" type="button" data-pay-method="bank" role="tab" aria-selected="false">
                    <span class="pay-logo logo-bank">BANK</span>
                    <small>Transfer</small>
                </button>
                <button class="method-button" type="button" data-pay-method="cash" role="tab" aria-selected="false">
                    <span class="pay-logo logo-cash">CASH</span>
                    <small>At gym</small>
                </button>
            </div>
            <button class="btn ghost change-method" data-change-method type="button">Choose Another Method</button>
        </section>

        <div class="payment-details" data-payment-details>
        <article class="pay-option mpesa-panel" data-pay-panel="mpesa">
            <div class="pay-head">
                <div class="pay-brand">
                    <span class="brand-tile mpesa-tile">M</span>
                    <span class="brand-title">
                        <strong class="mpesa-logo"><span>M</span>M-PESA STK Push</strong>
                        <small class="muted">Instant confirmation from Safaricom callback</small>
                    </span>
                </div>
                <span class="method-badge">Recommended</span>
            </div>
            <form method="POST" action="{{ route('mpesa.stkpush') }}" class="form-grid">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <input type="hidden" name="amount" value="{{ $priceOverride ?? $package->price }}">
                @if ($trainer)
                    <input type="hidden" name="trainer_id" value="{{ $trainer->id }}">
                @endif
                @if ($gym)
                    <input type="hidden" name="gym_id" value="{{ $gym->id }}">
                @endif
                <label style="color:#fff">M-PESA Phone Number
                    <input name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="0712345678 or 254712345678" required>
                </label>
                <label style="align-self:end">
                    <button class="btn" style="background:#fff;color:#047857;box-shadow:none" type="submit">Send STK Prompt</button>
                </label>
            </form>
        </article>

        <article class="pay-option" data-pay-panel="paypal">
            <div class="pay-head">
                <div class="pay-brand">
                    <span class="brand-tile paypal-tile">P</span>
                    <span class="brand-title"><strong>PayPal</strong><small class="muted">Submit transaction details for verification</small></span>
                </div>
                <span class="method-badge">Pending until approved</span>
            </div>
            <form method="POST" action="{{ route('payments.submit') }}" class="form-grid">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <input type="hidden" name="amount" value="{{ $priceOverride ?? $package->price }}">
                <input type="hidden" name="method" value="paypal">
                @if ($trainer)<input type="hidden" name="trainer_id" value="{{ $trainer->id }}">@endif
                @if ($gym)<input type="hidden" name="gym_id" value="{{ $gym->id }}">@endif
                <label>PayPal Email <input name="paypal_email" type="email" placeholder="you@example.com"></label>
                <label>Transaction ID <input name="paypal_transaction_id" placeholder="PAYPAL-TRANSACTION-ID"></label>
                <label style="align-self:end"><button class="btn" type="submit">Submit PayPal Details</button></label>
            </form>
        </article>

        <article class="pay-option" data-pay-panel="card">
            <div class="pay-head">
                <div class="pay-brand">
                    <span class="brand-tile card-tile">V</span>
                    <span class="brand-title"><strong>Card Payment</strong><small class="muted">Card details are verified before activation</small></span>
                </div>
                <span class="method-badge">Visa / Mastercard</span>
            </div>
            <div class="grid two" style="margin-bottom:0">
                <div class="card-preview">
                    <div style="display:flex;justify-content:space-between;align-items:center"><small>Credit / Debit</small><strong>VISA</strong></div>
                    <div>
                        <div class="card-number">••••  ••••  ••••  {{ substr(preg_replace('/\D+/', '', old('card_number', '4242')), -4) }}</div>
                        <small>{{ strtoupper(old('card_name', auth()->user()->name)) }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('payments.submit') }}" class="form-grid">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <input type="hidden" name="amount" value="{{ $priceOverride ?? $package->price }}">
                    <input type="hidden" name="method" value="card">
                    @if ($trainer)<input type="hidden" name="trainer_id" value="{{ $trainer->id }}">@endif
                    @if ($gym)<input type="hidden" name="gym_id" value="{{ $gym->id }}">@endif
                    <label>Name on Card <input name="card_name" value="{{ old('card_name', auth()->user()->name) }}" autocomplete="cc-name"></label>
                    <label>Card Number <input name="card_number" inputmode="numeric" placeholder="4242 4242 4242 4242" autocomplete="cc-number"></label>
                    <label>Expiry <input name="card_expiry" placeholder="MM/YY" autocomplete="cc-exp"></label>
                    <label>CVV <input name="card_cvv" inputmode="numeric" placeholder="123" autocomplete="cc-csc"></label>
                    <label style="align-self:end"><button class="btn" type="submit">Submit Card Payment</button></label>
                </form>
            </div>
            <p class="secure-note"><span class="badge green">Secure</span> Full card numbers and CVV are not stored in Fitzone.</p>
        </article>

        <article class="pay-option" data-pay-panel="bank">
            <div class="pay-head">
                <div class="pay-brand">
                    <span class="brand-tile bank-tile">B</span>
                    <span class="brand-title"><strong>Bank Transfer</strong><small class="muted">Use your bank receipt/reference for verification</small></span>
                </div>
                <span class="method-badge">Manual verification</span>
            </div>
            <form method="POST" action="{{ route('payments.submit') }}" class="form-grid">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <input type="hidden" name="amount" value="{{ $priceOverride ?? $package->price }}">
                <input type="hidden" name="method" value="bank">
                @if ($trainer)<input type="hidden" name="trainer_id" value="{{ $trainer->id }}">@endif
                @if ($gym)<input type="hidden" name="gym_id" value="{{ $gym->id }}">@endif
                <label>Bank Name <input name="bank_name" placeholder="KCB / Equity / Cooperative"></label>
                <label>Bank Reference <input name="bank_reference" placeholder="Receipt or transaction reference"></label>
                <label>Depositor Name <input name="depositor_name" value="{{ auth()->user()->name }}"></label>
                <label style="align-self:end"><button class="btn" type="submit">Submit Bank Details</button></label>
            </form>
        </article>

        <article class="pay-option" data-pay-panel="cash">
            <div class="pay-head">
                <div class="pay-brand">
                    <span class="brand-tile cash-tile">C</span>
                    <span class="brand-title"><strong>Cash at Gym</strong><small class="muted">No access until admin or trainer approves receipt</small></span>
                </div>
                <span class="method-badge">Locked until approved</span>
            </div>
            <form method="POST" action="{{ route('payments.submit') }}" class="form-grid">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <input type="hidden" name="amount" value="{{ $priceOverride ?? $package->price }}">
                <input type="hidden" name="method" value="cash">
                @if ($trainer)<input type="hidden" name="trainer_id" value="{{ $trainer->id }}">@endif
                @if ($gym)<input type="hidden" name="gym_id" value="{{ $gym->id }}">@endif
                <label>Cash Note <input name="cash_note" placeholder="Who received it, desk, time, or receipt note"></label>
                <label style="align-self:end"><button class="btn ghost" type="submit">Request Cash Approval</button></label>
            </form>
        </article>
        </div>
    </section>

    <aside class="card summary-box payment-details" data-payment-details>
        <h2>Order Summary</h2>
        <div class="summary-row"><span>Package</span><strong>{{ $package->name }}</strong></div>
        <div class="summary-row"><span>Duration</span><strong>{{ $package->duration_count }} {{ $package->duration_unit }}</strong></div>
        <div class="summary-row"><span>Trainer</span><strong>{{ $trainer?->user?->name ?? 'No trainer selected' }}</strong></div>
        <div class="summary-row"><span>Gym</span><strong>{{ $gym?->gym_name ?? $gym?->name ?? 'No gym selected' }}</strong></div>
        @if(isset($priceOverride) && $priceOverride !== null)
            <div class="summary-row"><span>Provider Rate</span><strong>KES {{ number_format($priceOverride, 2) }}</strong></div>
        @endif
        <div class="summary-row"><span>Access</span><span class="badge amber">Locked until confirmed</span></div>
        <div style="padding-top:14px">
            <small class="muted">Total</small>
            <div class="summary-total">KES {{ number_format($priceOverride ?? $package->price, 2) }}</div>
        </div>
        <div class="actions" style="justify-content:flex-start">
            <a class="btn ghost" href="{{ route('client.packages') }}">Back to Plans</a>
        </div>
    </aside>
</div>
<script>
    const methodButtons = document.querySelectorAll('[data-pay-method]');
    const methodPanels = document.querySelectorAll('[data-pay-panel]');
    const paymentDetails = document.querySelectorAll('[data-payment-details]');
    const methodList = document.querySelector('[data-pay-methods]');
    const changeMethodButton = document.querySelector('[data-change-method]');

    function selectPaymentMethod(method) {
        paymentDetails.forEach((detail) => detail.classList.add('active'));
        methodList.classList.add('method-selected');
        changeMethodButton.classList.add('active');

        methodButtons.forEach((button) => {
            const active = button.dataset.payMethod === method;
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        methodPanels.forEach((panel) => {
            panel.classList.toggle('active', panel.dataset.payPanel === method);
        });
    }

    methodButtons.forEach((button) => {
        button.addEventListener('click', () => selectPaymentMethod(button.dataset.payMethod));
    });

    const initializePaymentMethod = () => selectPaymentMethod('mpesa');

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePaymentMethod);
    } else {
        initializePaymentMethod();
    }

    changeMethodButton.addEventListener('click', () => {
        methodList.classList.remove('method-selected');
        changeMethodButton.classList.remove('active');
        paymentDetails.forEach((detail) => detail.classList.remove('active'));
        methodButtons.forEach((button) => {
            button.classList.remove('active');
            button.setAttribute('aria-selected', 'false');
        });
        methodPanels.forEach((panel) => panel.classList.remove('active'));
    });
</script>
@endsection
