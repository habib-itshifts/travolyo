@extends('frontend.layouts.app')

@php
    $isB2BCheckout = $isB2BCheckout ?? false;
    $b2bCheckoutMeta = $b2bCheckoutMeta ?? [];
    $backUrl = $backUrl ?? (!$isB2BCheckout ? route('frontend.hotels.detail', $hotel->slug) : route('frontend.hotels.index'));
    $adultsCount = max(1, (int) ($b2bCheckoutMeta['adults'] ?? request('adults', request('guests', $dto->adults ?? 1))));
    $childrenCount = max(0, (int) ($b2bCheckoutMeta['children'] ?? request('children', $dto->children ?? 0)));
    $roomsCount = max(1, (int) ($b2bCheckoutMeta['rooms'] ?? request('rooms', 1)));
    $totalGuestsCount = $adultsCount + $childrenCount;

    // ── Extra Price Add-ons ───────────────────────────────────────
    $extraPriceItems = [];
    $extraPriceTotal = 0;
    if (!$isB2BCheckout && $hotel->enable_extra_price && !empty($hotel->extra_price)) {
        $selectedExtras = request()->input('extra_price', []);
        foreach ($selectedExtras as $idx => $val) {
            if ($val != '1') continue;
            $extra = $hotel->extra_price[$idx] ?? null;
            if (!$extra) continue;
            $amount = (float) ($extra['price'] ?? 0);
            if (($extra['type'] ?? '') === 'per_day') {
                $amount *= $availabilityResult->nights;
            }
            if (!empty($extra['per_person'])) {
                $amount *= $totalGuestsCount;
            }
            $extraPriceItems[] = ['name' => $extra['name'] ?? 'Add-on', 'amount' => round($amount), 'index' => $idx];
            $extraPriceTotal += round($amount);
        }
    }
@endphp
@section('title', 'Checkout — ' . ($hotel->title ?? 'Hotel') . ' — Travolyo')

@push('styles')
<style>
/* ── Checkout Top Bar ───────────────────────────── */
.checkout-topbar {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 1rem 0 0.75rem;
}
.checkout-topbar a {
    color: var(--color-text);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.checkout-topbar a:hover { color: var(--color-primary); }

/* ── Hotel Mini Header ──────────────────────────── */
.checkout-hotel-header {
    margin-bottom: 1.5rem;
}
.checkout-hotel-header__title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-dark);
    margin-bottom: 0.2rem;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.checkout-hotel-header__badge {
    font-size: 0.7rem;
    font-weight: 600;
    background: var(--color-primary);
    color: #fff;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.checkout-hotel-header__address {
    color: #888;
    font-size: 0.88rem;
}
.checkout-hotel-header__rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.35rem;
    flex-wrap: wrap;
}
.checkout-hotel-header__score {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--color-dark);
}
.checkout-hotel-header__score i { color: #fbbf24; }
.checkout-hotel-header__tag {
    font-size: 0.78rem;
    padding: 0.15rem 0.6rem;
    border-radius: 20px;
    border: 1px solid #10b981;
    color: #10b981;
    background: #f0fdf4;
}

/* ── Card shared ────────────────────────────────── */
.checkout-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.75rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    margin-bottom: 1.25rem;
}
.checkout-card__title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-dark);
    margin-bottom: 1.25rem;
}

/* ── Guest Details Form ─────────────────────────── */
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
    color: var(--color-dark);
    transition: border-color 0.2s, box-shadow 0.2s;
    background: #fff;
}
.form-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(23,195,206,0.12);
}
.form-input.is-invalid { border-color: #ef4444; }

/* ── Booking Summary Card ───────────────────────── */
.summary-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    position: sticky;
    top: 80px;
}
.summary-card__title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--color-dark);
    margin-bottom: 1rem;
}
.summary-card__hotel-name {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--color-dark);
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
    font-size: 0.875rem;
    color: #555;
    margin-bottom: 0.55rem;
}
.summary-row__label { color: #888; }
.summary-row__value { font-weight: 500; color: var(--color-dark); }
.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-dark);
    margin-top: 0.5rem;
}
.summary-price-breakdown {
    font-size: 0.82rem;
    color: #888;
    margin-bottom: 0.5rem;
}
.btn-continue {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 48px;
    background: var(--color-primary, #17c3ce) !important;
    color: #fff !important;
    border: 1px solid var(--color-primary, #17c3ce) !important;
    padding: 0.9rem 1.5rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1rem;
    margin-top: 1.25rem;
    transition: background 0.2s, border-color 0.2s;
    cursor: pointer;
}
.btn-continue:hover {
    background: var(--color-primary-dark, #13a9b3) !important;
    border-color: var(--color-primary-dark, #13a9b3) !important;
}
.btn-continue:disabled { opacity: 0.65; cursor: not-allowed; }

/* ── Validation error ───────────────────────────── */
.field-error {
    font-size: 0.78rem;
    color: #ef4444;
    margin-top: 0.25rem;
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
.payment-option:hover { border-color: var(--color-primary); }
.payment-option.selected {
    border-color: var(--color-primary);
    background: #f0fdfe;
}
.payment-option input[type="radio"] {
    accent-color: var(--color-primary);
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
}
.payment-option__logo--card { background: #1a1a2e; color: #fff; }
.payment-option__logo--bank {
    background: #f3f4f6;
    color: var(--color-dark);
    border: 1px solid #e5e7eb;
}
.payment-option__content { min-width: 0; }
.payment-option__name {
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--color-dark);
}
.payment-option__sub {
    font-size: 0.78rem;
    color: #777;
}
.card-icons { display: flex; gap: 0.4rem; margin-left: auto; }
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
.card-icon.visa { background: #1a1f71; }
.card-icon.mc { background: #eb001b; }
.card-icon.amex { background: #2e77bc; }
.pay-error {
    display: none;
    margin-top: 0.75rem;
    font-size: 0.85rem;
}
</style>
@endpush

@section('content')

<div class="container">

    {{-- Back link --}}
    <div class="checkout-topbar">
        <a href="{{ $backUrl }}">
            <i class="bi bi-arrow-left"></i> Back to {{ $hotel->title }}
        </a>
    </div>

    {{-- Hotel mini header --}}
    <div class="checkout-hotel-header">
        <h1 class="checkout-hotel-header__title">
            {{ $hotel->title }}
            @if($hotel->is_featured)
                <span class="checkout-hotel-header__badge">Best Seller</span>
            @endif
        </h1>
        <div class="checkout-hotel-header__address">
            <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $hotel->address }}
        </div>
        <div class="checkout-hotel-header__rating">
            <div class="checkout-hotel-header__score">
                <i class="bi bi-star-fill"></i>
                <span>{{ number_format($hotel->star_rate, 1) }}</span>
            </div>
            <span class="checkout-hotel-header__tag">Free Cancellation</span>
            <span class="checkout-hotel-header__tag">Breakfast Included</span>
        </div>
    </div>

@if(session('error'))
        <div class="alert alert-danger rounded-3 mb-3">{{ session('error') }}</div>
    @endif

    @php
        $allGateways = get_payment_gateways();
        $stripeGatewayKey = isset($allGateways['stripe']) ? 'stripe' : (isset($allGateways['stripe_checkout']) ? 'stripe_checkout' : 'stripe');
        $hasStripeGateway = isset($allGateways['stripe']) || isset($allGateways['stripe_checkout']);
        $hasNGeniusGateway = isset($allGateways['ngenius']);
    @endphp

    <div class="row g-4 align-items-start">

        {{-- Left: Guest Details Form --}}
        <div class="col-lg-8">
            <div class="checkout-card">
                <h2 class="checkout-card__title">Guest Details</h2>

                <form action="{{ $isB2BCheckout ? '#' : route('frontend.hotels.processCheckout', $hotel->slug) }}"
                      method="POST"
                      id="guestForm">
                    @csrf

                    {{-- Hidden booking context --}}
                    <input type="hidden" name="room_id"    value="{{ $room->id }}">
                    <input type="hidden" name="checkin"    value="{{ $dto->check_in }}">
                    <input type="hidden" name="checkout"   value="{{ $dto->check_out }}">
                    <input type="hidden" name="guests"     value="{{ $totalGuestsCount }}">
                    <input type="hidden" name="nights"     value="{{ $availabilityResult->nights }}">
                    <input type="hidden" name="total_price" value="{{ $availabilityResult->total_price + $extraPriceTotal }}">
                    <input type="hidden" name="currency" value="{{ $b2bCheckoutMeta['currency'] ?? 'USD' }}">
                    @if($isB2BCheckout)
                        <input type="hidden" name="hotel_code" value="{{ $b2bCheckoutMeta['hotel_code'] ?? '' }}">
                        <input type="hidden" name="hotel_name" value="{{ $b2bCheckoutMeta['hotel_name'] ?? '' }}">
                        <input type="hidden" name="address" value="{{ $b2bCheckoutMeta['address'] ?? '' }}">
                        <input type="hidden" name="city_code" value="{{ $b2bCheckoutMeta['city_code'] ?? '' }}">
                        <input type="hidden" name="hotel_image_url" value="{{ $b2bCheckoutMeta['hotel_image_url'] ?? '' }}">
                        <input type="hidden" name="adults" value="{{ $adultsCount }}">
                        <input type="hidden" name="children" value="{{ $childrenCount }}">
                        <input type="hidden" name="rooms" value="{{ $roomsCount }}">
                        <input type="hidden" name="occupancy" value="{{ max(1, (int) ($b2bCheckoutMeta['occupancy'] ?? ceil($totalGuestsCount / $roomsCount))) }}">
                        <input type="hidden" name="nationality" value="{{ $b2bCheckoutMeta['nationality'] ?? 'AE' }}">
                        <input type="hidden" name="agreement_code" value="{{ $b2bCheckoutMeta['agreement_code'] ?? '' }}">
                        <input type="hidden" name="room_type_code" value="{{ $b2bCheckoutMeta['room_type_code'] ?? '' }}">
                        <input type="hidden" name="room_type_name" value="{{ $b2bCheckoutMeta['room_type_name'] ?? $room->title }}">
                        <input type="hidden" name="meal_basis_code" value="{{ $b2bCheckoutMeta['meal_basis_code'] ?? '' }}">
                        <input type="hidden" name="meal_basis_name" value="{{ $b2bCheckoutMeta['meal_basis_name'] ?? '' }}">
                        <input type="hidden" name="search_number" value="{{ $b2bCheckoutMeta['search_number'] ?? '' }}">
                        <input type="hidden" name="token_id" value="{{ $b2bCheckoutMeta['token_id'] ?? '' }}">
                        <input type="hidden" name="rate_key" value="{{ $b2bCheckoutMeta['rate_key'] ?? '' }}">
                        <input type="hidden" name="agreement_price" value="{{ $b2bCheckoutMeta['agreement_price'] ?? '' }}">
                        <input type="hidden" name="data_source" value="b2b">
                    @else
                        <input type="hidden" name="hotel_code" value="LOCAL-{{ $hotel->id }}">
                        <input type="hidden" name="hotel_name" value="{{ $hotel->title }}">
                        <input type="hidden" name="address" value="{{ $hotel->address }}">
                        <input type="hidden" name="city_code" value="{{ request('location', '') }}">
                        <input type="hidden" name="hotel_image_url" value="{{ method_exists($hotel, 'getImageUrl') ? $hotel->getImageUrl() : '' }}">
                        <input type="hidden" name="adults" value="{{ $adultsCount }}">
                        <input type="hidden" name="children" value="{{ $childrenCount }}">
                        <input type="hidden" name="rooms" value="{{ $roomsCount }}">
                        <input type="hidden" name="occupancy" value="{{ max(1, (int) ceil($totalGuestsCount / $roomsCount)) }}">
                        <input type="hidden" name="nationality" value="{{ request('country_code', 'AE') }}">
                        <input type="hidden" name="agreement_code" value="">
                        <input type="hidden" name="room_type_code" value="{{ $room->id }}">
                        <input type="hidden" name="room_type_name" value="{{ $room->title }}">
                        <input type="hidden" name="meal_basis_code" value="RO">
                        <input type="hidden" name="meal_basis_name" value="Room Only">
                        <input type="hidden" name="search_number" value="">
                        <input type="hidden" name="token_id" value="">
                        <input type="hidden" name="rate_key" value="">
                        <input type="hidden" name="agreement_price" value="{{ $availabilityResult->total_price }}">
                        <input type="hidden" name="local_hotel_id" value="{{ $hotel->id }}">
                        <input type="hidden" name="data_source" value="local_db">
                        <input type="hidden" name="extra_price_total" value="{{ $extraPriceTotal }}">
                        @foreach($extraPriceItems as $extraItem)
                            <input type="hidden" name="extra_price[{{ $extraItem['index'] }}]" value="1">
                        @endforeach
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-sm">First Name *</label>
                            <input type="text"
                                   name="first_name"
                                   class="form-input @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', 'Habib') }}"
                                   placeholder="First name"
                                   required>
                            @error('first_name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Last Name *</label>
                            <input type="text"
                                   name="last_name"
                                   class="form-input @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', 'Travolyo') }}"
                                   placeholder="Last name"
                                   required>
                            @error('last_name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label-sm">Email *</label>
                            <input type="email"
                                   name="email"
                                   class="form-input @error('email') is-invalid @enderror"
                                   value="{{ old('email', 'habib@travolyo.com'  ) }}"
                                   placeholder="Email address"
                                   required>
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label-sm">Phone *</label>
                            <input type="tel"
                                   name="phone"
                                   class="form-input @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', '+971501234567') }}"
                                   placeholder="+971501234567"
                                   required>
                            @error('phone')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Address line 1</label>
                            <input type="text"
                                   name="address_line_1"
                                   class="form-input"
                                   value="{{ old('address_line_1','Dubai') }}"
                                   placeholder="Address line 1" >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Address line 2</label>
                            <input type="text"
                                   name="address_line_2"
                                   class="form-input"
                                   value="{{ old('address_line_2','UAE') }}"
                                   placeholder="Address line 2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">City</label>
                            <input type="text"
                                   name="city"
                                   class="form-input"
                                   value="{{ old('city','Dubai') }}"
                                   placeholder="Your city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">State/Province/Region</label>
                            <input type="text"
                                   name="state"
                                   class="form-input"
                                   value="{{ old('state','Dubai') }}"
                                   placeholder="State/Province/Region">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">ZIP code/Postal code</label>
                            <input type="text"
                                   name="zip_code"
                                   class="form-input"
                                   value="{{ old('zip_code','12345') }}"
                                   placeholder="ZIP code/Postal code">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Country *</label>
                            <select
                                   name="country"
                                   class="form-input"
                                   required>
                                @php $selectedCountry = old('country', 'Pakistan'); @endphp
                                @foreach(['Pakistan', 'United Arab Emirates', 'Saudi Arabia', 'Qatar', 'Kuwait', 'United Kingdom', 'United States'] as $countryOption)
                                    <option value="{{ $countryOption }}" {{ $selectedCountry === $countryOption ? 'selected' : '' }}>
                                        {{ $countryOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-sm">Special Requirements</label>
                            <textarea name="customer_notes"
                                      class="form-input"
                                      rows="5"
                                      placeholder="Special Requirements">{{ old('customer_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label-sm mb-2 d-block">Payment Method</label>

                        @if($hasStripeGateway)
                        <label class="payment-option selected" id="optStripe" for="payStripe">
                            <input type="radio" id="payStripe" name="payment_gateway" value="{{ $stripeGatewayKey }}" checked>
                            <div class="payment-option__logo payment-option__logo--card">CARD</div>
                            <div class="payment-option__content">
                                <div class="payment-option__name">Stripe</div>
                                <div class="payment-option__sub">Visa, Mastercard, Amex</div>
                            </div>
                            <div class="card-icons" aria-hidden="true">
                                <span class="card-icon visa">VISA</span>
                                <span class="card-icon mc">MC</span>
                                <span class="card-icon amex">AMEX</span>
                            </div>
                        </label>
                        @endif

                        @if($hasNGeniusGateway)
                        <label class="payment-option {{ !$hasStripeGateway ? 'selected' : '' }}" id="optNgenius" for="payNgenius">
                            <input type="radio" id="payNgenius" name="payment_gateway" value="ngenius" {{ !$hasStripeGateway ? 'checked' : '' }}>
                            <div class="payment-option__logo payment-option__logo--bank">N</div>
                            <div class="payment-option__content">
                                <div class="payment-option__name">N-Genius</div>
                                <div class="payment-option__sub">Secure hosted checkout by Network International</div>
                            </div>
                        </label>
                        @endif

                        @if(!$hasStripeGateway && !$hasNGeniusGateway)
                            <div class="alert alert-warning py-2 px-3 mb-0 small">
                                Payment gateways are not configured.
                            </div>
                        @endif
                    </div>

                    {{-- Mobile: show Continue button here too on small screens --}}
                    <div class="d-lg-none mt-4">
                        <button type="submit" class="btn-continue" id="mobilePayBtn">
                            Continue to Payment
                        </button>
                        <div id="mobilePayError" class="alert alert-danger pay-error mb-0"></div>
                    </div>

                </form>
            </div>
        </div>

        {{-- Right: Booking Summary --}}
        <div class="col-lg-4">
            <div class="summary-card">
                <div class="summary-card__title">Booking Summary</div>

                <div class="summary-card__hotel-name">{{ $hotel->title }}</div>
                <div class="summary-card__hotel-addr">{{ $hotel->address }}</div>

                <hr class="summary-card__divider">

                <div class="summary-row">
                    <span class="summary-row__label">Room Type</span>
                    <span class="summary-row__value">{{ $room->title }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Check-in</span>
                    <span class="summary-row__value">{{ \Carbon\Carbon::parse($dto->check_in)->format('Y-m-d') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Check-out</span>
                    <span class="summary-row__value">{{ \Carbon\Carbon::parse($dto->check_out)->format('Y-m-d') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Guests</span>
                    <span class="summary-row__value">
                        {{ $totalGuestsCount }}
                        ({{ $adultsCount }} Adult{{ $adultsCount > 1 ? 's' : '' }}
                        @if($childrenCount > 0), {{ $childrenCount }} Child{{ $childrenCount > 1 ? 'ren' : '' }}@endif)
                    </span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Rooms</span>
                    <span class="summary-row__value">{{ $roomsCount }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Nights</span>
                    <span class="summary-row__value">{{ $availabilityResult->nights }}</span>
                </div>

                <hr class="summary-card__divider">

                @php
                    $TAX_RATE  = 0;
                    $subtotal  = $availabilityResult->total_price;
                    $tax       = round($subtotal * $TAX_RATE);
                    $grandTotal= $subtotal + $tax + $extraPriceTotal;
                @endphp

                <div class="summary-price-breakdown">
                    ${{ number_format($availabilityResult->price_per_night) }}
                    × {{ $availabilityResult->nights }} nights
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Subtotal</span>
                    <span class="summary-row__value">${{ number_format($subtotal) }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row__label">Taxes &amp; fees (4%)</span>
                    <span class="summary-row__value">${{ number_format($tax) }}</span>
                </div>

                @foreach($extraPriceItems as $extraItem)
                <div class="summary-row">
                    <span class="summary-row__label">{{ $extraItem['name'] }}</span>
                    <span class="summary-row__value">${{ number_format($extraItem['amount']) }}</span>
                </div>
                @endforeach

                <hr class="summary-card__divider">

                <div class="summary-total">
                    <span>Total</span>
                    <span>${{ number_format($grandTotal) }}</span>
                </div>

                {{-- Desktop: Continue to Payment button submits the guest form --}}
                <button type="submit"
                        form="guestForm"
                        class="btn-continue"
                        id="desktopPayBtn">
                    Continue to Payment
                </button>
                <div id="desktopPayError" class="alert alert-danger pay-error mb-0"></div>
            </div>
        </div>

    </div>
</div>

{{-- Why Book section --}}
<div class="why-book" style="background:#f9fafb; padding:3rem 0; margin-top:2rem;">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @php
                $reasons = [
                    ['bi-patch-check',   'Verified Properties',  'Every home is verified for quality and accuracy.'],
                    ['bi-tags',          'Best Price Guarantee',  "Find a lower price? We'll match it."],
                    ['bi-headset',       '24/7 Support',          'Our team is here to help anytime you need.'],
                    ['bi-arrow-repeat',  'Flexible Cancellation', 'Plans change? Most bookings offer free cancellation.'],
                ];
            @endphp
            @foreach($reasons as [$icon, $label, $desc])
            <div class="col-6 col-md-3 text-center">
                <div style="width:60px;height:60px;background:var(--color-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;color:#fff;font-size:1.3rem;">
                    <i class="bi {{ $icon }}"></i>
                </div>
                <div style="font-weight:700;color:var(--color-dark);margin-bottom:0.3rem;font-size:0.9rem;">{{ $label }}</div>
                <div style="font-size:0.8rem;color:#777;">{{ $desc }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const IS_B2B_CHECKOUT = @json($isB2BCheckout);
    const RESERVE_URL = @json($b2bCheckoutMeta['reserve_url'] ?? route('hotel.b2b.add_to_cart'));
    const PAY_URL = @json(route('frontend.hotels.pay'));
    const form = document.getElementById('guestForm');
    if (!form || !RESERVE_URL) return;

    const desktopBtn = document.getElementById('desktopPayBtn');
    const mobileBtn = document.getElementById('mobilePayBtn');
    const desktopErr = document.getElementById('desktopPayError');
    const mobileErr = document.getElementById('mobilePayError');
    const buttons = [desktopBtn, mobileBtn].filter(Boolean);
    const errorBoxes = [desktopErr, mobileErr].filter(Boolean);
    const bookingCodeRegex = /\/booking\/([^\/]+)\/checkout/i;

    const setLoading = (loading) => {
        buttons.forEach((btn) => {
            btn.disabled = loading;
            if (loading) {
                if (!btn.dataset.originalText) btn.dataset.originalText = btn.textContent.trim();
                btn.textContent = 'Please wait...';
            } else if (btn.dataset.originalText) {
                btn.textContent = btn.dataset.originalText;
            }
        });
    };

    const setError = (message = '') => {
        errorBoxes.forEach((box) => {
            if (!box) return;
            box.textContent = message;
            box.style.display = message ? 'block' : 'none';
        });
    };

    const toNum = (value, fallback = 0) => {
        const n = Number(value);
        return Number.isFinite(n) ? n : fallback;
    };

    const parseBookingCode = (url) => {
        const match = String(url || '').match(bookingCodeRegex);
        return match && match[1] ? match[1] : '';
    };

    const getSelectedGateway = () => {
        const selected = form.querySelector('input[name="payment_gateway"]:checked');
        return selected ? selected.value : '';
    };

    const reserveDraftBooking = async (fd) => {
        const reservePayload = {
            data_source: String(fd.get('data_source') || (IS_B2B_CHECKOUT ? 'b2b' : 'local_db')),
            hotel_code: String(fd.get('hotel_code') || ''),
            hotel_name: String(fd.get('hotel_name') || ''),
            address: String(fd.get('address') || ''),
            city_code: String(fd.get('city_code') || ''),
            hotel_image_url: String(fd.get('hotel_image_url') || ''),
            check_in: String(fd.get('checkin') || ''),
            check_out: String(fd.get('checkout') || ''),
            adults: toNum(fd.get('adults') || fd.get('guests'), 1),
            children: toNum(fd.get('children'), 0),
            rooms: toNum(fd.get('rooms'), 1),
            occupancy: toNum(fd.get('occupancy') || fd.get('guests'), 1),
            nationality: String(fd.get('nationality') || 'AE'),
            agreement_code: String(fd.get('agreement_code') || ''),
            room_type_code: String(fd.get('room_type_code') || ''),
            room_type_name: String(fd.get('room_type_name') || ''),
            meal_basis_code: String(fd.get('meal_basis_code') || ''),
            meal_basis_name: String(fd.get('meal_basis_name') || ''),
            search_number: String(fd.get('search_number') || ''),
            token_id: String(fd.get('token_id') || ''),
            rate_key: String(fd.get('rate_key') || ''),
            agreement_price: String(fd.get('agreement_price') || ''),
            total_price: toNum(fd.get('total_price'), 0),
            currency: String(fd.get('currency') || 'USD'),
            local_hotel_id: toNum(fd.get('local_hotel_id'), 0),
            room_id: toNum(fd.get('room_id'), 0),
        };

        const reserveResponse = await fetch(RESERVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify(reservePayload),
        });
        const reserveJson = await reserveResponse.json();
        if (!(reserveJson.success && reserveJson.checkout_url)) {
            throw new Error(reserveJson.message || 'Unable to prepare booking for payment.');
        }

        const bookingCode = parseBookingCode(reserveJson.checkout_url);
        if (!bookingCode) throw new Error('Unable to resolve booking code.');
        return bookingCode;
    };

    const processPaymentGateway = async (bookingCode, fd, gateway) => {
        const payResponse = await fetch(PAY_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                booking_code: bookingCode,
                payment_gateway: gateway,
                first_name: String(fd.get('first_name') || ''),
                last_name: String(fd.get('last_name') || ''),
                email: String(fd.get('email') || ''),
                phone: String(fd.get('phone') || ''),
            }),
        });
        const payJson = await payResponse.json();
        const redirectUrl = payJson.url || payJson.redirect || payJson.checkout_url || '';
        if (!redirectUrl) {
            throw new Error(payJson.message || 'Payment initiation failed.');
        }
        return redirectUrl;
    };

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        setError('');

        const gateway = getSelectedGateway();
        if (!gateway) {
            setError('Please select a payment gateway (Stripe or N-Genius).');
            return;
        }

        setLoading(true);
        try {
            const bookingCode = await reserveDraftBooking(fd);
            const redirectUrl = await processPaymentGateway(bookingCode, fd, gateway);
            window.location.href = redirectUrl;
        } catch (err) {
            setError(err?.message || 'Unable to continue payment. Please verify details and try again.');
        } finally {
            setLoading(false);
        }
    });

    form.querySelectorAll('input[name="payment_gateway"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.payment-option').forEach((el) => el.classList.remove('selected'));
            if (radio.id === 'payStripe') document.getElementById('optStripe')?.classList.add('selected');
            if (radio.id === 'payNgenius') document.getElementById('optNgenius')?.classList.add('selected');
        });
    });
})();
</script>
@endpush

