@extends('layouts.master')

@section('title', 'Checkout - ' . ($activityOffer?->title ?? 'Activity') . ' - Travolyo')

@push('styles')
<style>
    .activity-checkout-wrap {
        padding: 1rem 0 3rem;
    }
    .checkout-topbar {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 1rem 0 0.75rem;
    }
    .checkout-topbar a {
        color: var(--color-text, #334155);
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .checkout-topbar a:hover {
        color: var(--color-primary, #17c3ce);
    }
    .checkout-activity-header {
        margin-bottom: 1.5rem;
    }
    .checkout-activity-header__title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--color-dark, #0f172a);
        margin-bottom: 0.2rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .checkout-activity-header__badge {
        font-size: 0.7rem;
        font-weight: 600;
        background: var(--color-primary, #17c3ce);
        color: #fff;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .checkout-activity-header__address {
        color: #888;
        font-size: 0.88rem;
    }
    .checkout-activity-header__meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.35rem;
        flex-wrap: wrap;
    }
    .checkout-activity-header__tag {
        font-size: 0.78rem;
        padding: 0.15rem 0.6rem;
        border-radius: 20px;
        border: 1px solid #10b981;
        color: #10b981;
        background: #f0fdf4;
    }
    .checkout-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        margin-bottom: 1.25rem;
    }
    .checkout-card__title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--color-dark, #0f172a);
        margin-bottom: 1.25rem;
    }
    .checkout-section + .checkout-section {
        margin-top: 1.75rem;
        padding-top: 1.75rem;
        border-top: 1px solid #f0f0f0;
    }
    .checkout-section__title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--color-dark, #0f172a);
        margin-bottom: 0.3rem;
    }
    .checkout-section__sub {
        font-size: 0.84rem;
        color: #777;
        margin-bottom: 1rem;
    }
    .passenger-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 1rem;
        background: #fbfdff;
    }
    .passenger-card + .passenger-card {
        margin-top: 1rem;
    }
    .passenger-card__title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--color-dark, #0f172a);
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .passenger-card__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 0.5rem;
        border-radius: 999px;
        background: rgba(23, 195, 206, 0.12);
        color: var(--color-primary, #17c3ce);
        font-size: 0.78rem;
        font-weight: 700;
    }
    .form-label-sm {
        font-size: 0.8rem;
        font-weight: 600;
        color: #555;
        margin-bottom: 0.3rem;
        display: block;
    }
    .form-input {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.9rem;
        color: var(--color-dark, #0f172a);
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #fff;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--color-primary, #17c3ce);
        box-shadow: 0 0 0 3px rgba(23, 195, 206, 0.12);
    }
    .form-input.is-invalid {
        border-color: #ef4444;
    }
    .summary-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        position: sticky;
        top: 80px;
    }
    .summary-card__title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--color-dark, #0f172a);
        margin-bottom: 1rem;
    }
    .summary-card__img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 12px;
        display: block;
        margin-bottom: 1rem;
        background: #e2e8f0;
    }
    .summary-card__hotel-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--color-dark, #0f172a);
        margin-bottom: 0.15rem;
    }
    .summary-card__hotel-addr {
        font-size: 0.8rem;
        color: #999;
        margin-bottom: 1rem;
    }
    .summary-card__divider {
        border: none;
        border-top: 1px solid #f0f0f0;
        margin: 1rem 0;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        font-size: 0.875rem;
        color: #555;
        margin-bottom: 0.55rem;
    }
    .summary-row__label {
        color: #888;
    }
    .summary-row__value {
        font-weight: 500;
        color: var(--color-dark, #0f172a);
        text-align: right;
    }
    .summary-price-breakdown {
        font-size: 0.82rem;
        color: #888;
        margin-bottom: 0.5rem;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-dark, #0f172a);
        margin-top: 0.5rem;
    }
    .payment-option {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.9rem 1rem;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        display: flex;
        align-items: center;
        gap: 0.7rem;
        margin-bottom: 0.65rem;
    }
    .payment-option:hover {
        border-color: var(--color-primary, #17c3ce);
    }
    .payment-option.selected {
        border-color: var(--color-primary, #17c3ce);
        background: #f0fdfe;
    }
    .payment-option input[type="radio"] {
        accent-color: var(--color-primary, #17c3ce);
        width: 16px;
        height: 16px;
    }
    .payment-option__logo {
        width: 42px;
        height: 28px;
        border-radius: 0.35rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 700;
        flex-shrink: 0;
        color: #fff;
    }
    .payment-option__logo--card {
        background: #1a1a2e;
    }
    .payment-option__logo--bank {
        background: #1d4ed8;
    }
    .payment-option__content {
        min-width: 0;
    }
    .payment-option__name {
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--color-dark, #0f172a);
    }
    .payment-option__sub {
        font-size: 0.78rem;
        color: #777;
    }
    .card-icons {
        display: flex;
        gap: 0.4rem;
        margin-left: auto;
    }
    .card-icon {
        width: 38px;
        height: 24px;
        border-radius: 0.25rem;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.55rem;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
    }
    .card-icon.visa {
        background: #1a1f71;
    }
    .card-icon.mc {
        background: #eb001b;
    }
    .card-icon.amex {
        background: #2e77bc;
    }
    .btn-continue {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 48px;
        background: var(--color-primary, #17c3ce);
        color: #fff;
        border: 1px solid var(--color-primary, #17c3ce);
        padding: 0.9rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        margin-top: 1.25rem;
        transition: background 0.2s, border-color 0.2s;
        cursor: pointer;
    }
    .btn-continue:hover {
        background: var(--color-primary-dark, #13a9b3);
        border-color: var(--color-primary-dark, #13a9b3);
        color: #fff;
    }
    .field-error {
        font-size: 0.78rem;
        color: #ef4444;
        margin-top: 0.25rem;
    }
    .summary-terms {
        margin-top: 1rem;
        font-size: 0.84rem;
        color: #666;
    }
</style>
@endpush

@section('content')
@php
    $offer = $activityOffer ?? null;
    $basePrice = (float) ($offer?->basePricePerPerson ?? 0);
    $baseCurrency = strtoupper((string) ($offer?->baseCurrency ?? 'AED'));
    $unitPrice = (float) ($offer?->convertedPricePerPerson ?? $basePrice);
    $currency = strtoupper((string) ($offer?->convertedCurrency ?? $displayCurrency ?? session('currency', $baseCurrency)));
    $activityImage = $offer?->imageUrl ?: asset('assets/images/favicon/favicon1.png');
    $participantsValue = (int) old('participants', $participants);
    $todayDate = now()->toDateString();
    $passengerForms = old('passengers', $passengersData ?? []);
    $passengerForms = is_array($passengerForms) ? array_values($passengerForms) : [];
    for ($i = count($passengerForms); $i < $participantsValue; $i++) {
        $passengerForms[] = [
            'title' => 'Mr',
            'first_name' => '',
            'last_name' => '',
            'dob' => '',
            'nationality' => '',
            'gender' => 'M',
            'passport' => '',
            'passport_expiry' => '',
            'label' => $i === 0 ? 'Lead Passenger' : 'Passenger ' . ($i + 1),
        ];
    }
    $passengerForms = array_slice($passengerForms, 0, max(1, $participantsValue));
@endphp

<div class="container activity-checkout-wrap">
    <div class="checkout-topbar">
        <a href="{{ route('activities.index', ['city' => $city, 'activity_date' => $date]) }}">
            <i class="bi bi-arrow-left"></i> Back to results
        </a>
    </div>

    <div class="checkout-activity-header">
        <h1 class="checkout-activity-header__title">
            {{ $offer?->title ?? 'Activity' }}
            @if($offer?->instantConfirmation)
                <span class="checkout-activity-header__badge">Instant</span>
            @endif
        </h1>
        <div class="checkout-activity-header__address">
            <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $offer?->city ?: '-' }}, {{ $offer?->country ?: '-' }}
            @if($offer?->address)
                <span class="mx-1">|</span>{{ $offer->address }}
            @endif
        </div>
        <div class="checkout-activity-header__meta">
            @if($offer?->category)
                <span class="checkout-activity-header__tag">{{ $offer->category }}</span>
            @endif
            @if($offer?->duration)
                <span class="checkout-activity-header__tag">{{ $offer->duration }}</span>
            @endif
            <span class="checkout-activity-header__tag">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-3">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3 mb-3">
            <strong>Please fix the highlighted fields.</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <div class="checkout-card">
                <h2 class="checkout-card__title">Passenger & Payment Details</h2>

                <form method="POST" action="{{ route('activities.pay', $activity) }}" id="activityCheckoutForm">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking?->id }}">

                    <div class="checkout-section">
                        <div class="checkout-section__title">Contact Information</div>
                        <div class="checkout-section__sub">Booking updates and payment receipts will be sent here.</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-sm">Email Address *</label>
                                <input type="email" name="contact_email" value="{{ old('contact_email', $passenger?->contact_email ?? auth()->user()?->email) }}" class="form-input @error('contact_email') is-invalid @enderror" placeholder="name@example.com" required>
                                @error('contact_email')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-sm">Phone Number *</label>
                                <input type="text" name="contact_phone" value="{{ old('contact_phone', $passenger?->contact_phone) }}" class="form-input @error('contact_phone') is-invalid @enderror" placeholder="+971 50 123 4567" required>
                                @error('contact_phone')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <div class="checkout-section__title">Passenger Details</div>
                        <div class="checkout-section__sub">Passenger blocks will update automatically according to the selected participant count.</div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-sm">Participants *</label>
                                <input type="number" min="1" max="{{ (int) ($offer?->maxParticipants ?: 20) }}" id="participantsInput" name="participants" value="{{ $participantsValue }}" class="form-input @error('participants') is-invalid @enderror" placeholder="Enter participants" required>
                                @error('participants')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-sm">Activity Date *</label>
                                <input type="date" name="activity_date" value="{{ old('activity_date', $date) }}" class="form-input @error('activity_date') is-invalid @enderror" placeholder="Select activity date" required>
                                @error('activity_date')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @error('passengers')
                            <div class="field-error mb-3">{{ $message }}</div>
                        @enderror

                        <div id="passengerFormsContainer" data-passenger-count="{{ count($passengerForms) }}">
                            @foreach($passengerForms as $index => $traveler)
                                <div class="passenger-card" data-passenger-card>
                                    <div class="passenger-card__title">
                                        <span class="passenger-card__badge">{{ $index + 1 }}</span>
                                        <span>{{ $index === 0 ? 'Lead Passenger' : 'Passenger ' . ($index + 1) }}</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label-sm">Title *</label>
                                            <select name="passengers[{{ $index }}][title]" class="form-input @error("passengers.$index.title") is-invalid @enderror">
                                                <option value="Mr" @selected(($traveler['title'] ?? 'Mr') === 'Mr')>Mr</option>
                                                <option value="Mrs" @selected(($traveler['title'] ?? '') === 'Mrs')>Mrs</option>
                                                <option value="Ms" @selected(($traveler['title'] ?? '') === 'Ms')>Ms</option>
                                            </select>
                                            @error("passengers.$index.title")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label-sm">First Name *</label>
                                            <input type="text" name="passengers[{{ $index }}][first_name]" value="{{ $traveler['first_name'] ?? '' }}" class="form-input @error("passengers.$index.first_name") is-invalid @enderror" placeholder="Enter first name" required>
                                            @error("passengers.$index.first_name")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label-sm">Last Name *</label>
                                            <input type="text" name="passengers[{{ $index }}][last_name]" value="{{ $traveler['last_name'] ?? '' }}" class="form-input @error("passengers.$index.last_name") is-invalid @enderror" placeholder="Enter last name" required>
                                            @error("passengers.$index.last_name")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-sm">Date of Birth *</label>
                                            <input type="date" name="passengers[{{ $index }}][dob]" value="{{ $traveler['dob'] ?? '' }}" class="form-input @error("passengers.$index.dob") is-invalid @enderror" max="{{ $todayDate }}" required>
                                            @error("passengers.$index.dob")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-sm">Nationality *</label>
                                            <input type="text" name="passengers[{{ $index }}][nationality]" value="{{ $traveler['nationality'] ?? '' }}" class="form-input @error("passengers.$index.nationality") is-invalid @enderror" placeholder="PK, AE, US" required>
                                            @error("passengers.$index.nationality")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-sm">Gender *</label>
                                            <select name="passengers[{{ $index }}][gender]" class="form-input @error("passengers.$index.gender") is-invalid @enderror" required>
                                                <option value="M" @selected(($traveler['gender'] ?? 'M') === 'M')>Male</option>
                                                <option value="F" @selected(($traveler['gender'] ?? '') === 'F')>Female</option>
                                            </select>
                                            @error("passengers.$index.gender")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-sm">Passport Number *</label>
                                            <input type="text" name="passengers[{{ $index }}][passport]" value="{{ $traveler['passport'] ?? '' }}" class="form-input @error("passengers.$index.passport") is-invalid @enderror" placeholder="Enter passport number" required>
                                            @error("passengers.$index.passport")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-sm">Passport Expiry *</label>
                                            <input type="date" name="passengers[{{ $index }}][passport_expiry]" value="{{ $traveler['passport_expiry'] ?? '' }}" class="form-input @error("passengers.$index.passport_expiry") is-invalid @enderror" min="{{ $todayDate }}" required>
                                            @error("passengers.$index.passport_expiry")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="checkout-section">
                        <div class="checkout-section__title">Special Requests</div>
                        <div class="checkout-section__sub">Optional notes for pickup, accessibility, or assistance.</div>
                        <textarea name="special_requests" rows="5" class="form-input @error('special_requests') is-invalid @enderror" placeholder="Pickup point, accessibility request, special notes...">{{ old('special_requests', $passenger?->special_requests) }}</textarea>
                        @error('special_requests')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="checkout-section">
                        <div class="checkout-section__title">Payment Method</div>
                        <div class="checkout-section__sub">Choose your preferred secure payment gateway.</div>

                        <label class="payment-option selected" id="optStripe" for="payStripe">
                            <input type="radio" id="payStripe" name="payment_gateway" value="stripe" @checked(old('payment_gateway', $passenger?->payment_gateway ?? 'stripe') === 'stripe')>
                            <div class="payment-option__logo payment-option__logo--card">CARD</div>
                            <div class="payment-option__content">
                                <div class="payment-option__name">Credit / Debit Card</div>
                                <div class="payment-option__sub">Visa, Mastercard and more via Stripe checkout.</div>
                            </div>
                            <div class="card-icons" aria-hidden="true">
                                <span class="card-icon visa">VISA</span>
                                <span class="card-icon mc">MC</span>
                                <span class="card-icon amex">AMEX</span>
                            </div>
                        </label>

                        <label class="payment-option" id="optNgenius" for="payNgenius">
                            <input type="radio" id="payNgenius" name="payment_gateway" value="ngenius" @checked(old('payment_gateway', $passenger?->payment_gateway) === 'ngenius')>
                            <div class="payment-option__logo payment-option__logo--bank">NG</div>
                            <div class="payment-option__content">
                                <div class="payment-option__name">N-Genius</div>
                                <div class="payment-option__sub">Secure hosted checkout by Network International.</div>
                            </div>
                        </label>
                    </div>

                    <div class="d-lg-none mt-4">
                        <button type="submit" class="btn-continue">Continue to Payment</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="summary-card">
                <div class="summary-card__title">Booking Summary</div>

                <img src="{{ $activityImage }}" alt="{{ $offer?->title ?? 'Activity' }}" class="summary-card__img">

                <div class="summary-card__hotel-name">{{ $offer?->title ?? 'Activity' }}</div>
                <div class="summary-card__hotel-addr">{{ $offer?->city ?: '-' }}, {{ $offer?->country ?: '-' }}</div>

                <hr class="summary-card__divider">

                <div class="summary-row">
                    <span class="summary-row__label">Category</span>
                    <span class="summary-row__value">{{ $offer?->category ?: '-' }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Date</span>
                    <span class="summary-row__value">{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Duration</span>
                    <span class="summary-row__value">{{ $offer?->duration ?: '-' }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Participants</span>
                    <span class="summary-row__value" id="participantCountLabel">{{ $participantsValue }}</span>
                </div>

                <hr class="summary-card__divider">

                <div class="summary-price-breakdown">
                    {{ $currency }} {{ number_format($unitPrice, 2) }}
                    @if($basePrice > 0)
                        <span class="text-muted">({{ $baseCurrency }} {{ number_format($basePrice, 2) }})</span>
                    @endif
                    x <span id="participantCountBreakdown">{{ $participantsValue }}</span> participant(s)
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Subtotal</span>
                    <span class="summary-row__value" id="activitySubtotalLabel">
                        {{ $currency }} {{ number_format($unitPrice * $participantsValue, 2) }}
                        @if($basePrice > 0)
                            <span class="text-muted">({{ $baseCurrency }} {{ number_format($basePrice * $participantsValue, 2) }})</span>
                        @endif
                    </span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Taxes &amp; fees</span>
                    <span class="summary-row__value">Included</span>
                </div>

                <hr class="summary-card__divider">

                <div class="summary-total">
                    <span>Total</span>
                    <span id="activityTotalLabel">
                        {{ $currency }} {{ number_format($unitPrice * $participantsValue, 2) }}
                        @if($basePrice > 0)
                            <span class="text-muted">({{ $baseCurrency }} {{ number_format($basePrice * $participantsValue, 2) }})</span>
                        @endif
                    </span>
                </div>

                <div class="form-check summary-terms">
                    <input class="form-check-input" type="checkbox" id="termsCheckbox" required form="activityCheckoutForm">
                    <label class="form-check-label" for="termsCheckbox">
                        I agree to the terms and privacy policy.
                    </label>
                </div>

                <button type="submit" form="activityCheckoutForm" class="btn-continue d-none d-lg-inline-flex">
                    Continue to Payment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const participantsInput = document.getElementById('participantsInput');
        const participantLabel = document.getElementById('participantCountLabel');
        const participantBreakdown = document.getElementById('participantCountBreakdown');
        const subtotalLabel = document.getElementById('activitySubtotalLabel');
        const totalLabel = document.getElementById('activityTotalLabel');
        const passengerFormsContainer = document.getElementById('passengerFormsContainer');
        const unitPrice = {{ json_encode($unitPrice) }};
        const currency = {{ json_encode($currency) }};
        const todayDate = {{ json_encode($todayDate) }};

        let lastPassengerCount = parseInt(passengerFormsContainer?.dataset.passengerCount || '{{ count($passengerForms) }}', 10) || 1;

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const readPassengerForms = () => {
            if (!passengerFormsContainer) {
                return [];
            }

            return Array.from(passengerFormsContainer.querySelectorAll('[data-passenger-card]')).map((card) => ({
                title: card.querySelector('[name$="[title]"]')?.value || 'Mr',
                first_name: card.querySelector('[name$="[first_name]"]')?.value || '',
                last_name: card.querySelector('[name$="[last_name]"]')?.value || '',
                dob: card.querySelector('[name$="[dob]"]')?.value || '',
                nationality: card.querySelector('[name$="[nationality]"]')?.value || '',
                gender: card.querySelector('[name$="[gender]"]')?.value || 'M',
                passport: card.querySelector('[name$="[passport]"]')?.value || '',
                passport_expiry: card.querySelector('[name$="[passport_expiry]"]')?.value || '',
            }));
        };

        const buildPassengerCard = (index, traveler) => {
            const title = traveler?.title || 'Mr';
            const gender = traveler?.gender || 'M';
            const titleText = index === 0 ? 'Lead Passenger' : `Passenger ${index + 1}`;

            return `
                <div class="passenger-card" data-passenger-card>
                    <div class="passenger-card__title">
                        <span class="passenger-card__badge">${index + 1}</span>
                        <span>${titleText}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label-sm">Title *</label>
                            <select name="passengers[${index}][title]" class="form-input">
                                <option value="Mr" ${title === 'Mr' ? 'selected' : ''}>Mr</option>
                                <option value="Mrs" ${title === 'Mrs' ? 'selected' : ''}>Mrs</option>
                                <option value="Ms" ${title === 'Ms' ? 'selected' : ''}>Ms</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label-sm">First Name *</label>
                            <input type="text" name="passengers[${index}][first_name]" value="${escapeHtml(traveler?.first_name || '')}" class="form-input" placeholder="Enter first name" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label-sm">Last Name *</label>
                            <input type="text" name="passengers[${index}][last_name]" value="${escapeHtml(traveler?.last_name || '')}" class="form-input" placeholder="Enter last name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Date of Birth *</label>
                            <input type="date" name="passengers[${index}][dob]" value="${escapeHtml(traveler?.dob || '')}" class="form-input" max="${todayDate}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Nationality *</label>
                            <input type="text" name="passengers[${index}][nationality]" value="${escapeHtml(traveler?.nationality || '')}" class="form-input" placeholder="PK, AE, US" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Gender *</label>
                            <select name="passengers[${index}][gender]" class="form-input" required>
                                <option value="M" ${gender === 'M' ? 'selected' : ''}>Male</option>
                                <option value="F" ${gender === 'F' ? 'selected' : ''}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Passport Number *</label>
                            <input type="text" name="passengers[${index}][passport]" value="${escapeHtml(traveler?.passport || '')}" class="form-input" placeholder="Enter passport number" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Passport Expiry *</label>
                            <input type="date" name="passengers[${index}][passport_expiry]" value="${escapeHtml(traveler?.passport_expiry || '')}" class="form-input" min="${todayDate}" required>
                        </div>
                    </div>
                </div>
            `;
        };

        const syncPassengerForms = () => {
            if (!passengerFormsContainer || !participantsInput) {
                return;
            }

            const participants = Math.max(1, parseInt(participantsInput.value || '1', 10));
            if (participants === lastPassengerCount) {
                return;
            }

            const currentPassengers = readPassengerForms();
            let html = '';
            for (let index = 0; index < participants; index++) {
                html += buildPassengerCard(index, currentPassengers[index] || {});
            }

            passengerFormsContainer.innerHTML = html;
            passengerFormsContainer.dataset.passengerCount = String(participants);
            lastPassengerCount = participants;
        };

        const syncPaymentSelection = () => {
            const stripeInput = document.getElementById('payStripe');
            const ngeniusInput = document.getElementById('payNgenius');
            document.getElementById('optStripe')?.classList.toggle('selected', Boolean(stripeInput?.checked));
            document.getElementById('optNgenius')?.classList.toggle('selected', Boolean(ngeniusInput?.checked));
        };

        const recalc = () => {
            const participants = Math.max(1, parseInt(participantsInput?.value || '1', 10));
            const total = unitPrice * participants;
            const totalText = `${currency} ${total.toFixed(2)}`;

            if (participantLabel) participantLabel.textContent = participants;
            if (participantBreakdown) participantBreakdown.textContent = participants;
            if (subtotalLabel) subtotalLabel.textContent = totalText;
            if (totalLabel) totalLabel.textContent = totalText;
        };

        document.querySelectorAll('input[name="payment_gateway"]').forEach((input) => {
            input.addEventListener('change', syncPaymentSelection);
        });

        participantsInput?.addEventListener('input', () => {
            recalc();
            syncPassengerForms();
        });

        syncPaymentSelection();
        recalc();
    })();
</script>
@endpush
