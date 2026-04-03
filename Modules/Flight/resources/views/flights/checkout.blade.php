@extends('layouts.master')

@section('title', 'Complete Your Booking')

@php
    $fc       = $fc ?? [];
    $adults   = (int) ($fc['adults']   ?? 1);
    $children = (int) ($fc['children'] ?? 0);
    $infants  = (int) ($fc['infants']  ?? 0);
    $symbol   = $fc['currency_symbol'] ?? '$';
    $price    = (float) ($fc['price']  ?? 0);
    $paxCount = max(1, $adults + $children);
@endphp

@push('styles')
<style>
/* ── Checkout page ───────────────────────────────────── */
.checkout-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%);
    padding: 2rem 0 4rem;
    color: #fff;
}
.checkout-hero__title { font-size: 1.5rem; font-weight: 700; }
.checkout-hero__sub   { font-size: .9rem; opacity: .85; }

/* Steps */
.booking-steps { display: flex; align-items: center; margin-top: 1.5rem; }
.step {
    display: flex; align-items: center; gap: .5rem;
    font-size: .8rem; font-weight: 500;
    color: rgba(255,255,255,.55);
}
.step.active { color: #fff; }
.step.done   { color: rgba(255,255,255,.75); }
.step__num {
    width: 28px; height: 28px; border-radius: 50%;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem; font-weight: 700; flex-shrink: 0;
}
.step.active .step__num { background: #fff; color: var(--primary); }
.step.done   .step__num { background: rgba(255,255,255,.4); color: #fff; }
.step__sep { flex:1; height:1px; background:rgba(255,255,255,.25); margin:0 .5rem; max-width:48px; }

/* Cards */
.checkout-card {
    background: #fff; border-radius: var(--radius-lg);
    box-shadow: var(--shadow-card); border: 1px solid var(--border);
    overflow: hidden; margin-bottom: 1.25rem;
}
.checkout-card__header {
    padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: .75rem;
}
.checkout-card__icon {
    width: 36px; height: 36px; border-radius: 50%;
    background: var(--primary-light);
    display: flex; align-items: center; justify-content: center;
    color: var(--primary); font-size: 1rem; flex-shrink: 0;
}
.checkout-card__title    { font-size: 1rem; font-weight: 700; color: var(--text-dark); margin: 0; }
.checkout-card__subtitle { font-size: .8rem; color: var(--text-muted); margin: 0; }
.checkout-card__body     { padding: 1.5rem; }

/* Form */
.form-label-sm { font-size: .8rem; font-weight: 500; color: var(--text-muted); margin-bottom: .35rem; }
.form-control-co {
    border: 1px solid var(--border); border-radius: .5rem;
    padding: .6rem .9rem; font-size: .9rem; color: var(--text-dark);
    transition: border-color .15s, box-shadow .15s; background: #fff; width: 100%;
}
.form-control-co:focus {
    outline: none; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(23,195,206,.12);
}
.form-select-co {
    border: 1px solid var(--border); border-radius: .5rem;
    padding: .6rem .9rem; font-size: .9rem; color: var(--text-dark);
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%236b7280' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") no-repeat right .75rem center/12px;
    -webkit-appearance: none; appearance: none; width: 100%;
    transition: border-color .15s, box-shadow .15s;
}
.form-select-co:focus {
    outline: none; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(23,195,206,.12);
}

/* Passenger label */
.passenger-label {
    font-size: .75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: var(--primary); margin-bottom: 1rem;
    display: flex; align-items: center; gap: .5rem;
}
.passenger-label::after { content: ''; flex:1; height:1px; background:var(--primary-light); }

/* Payment options */
.payment-option {
    border: 1.5px solid var(--border); border-radius: .75rem;
    padding: 1rem 1.25rem; cursor: pointer;
    transition: border-color .15s, background .15s;
    display: flex; align-items: center; gap: .875rem; margin-bottom: .75rem;
}
.payment-option:hover { border-color: var(--primary); }
.payment-option.selected { border-color: var(--primary); background: var(--primary-light); }
.payment-option input[type="radio"] { accent-color: var(--primary); width: 16px; height: 16px; }
.payment-option__logo {
    width: 42px; height: 28px; border-radius: .35rem;
    display: flex; align-items: center; justify-content: center;
    font-size: .65rem; font-weight: 700; flex-shrink: 0;
}
.payment-option__logo--card   { background: #1a1a2e; color: #fff; }
.payment-option__logo--ngenius { background: #1a3a6e; color: #fff; }
.payment-option__name { font-size: .9rem; font-weight: 600; color: var(--text-dark); }
.payment-option__sub  { font-size: .78rem; color: var(--text-muted); }

.card-icons { display: flex; gap: .4rem; }
.card-icon { width:38px; height:24px; border-radius:.25rem; background:#f3f4f6; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:.55rem; font-weight:800; color:var(--text-muted); }
.card-icon.visa { background:#1a1f71; color:#fff; }
.card-icon.mc   { background:#eb001b; color:#fff; }
.card-icon.amex { background:#2E77BC; color:#fff; }

/* Flight summary sidebar */
.flight-summary-route {
    background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    padding: 1.25rem 1.5rem; color: #fff;
}
.fsr-airports { display:flex; align-items:center; gap:.75rem; font-weight:700; font-size:1.4rem; }
.fsr-sep { display:flex; flex-direction:column; align-items:center; flex:1; }
.fsr-sep-line { width:100%; height:1px; background:rgba(255,255,255,.4); position:relative; }
.fsr-sep-plane { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:.9rem; }
.fsr-meta { font-size:.8rem; opacity:.85; margin-top:.5rem; }

.flight-summary-body { padding: 1.25rem 1.5rem; }
.fsd-row { display:flex; align-items:flex-start; gap:.75rem; padding:.75rem 0; border-bottom:1px solid var(--border); }
.fsd-row:last-child { border-bottom:none; padding-bottom:0; }
.fsd-icon { width:32px; height:32px; border-radius:50%; background:var(--primary-light); display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:.85rem; flex-shrink:0; margin-top:.1rem; }
.fsd-label { font-size:.75rem; color:var(--text-muted); font-weight:500; }
.fsd-value { font-size:.9rem; color:var(--text-dark); font-weight:600; }

/* Price rows */
.price-row { display:flex; justify-content:space-between; align-items:center; font-size:.88rem; color:var(--text-muted); padding:.45rem 0; }
.price-row.total { font-size:1.1rem; font-weight:700; color:var(--text-dark); border-top:2px solid var(--border); margin-top:.5rem; padding-top:.75rem; }
.price-row.total span:last-child { color:var(--primary); }

/* Submit */
.btn-pay {
    background: var(--primary); color: #fff; border: none;
    border-radius: .75rem; padding: .875rem 2rem;
    font-size: 1rem; font-weight: 700; width: 100%;
    transition: background .15s, transform .15s;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.btn-pay:hover { background: var(--primary-dark); transform: translateY(-1px); color: #fff; }

.baggage-notice {
    background: #fffbeb; border: 1px solid #fcd34d;
    border-radius: .6rem; padding: .75rem 1rem;
    font-size: .82rem; color: #92400e;
    display: flex; gap: .5rem; align-items: flex-start;
}
.terms-check { font-size: .83rem; color: var(--text-muted); }
.terms-check a { color: var(--primary); }
.summary-sticky { position: sticky; top: 80px; }
</style>
@endpush

@section('content')

{{-- ── Hero ──────────────────────────────────────────── --}}
<div class="checkout-hero">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div>
                <h1 class="checkout-hero__title mb-1">
                    <i class="bi bi-shield-check me-2 opacity-75"></i>Complete Your Booking
                </h1>
                <p class="checkout-hero__sub mb-0">You're just a few steps away from confirming your flight</p>
            </div>
            <a href="{{ route('flights.index') }}" class="text-white text-decoration-none small opacity-75">
                <i class="bi bi-arrow-left me-1"></i>Back to results
            </a>
        </div>

        <div class="booking-steps mt-3">
            <div class="step done"><span class="step__num"><i class="bi bi-check2"></i></span><span class="d-none d-sm-inline ms-1">Search</span></div>
            <div class="step__sep"></div>
            <div class="step done"><span class="step__num"><i class="bi bi-check2"></i></span><span class="d-none d-sm-inline ms-1">Select</span></div>
            <div class="step__sep"></div>
            <div class="step active"><span class="step__num">3</span><span class="d-none d-sm-inline ms-1">Passenger Details</span></div>
            <div class="step__sep"></div>
            <div class="step"><span class="step__num">4</span><span class="d-none d-sm-inline ms-1">Payment</span></div>
            <div class="step__sep"></div>
            <div class="step"><span class="step__num">5</span><span class="d-none d-sm-inline ms-1">Confirm</span></div>
        </div>
    </div>
</div>

{{-- ── Main ──────────────────────────────────────────── --}}
<section class="py-4" style="background:#f8f9fa; margin-top: -2rem;">
<div class="container">
<div class="row g-4">

    {{-- ── LEFT: Passenger + Payment ────────────────── --}}
    <div class="col-12 col-lg-8">

        {{-- Contact --}}
        <div class="checkout-card">
            <div class="checkout-card__header">
                <div class="checkout-card__icon"><i class="bi bi-envelope"></i></div>
                <div>
                    <p class="checkout-card__title">Contact Information</p>
                    <p class="checkout-card__subtitle">Booking confirmation will be sent here</p>
                </div>
            </div>
            <div class="checkout-card__body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label-sm">Email Address *</label>
                        <input type="email" class="form-control-co" id="contactEmail"
                               placeholder="your@email.com" value="{{ auth()->user()?->email }}" />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label-sm">Phone Number *</label>
                        <input type="tel" class="form-control-co" id="contactPhone"
                               placeholder="+971501234567" />
                        <small class="text-muted" style="font-size:.75rem;">Include country code, e.g. +971501234567</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Passengers --}}
        <div class="checkout-card">
            <div class="checkout-card__header">
                <div class="checkout-card__icon"><i class="bi bi-people"></i></div>
                <div>
                    <p class="checkout-card__title">Passenger Details</p>
                    <p class="checkout-card__subtitle">Enter details exactly as they appear on your passport</p>
                </div>
            </div>
            <div class="checkout-card__body">

                @php
                    $passengerGroups = [];
                    for ($i = 1; $i <= $adults;   $i++) $passengerGroups[] = ['index' => count($passengerGroups) + 1, 'type' => 'Adult'];
                    for ($i = 1; $i <= $children; $i++) $passengerGroups[] = ['index' => count($passengerGroups) + 1, 'type' => 'Child'];
                    for ($i = 1; $i <= $infants;  $i++) $passengerGroups[] = ['index' => count($passengerGroups) + 1, 'type' => 'Infant'];
                @endphp

                @foreach($passengerGroups as $pax)
                <div class="passenger-label">
                    <i class="bi bi-person-circle"></i>
                    Passenger {{ $pax['index'] }} — {{ $pax['type'] }}
                </div>
                <div class="row g-3 mb-4 js-pax-block" data-index="{{ $pax['index'] - 1 }}">
                    <div class="col-6 col-sm-2">
                        <label class="form-label-sm">Title *</label>
                        <select class="form-select-co" data-field="title">
                            <option value="Mr">Mr</option>
                            <option value="Mrs">Mrs</option>
                            <option value="Ms">Ms</option>
                            <option value="Dr">Dr</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-5">
                        <label class="form-label-sm">First Name *</label>
                        <input type="text" class="form-control-co" data-field="first_name" placeholder="First name" />
                    </div>
                    <div class="col-12 col-sm-5">
                        <label class="form-label-sm">Last Name *</label>
                        <input type="text" class="form-control-co" data-field="last_name" placeholder="Last name" />
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label-sm">Date of Birth *</label>
                        <input type="date" class="form-control-co" data-field="dob" />
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label-sm">Nationality *</label>
                        <select class="form-select-co" data-field="nationality">
                            <option value="">Select country</option>
                            <option value="US">United States</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AE">UAE</option>
                            <option value="PK">Pakistan</option>
                            <option value="IN">India</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="SA">Saudi Arabia</option>
                            <option value="CA">Canada</option>
                            <option value="AU">Australia</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label-sm">Gender *</label>
                        <select class="form-select-co" data-field="gender">
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label-sm">Passport Number *</label>
                        <input type="text" class="form-control-co" data-field="passport" placeholder="e.g. AA1234567" />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label-sm">Passport Expiry Date *</label>
                        <input type="date" class="form-control-co" data-field="passport_expiry" />
                    </div>
                </div>
                @endforeach

                <div class="baggage-notice">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Baggage allowance is as per your selected fare. Additional baggage can be purchased after booking.</span>
                </div>
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
                    <div class="card-icons ms-auto">
                        <span class="card-icon visa">VISA</span>
                        <span class="card-icon mc">MC</span>
                        <span class="card-icon amex">AMEX</span>
                    </div>
                </label>

                <label class="payment-option mb-0" id="optNgenius" for="payNgenius">
                    <input type="radio" id="payNgenius" name="payment_gateway" value="ngenius"
                           onchange="togglePayment('ngenius')">
                    <div class="payment-option__logo payment-option__logo--ngenius">N‑G</div>
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
                        I understand this booking is subject to the airline's
                        <a href="#">fare rules and cancellation policy</a>.
                    </label>
                </div>
                <button type="button" class="btn-pay" id="btnPay">
                    <i class="bi bi-lock-fill"></i>
                    <span id="btnPayLabel">Confirm &amp; Pay</span>
                    <span class="ms-1 opacity-75 fw-400 fs-6">
                        {{ $symbol }}{{ number_format($price, 0) }}
                    </span>
                </button>
                <div id="payError" class="alert alert-danger mt-3 mb-0 d-none" style="font-size:.85rem;"></div>
                <p class="text-center text-muted small mt-2 mb-0">
                    <i class="bi bi-shield-lock me-1"></i>
                    Your payment is protected with 256-bit SSL encryption
                </p>
            </div>
        </div>

    </div>{{-- /left col --}}


    {{-- ── RIGHT: Summary ───────────────────────────── --}}
    <div class="col-12 col-lg-4">
        <div class="summary-sticky">

            {{-- Flight summary --}}
            <div class="checkout-card mb-0">
                <div class="flight-summary-route">
                    <div class="fsr-airports">
                        <span>{{ $fc['dep_iata'] ?? '—' }}</span>
                        <div class="fsr-sep">
                            <div class="fsr-sep-line">
                                <span class="fsr-sep-plane"><i class="bi bi-airplane-fill"></i></span>
                            </div>
                        </div>
                        <span>{{ $fc['arr_iata'] ?? '—' }}</span>
                    </div>
                    <div class="fsr-meta">
                        {{ $fc['dep_date'] ?? '' }} · {{ $fc['trip_type'] === 'round_trip' ? 'Round Trip' : 'One Way' }}
                    </div>
                </div>
                <div class="flight-summary-body">
                    <div class="fsd-row">
                        <div class="fsd-icon"><i class="bi bi-clock"></i></div>
                        <div>
                            <div class="fsd-label">Departure</div>
                            <div class="fsd-value">{{ $fc['dep_time'] ?? '—' }} · {{ $fc['dep_date'] ?? '' }}</div>
                        </div>
                    </div>
                    <div class="fsd-row">
                        <div class="fsd-icon"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <div class="fsd-label">Arrival</div>
                            <div class="fsd-value">{{ $fc['arr_time'] ?? '—' }} · {{ $fc['arr_date'] ?? '' }}</div>
                        </div>
                    </div>
                    <div class="fsd-row">
                        <div class="fsd-icon"><i class="bi bi-stopwatch"></i></div>
                        <div>
                            <div class="fsd-label">Duration · Stops</div>
                            <div class="fsd-value">
                                {{ $fc['duration'] ?? '—' }} ·
                                {{ ($fc['stops'] ?? 0) === 0 ? 'Direct' : ($fc['stops'] . ' stop' . (($fc['stops'] ?? 0) > 1 ? 's' : '')) }}
                            </div>
                        </div>
                    </div>
                    <div class="fsd-row">
                        <div class="fsd-icon"><i class="bi bi-airplane"></i></div>
                        <div>
                            <div class="fsd-label">Airline</div>
                            <div class="fsd-value d-flex align-items-center gap-2">
                                @if(!empty($fc['airline_logo']))
                                    <img src="{{ $fc['airline_logo'] }}" alt="{{ $fc['airline_name'] }}" style="height:20px;object-fit:contain;">
                                @endif
                                {{ $fc['airline_name'] ?? '—' }}
                            </div>
                        </div>
                    </div>
                    <div class="fsd-row">
                        <div class="fsd-icon"><i class="bi bi-person-check"></i></div>
                        <div>
                            <div class="fsd-label">Passengers · Cabin</div>
                            <div class="fsd-value">
                                {{ $paxCount }} Pax · {{ $fc['cabin_class'] ?? 'ECONOMY' }}
                            </div>
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
                        <span>Base fare × {{ $paxCount }}</span>
                        <span>{{ $symbol }}{{ number_format($price, 2) }}</span>
                    </div>
                    <div class="price-row">
                        <span>Taxes & fees</span>
                        <span>Included</span>
                    </div>
                    <div class="price-row total">
                        <span>Total</span>
                        <span>{{ $symbol }}{{ number_format($price, 2) }}</span>
                    </div>
                    <div class="mt-3 p-2 rounded" style="background:#f8f9fa;font-size:.8rem;color:var(--text-muted);">
                        <div class="d-flex justify-content-between">
                            <span>Currency</span>
                            <strong>{{ $fc['currency'] ?? 'USD' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 p-3 rounded mt-3"
                 style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.82rem;color:#15803d;">
                <i class="bi bi-shield-fill-check" style="font-size:1.1rem;"></i>
                <span>Secure booking — your data is encrypted and protected.</span>
            </div>

        </div>
    </div>{{-- /right col --}}

</div>{{-- /row --}}
</div>{{-- /container --}}
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

    // Build passengers array from form fields
    const passengers = [];
    document.querySelectorAll('.js-pax-block').forEach(block => {
        const get = field => block.querySelector(`[data-field="${field}"]`)?.value ?? '';
        passengers.push({
            title:          get('title'),
            first_name:     get('first_name'),
            last_name:      get('last_name'),
            dob:            get('dob'),
            nationality:    get('nationality'),
            gender:         get('gender'),
            passport:       get('passport'),
            passport_expiry: get('passport_expiry'),
        });
    });

    const body = {
        checkout_token:   '{{ $checkout_token }}',
        contact_email:    document.getElementById('contactEmail').value,
        contact_phone:    document.getElementById('contactPhone').value,
        payment_gateway:  document.querySelector('input[name="payment_gateway"]:checked')?.value ?? 'stripe',
        passengers,
    };

    const btn   = this;
    const label = document.getElementById('btnPayLabel');
    btn.disabled = true;
    label.textContent = 'Redirecting to payment…';
    err.classList.add('d-none');

    fetch('{{ route('flights.checkout.submit') }}', {
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
        const msg = data.message
            ?? (data.errors ? Object.values(data.errors).flat().join(' ') : null)
            ?? 'Payment could not be initiated. Please try again.';
        err.textContent = msg;
        err.classList.remove('d-none');
        btn.disabled    = false;
        label.textContent = 'Confirm & Pay';
    })
    .catch(() => {
        err.textContent = 'Network error. Please check your connection and try again.';
        err.classList.remove('d-none');
        btn.disabled    = false;
        label.textContent = 'Confirm & Pay';
    });
});
</script>
@endpush
