@extends('layouts.master')

@section('title', 'Complete Your Booking')

@php
    $sc       = $sc ?? [];
    $nights   = 0;
    if (!empty($sc['check_in']) && !empty($sc['check_out'])) {
        $nights = \Carbon\Carbon::parse($sc['check_in'])->diffInDays(\Carbon\Carbon::parse($sc['check_out']));
    }
    $nights     = max(1, (int) $nights);
    $guests     = (int) ($sc['guests'] ?? 1);
    $priceNight = (float) ($sc['price_per_night'] ?? 0);
    $cleaningFee= (float) ($sc['cleaning_fee'] ?? 0);
    $serviceFee = (float) ($sc['service_fee'] ?? 0);
    $totalPrice = (float) ($sc['total_price'] ?? 0);
    $currency   = $sc['currency'] ?? 'USD';
@endphp

@push('styles')
<style>
.checkout-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%);
    padding: 2rem 0 4rem; color: #fff;
}
.checkout-hero__title { font-size: 1.5rem; font-weight: 700; }
.checkout-hero__sub   { font-size: .9rem; opacity: .85; }

.booking-steps { display: flex; align-items: center; margin-top: 1.5rem; }
.step { display: flex; align-items: center; gap: .5rem; font-size: .8rem; font-weight: 500; color: rgba(255,255,255,.55); }
.step.active { color: #fff; }
.step.done   { color: rgba(255,255,255,.75); }
.step__num { width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; font-size: .75rem; font-weight: 700; flex-shrink: 0; }
.step.active .step__num { background: #fff; color: var(--primary); }
.step.done   .step__num { background: rgba(255,255,255,.4); color: #fff; }
.step__sep { flex:1; height:1px; background:rgba(255,255,255,.25); margin:0 .5rem; max-width:48px; }

.checkout-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.25rem; }
.checkout-card__header { padding: 1.1rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: .75rem; }
.checkout-card__icon { width: 36px; height: 36px; border-radius: 50%; background: #e0f7fa; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1rem; flex-shrink: 0; }
.checkout-card__title    { font-size: 1rem; font-weight: 700; color: #1a2942; margin: 0; }
.checkout-card__subtitle { font-size: .8rem; color: #6c757d; margin: 0; }
.checkout-card__body     { padding: 1.5rem; }

.form-label-sm { font-size: .8rem; font-weight: 500; color: #6c757d; margin-bottom: .35rem; }
.form-control-co { border: 1px solid #dee2e6; border-radius: .5rem; padding: .6rem .9rem; font-size: .9rem; width: 100%; transition: border-color .15s, box-shadow .15s; background: #fff; }
.form-control-co:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(23,195,206,.12); }

.payment-option { border: 1.5px solid #dee2e6; border-radius: .75rem; padding: 1rem 1.25rem; cursor: pointer; transition: border-color .15s, background .15s; display: flex; align-items: center; gap: .875rem; margin-bottom: .75rem; }
.payment-option:hover { border-color: var(--primary); }
.payment-option.selected { border-color: var(--primary); background: #e0f7fa; }
.payment-option input[type="radio"] { accent-color: var(--primary); width: 16px; height: 16px; }
.payment-option__logo { width: 42px; height: 28px; border-radius: .35rem; display: flex; align-items: center; justify-content: center; font-size: .65rem; font-weight: 700; flex-shrink: 0; }
.payment-option__logo--card    { background: #1a1a2e; color: #fff; }
.payment-option__logo--ngenius { background: #1a3a6e; color: #fff; }
.payment-option__name { font-size: .9rem; font-weight: 600; }
.payment-option__sub  { font-size: .78rem; color: #6c757d; }

.space-summary-banner { background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%); border-radius: 12px 12px 0 0; padding: 1.25rem 1.5rem; color: #fff; }
.ssb-name { font-size: 1.05rem; font-weight: 700; }
.ssb-meta { font-size: .82rem; opacity: .85; margin-top: .35rem; }
.space-summary-body { padding: 1.25rem 1.5rem; }
.ssd-row { display: flex; align-items: flex-start; gap: .75rem; padding: .7rem 0; border-bottom: 1px solid #e5e7eb; }
.ssd-row:last-child { border-bottom: none; padding-bottom: 0; }
.ssd-icon { width: 30px; height: 30px; border-radius: 50%; background: #e0f7fa; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: .85rem; flex-shrink: 0; }
.ssd-label { font-size: .73rem; color: #6c757d; font-weight: 500; }
.ssd-value { font-size: .9rem; font-weight: 600; color: #1a2942; }

.price-row { display: flex; justify-content: space-between; font-size: .88rem; color: #6c757d; padding: .45rem 0; }
.price-row.total { font-size: 1.1rem; font-weight: 700; color: #1a2942; border-top: 2px solid #e5e7eb; margin-top: .5rem; padding-top: .75rem; }
.price-row.total span:last-child { color: var(--primary); }

.btn-pay { background: var(--primary); color: #fff; border: none; border-radius: .75rem; padding: .875rem 2rem; font-size: 1rem; font-weight: 700; width: 100%; transition: background .15s, transform .15s; display: flex; align-items: center; justify-content: center; gap: .5rem; }
.btn-pay:hover { background: #0e9aa7; transform: translateY(-1px); color: #fff; }

.summary-sticky { position: sticky; top: 80px; }
.terms-check { font-size: .83rem; color: #6c757d; }
.terms-check a { color: var(--primary); }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="checkout-hero">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div>
                <h1 class="checkout-hero__title mb-1">
                    <i class="bi bi-shield-check me-2 opacity-75"></i>Complete Your Booking
                </h1>
                <p class="checkout-hero__sub mb-0">You're just a few steps away from confirming your stay</p>
            </div>
            <a href="{{ route('homes.index') }}" class="text-white text-decoration-none small opacity-75">
                <i class="bi bi-arrow-left me-1"></i>Back to search
            </a>
        </div>

        <div class="booking-steps mt-3">
            <div class="step done"><span class="step__num"><i class="bi bi-check2"></i></span><span class="d-none d-sm-inline ms-1">Search</span></div>
            <div class="step__sep"></div>
            <div class="step done"><span class="step__num"><i class="bi bi-check2"></i></span><span class="d-none d-sm-inline ms-1">Select</span></div>
            <div class="step__sep"></div>
            <div class="step active"><span class="step__num">3</span><span class="d-none d-sm-inline ms-1">Guest Details</span></div>
            <div class="step__sep"></div>
            <div class="step"><span class="step__num">4</span><span class="d-none d-sm-inline ms-1">Payment</span></div>
            <div class="step__sep"></div>
            <div class="step"><span class="step__num">5</span><span class="d-none d-sm-inline ms-1">Confirm</span></div>
        </div>
    </div>
</div>

{{-- Main --}}
<section class="py-4" style="background:#f8f9fa; margin-top: -2rem;">
<div class="container">
<div class="row g-4">

    {{-- LEFT: Guest + Payment --}}
    <div class="col-12 col-lg-8">

        {{-- Guest Details --}}
        <div class="checkout-card">
            <div class="checkout-card__header">
                <div class="checkout-card__icon"><i class="bi bi-person"></i></div>
                <div>
                    <p class="checkout-card__title">Guest Details</p>
                    <p class="checkout-card__subtitle">Primary guest information for this booking</p>
                </div>
            </div>
            <div class="checkout-card__body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label-sm">First Name *</label>
                        <input type="text" class="form-control-co" id="firstName"
                               placeholder="First name" value="{{ auth()->user()?->name ? explode(' ', auth()->user()->name)[0] : '' }}" />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label-sm">Last Name *</label>
                        <input type="text" class="form-control-co" id="lastName"
                               placeholder="Last name" />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label-sm">Email Address *</label>
                        <input type="email" class="form-control-co" id="guestEmail"
                               placeholder="your@email.com" value="{{ auth()->user()?->email }}" />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label-sm">Phone Number *</label>
                        <input type="tel" class="form-control-co" id="guestPhone"
                               placeholder="+971501234567" />
                        <small class="text-muted" style="font-size:.75rem;">Include country code</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Special Requests --}}
        <div class="checkout-card">
            <div class="checkout-card__header">
                <div class="checkout-card__icon"><i class="bi bi-chat-dots"></i></div>
                <div>
                    <p class="checkout-card__title">Special Requests</p>
                    <p class="checkout-card__subtitle">Optional — we'll pass them to the host</p>
                </div>
            </div>
            <div class="checkout-card__body">
                <label class="form-label-sm">Requests / Notes</label>
                <textarea class="form-control-co" id="specialRequests" rows="3"
                          placeholder="e.g. early check-in, extra towels, crib…" style="resize:vertical"></textarea>
            </div>
        </div>

        {{-- Payment --}}
        <div class="checkout-card">
            <div class="checkout-card__header">
                <div class="checkout-card__icon"><i class="bi bi-credit-card-2-front"></i></div>
                <div>
                    <p class="checkout-card__title">Payment Method</p>
                    <p class="checkout-card__subtitle">All transactions are secured and encrypted</p>
                </div>
            </div>
            <div class="checkout-card__body">
                <label class="payment-option selected" id="optStripe" for="payStripe">
                    <input type="radio" id="payStripe" name="payment_gateway" value="stripe" checked
                           onchange="togglePayment('stripe')">
                    <div class="payment-option__logo payment-option__logo--card">
                        <i class="bi bi-credit-card-fill" style="font-size:.9rem;"></i>
                    </div>
                    <div>
                        <div class="payment-option__name">Credit / Debit Card</div>
                        <div class="payment-option__sub">Visa, Mastercard, Amex — powered by Stripe</div>
                    </div>
                </label>

                <label class="payment-option mb-0" id="optNgenius" for="payNgenius">
                    <input type="radio" id="payNgenius" name="payment_gateway" value="ngenius"
                           onchange="togglePayment('ngenius')">
                    <div class="payment-option__logo payment-option__logo--ngenius">N-G</div>
                    <div>
                        <div class="payment-option__name">N-Genius</div>
                        <div class="payment-option__sub">Secure hosted checkout by Network International</div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Terms + Submit --}}
        <div class="checkout-card">
            <div class="checkout-card__body">
                <div class="form-check terms-check mb-3">
                    <input class="form-check-input" type="checkbox" id="termsCheck" />
                    <label class="form-check-label" for="termsCheck">
                        I have read and agree to the
                        <a href="#">Terms & Conditions</a> and
                        <a href="#">Privacy Policy</a>.
                        I understand the host's cancellation policy applies.
                    </label>
                </div>
                <button type="button" class="btn-pay" id="btnPay">
                    <i class="bi bi-lock-fill"></i>
                    <span id="btnPayLabel">Confirm &amp; Pay</span>
                    <span class="ms-1 opacity-75 fw-400 fs-6">
                        {{ $currency }} {{ number_format($totalPrice, 0) }}
                    </span>
                </button>
                <div id="payError" class="alert alert-danger mt-3 mb-0 d-none" style="font-size:.85rem;"></div>
                <p class="text-center text-muted small mt-2 mb-0">
                    <i class="bi bi-shield-lock me-1"></i>
                    Your payment is protected with 256-bit SSL encryption
                </p>
            </div>
        </div>

    </div>

    {{-- RIGHT: Booking Summary --}}
    <div class="col-12 col-lg-4">
        <div class="summary-sticky">

            <div class="checkout-card mb-0">
                <div class="space-summary-banner">
                    <div class="ssb-name">{{ $sc['space_name'] ?? 'Space' }}</div>
                    <div class="ssb-meta">
                        <i class="bi bi-geo-alt me-1"></i>{{ $sc['city'] ?? '' }}{{ !empty($sc['country']) ? ', ' . $sc['country'] : '' }}
                    </div>
                </div>
                <div class="space-summary-body">
                    <div class="ssd-row">
                        <div class="ssd-icon"><i class="bi bi-door-open"></i></div>
                        <div>
                            <div class="ssd-label">Check-in</div>
                            <div class="ssd-value">{{ $sc['check_in'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="ssd-row">
                        <div class="ssd-icon"><i class="bi bi-door-closed"></i></div>
                        <div>
                            <div class="ssd-label">Check-out</div>
                            <div class="ssd-value">{{ $sc['check_out'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="ssd-row">
                        <div class="ssd-icon"><i class="bi bi-moon"></i></div>
                        <div>
                            <div class="ssd-label">Duration</div>
                            <div class="ssd-value">{{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                    <div class="ssd-row">
                        <div class="ssd-icon"><i class="bi bi-people"></i></div>
                        <div>
                            <div class="ssd-label">Guests</div>
                            <div class="ssd-value">{{ $guests }} Guest{{ $guests !== 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Price breakdown --}}
            <div class="checkout-card mt-3">
                <div class="checkout-card__header">
                    <div class="checkout-card__icon"><i class="bi bi-receipt"></i></div>
                    <div><p class="checkout-card__title">Price Summary</p></div>
                </div>
                <div class="checkout-card__body pt-2">
                    <div class="price-row">
                        <span>{{ $currency }} {{ number_format($priceNight, 2) }} x {{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</span>
                        <span>{{ $currency }} {{ number_format($priceNight * $nights, 2) }}</span>
                    </div>
                    @if($cleaningFee > 0)
                    <div class="price-row">
                        <span>Cleaning fee</span>
                        <span>{{ $currency }} {{ number_format($cleaningFee, 2) }}</span>
                    </div>
                    @endif
                    @if($serviceFee > 0)
                    <div class="price-row">
                        <span>Service fee</span>
                        <span>{{ $currency }} {{ number_format($serviceFee, 2) }}</span>
                    </div>
                    @endif
                    <div class="price-row total">
                        <span>Total</span>
                        <span>{{ $currency }} {{ number_format($totalPrice, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 p-3 rounded mt-3"
                 style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.82rem;color:#15803d;">
                <i class="bi bi-shield-fill-check" style="font-size:1.1rem;"></i>
                <span>Secure booking — your data is encrypted and protected.</span>
            </div>

        </div>
    </div>

</div>
</div>
</section>

@endsection

@push('scripts')
<script>
function togglePayment(method) {
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    document.getElementById(method === 'stripe' ? 'optStripe' : 'optNgenius').classList.add('selected');
}

document.getElementById('btnPay').addEventListener('click', function () {
    const terms = document.getElementById('termsCheck');
    const err   = document.getElementById('payError');

    if (!terms.checked) {
        err.textContent = 'Please accept the Terms & Conditions to proceed.';
        err.classList.remove('d-none');
        return;
    }

    const firstName = document.getElementById('firstName').value.trim();
    const lastName  = document.getElementById('lastName').value.trim();
    const email     = document.getElementById('guestEmail').value.trim();
    const phone     = document.getElementById('guestPhone').value.trim();

    if (!firstName || !lastName || !email || !phone) {
        err.textContent = 'Please fill in all required guest details.';
        err.classList.remove('d-none');
        return;
    }

    const body = {
        checkout_token:   '{{ $checkout_token }}',
        first_name:       firstName,
        last_name:        lastName,
        email:            email,
        phone:            phone,
        payment_gateway:  document.querySelector('input[name="payment_gateway"]:checked')?.value ?? 'stripe',
        special_requests: document.getElementById('specialRequests').value.trim() || null,
    };

    const btn   = this;
    const label = document.getElementById('btnPayLabel');
    btn.disabled    = true;
    label.textContent = 'Redirecting to payment…';
    err.classList.add('d-none');

    fetch('{{ route('homes.checkout.submit') }}', {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'Accept':       'application/json',
        },
        body: JSON.stringify(body),
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (ok && (data.url || data.redirect)) {
            window.location.href = data.url ?? data.redirect;
            return;
        }

        const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Something went wrong.');
        err.textContent = msg;
        err.classList.remove('d-none');
        btn.disabled    = false;
        label.textContent = 'Confirm & Pay';
    })
    .catch(() => {
        err.textContent = 'Network error. Please try again.';
        err.classList.remove('d-none');
        btn.disabled    = false;
        label.textContent = 'Confirm & Pay';
    });
});
</script>
@endpush
