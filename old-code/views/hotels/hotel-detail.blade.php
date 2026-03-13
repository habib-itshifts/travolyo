@extends('frontend.layouts.app')

@php
    $isB2BDeal = $isB2BDeal ?? false;
    $search = $search ?? [];
    $b2bMeta = $b2bMeta ?? [];
@endphp

@section('title', ($hotel->title ?? 'Hotel Detail') . ' — Travolyo')

@push('styles')
<style>
/* ── Detail Header ─────────────────────────────── */
.detail-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    margin-bottom: 0.5rem;
}
.detail-topbar__back {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: var(--color-text);
    font-size: 0.9rem;
    text-decoration: none;
    font-weight: 500;
}
.detail-topbar__back:hover { color: var(--color-primary); }
.detail-topbar__actions { display: flex; gap: 0.75rem; }
.detail-topbar__btn {
    width: 38px; height: 38px;
    border-radius: 50%;
    border: 1px solid #e0e0e0;
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    color: var(--color-text);
    cursor: pointer;
    transition: all 0.2s;
}
.detail-topbar__btn:hover { border-color: var(--color-primary); color: var(--color-primary); }

/* ── Hotel Hero Info ───────────────────────────── */
.hotel-hero { margin-bottom: 1.25rem; }
.hotel-hero__title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--color-dark);
    margin-bottom: 0.35rem;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.hotel-hero__badge {
    font-size: 0.7rem;
    font-weight: 600;
    background: var(--color-primary);
    color: #fff;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.hotel-hero__address {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.6rem;
}
.hotel-hero__rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.hotel-hero__score {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-weight: 600;
    color: var(--color-dark);
}
.hotel-hero__score i { color: #fbbf24; font-size: 1.1rem; }
.hotel-hero__reviews { color: #999; font-size: 0.85rem; }
.hotel-hero__tag {
    font-size: 0.8rem;
    padding: 0.2rem 0.7rem;
    border-radius: 20px;
    border: 1px solid #e0e0e0;
    color: #555;
}
.hotel-hero__tag--green { border-color: #10b981; color: #10b981; background: #f0fdf4; }

/* ── Photo Gallery ─────────────────────────────── */
.hotel-gallery { margin-bottom: 2rem; border-radius: 16px; overflow: hidden; }
.hotel-gallery__grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 8px;
    height: 420px;
}
.hotel-gallery__main img {
    width: 100%; height: 100%;
    object-fit: cover;
    border-radius: 12px 0 0 12px;
}
.hotel-gallery__side {
    display: grid;
    grid-template-rows: 1fr 1fr;
    gap: 8px;
}
.hotel-gallery__thumb { position: relative; overflow: hidden; }
.hotel-gallery__thumb:first-child img { border-radius: 0 12px 0 0; }
.hotel-gallery__thumb--last img       { border-radius: 0 0 12px 0; }
.hotel-gallery__thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
}
.hotel-gallery__more-btn {
    position: absolute; inset: 0;
    background: rgba(0,0,0,0.45);
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
    border-radius: 0 0 12px 0;
}
.hotel-gallery__more-btn:hover { background: rgba(0,0,0,0.6); }

/* ── Content Sections ──────────────────────────── */
.detail-section {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.detail-section__title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-dark);
    margin-bottom: 1rem;
}
.detail-section p { color: #555; line-height: 1.7; margin: 0; }

/* ── Highlights ────────────────────────────────── */
.highlights-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem 1rem;
}
.highlight-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #555;
    font-size: 0.9rem;
}
.highlight-item i { color: var(--color-primary); font-size: 0.95rem; }

/* ── Amenities ─────────────────────────────────── */
.amenities-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
}
.amenity-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    text-align: center;
}
.amenity-item i {
    font-size: 1.4rem;
    color: var(--color-primary);
    background: #f0fdfe;
    width: 48px; height: 48px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}
.amenity-item span { font-size: 0.78rem; color: #555; }

/* ── Room Cards ────────────────────────────────── */
.room-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #f0f0f0;
    border-radius: 10px;
    margin-bottom: 0.25rem;
    transition: background 0.2s, border-color 0.2s;
    cursor: default;
}
.room-card:last-child { border-bottom: none; }
.room-card--selected {
    background: #f0fdfe;
    border: 1.5px solid var(--color-primary) !important;
    border-bottom: 1.5px solid var(--color-primary) !important;
}
.room-card--unavailable {
    opacity: 0.55;
    background: #fafafa;
}
.room-card--unavailable .room-card__price-amount { color: #aaa; }
.room-card--checking {
    opacity: 0.75;
}
.room-card__name { font-weight: 600; color: var(--color-dark); margin-bottom: 0.25rem; }
.room-card__specs { font-size: 0.85rem; color: #777; margin-bottom: 0.4rem; }
.room-card__dot { margin: 0 0.3rem; }
.room-card__tags { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.room-card__tag {
    font-size: 0.75rem;
    background: #f5f5f5;
    color: #555;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    display: flex; align-items: center; gap: 0.25rem;
}
.room-card__action { text-align: right; flex-shrink: 0; }
.room-card__price-label { font-size: 0.75rem; color: #999; display: block; }
.room-card__price-amount { font-size: 1.2rem; font-weight: 700; color: var(--color-primary); display: block; }
.btn-select {
    display: inline-block !important;
    visibility: visible !important;
    /* background: var(--color-primary); */
    color: #fff;
    border: none;
    padding: 0.4rem 1.1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 0.4rem;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-select:hover { background: var(--color-primary-dark, #13a9b3); color: #fff; }
.room-card--selected .btn-select {
    background: var(--color-dark, #1a1a2e);
    color: #fff;
}

/* ── Booking Widget ────────────────────────────── */
.booking-widget__heading {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-dark);
    margin-bottom: 1rem;
}
.booking-widget__no-room {
    text-align: center;
    padding: 1.25rem 0 0.75rem;
    color: #aaa;
}
.booking-widget__no-room i { font-size: 1.75rem; color: var(--color-primary); opacity: 0.5; display: block; margin-bottom: 0.4rem; }
.booking-widget__no-room p { font-size: 0.875rem; margin: 0; }
.booking-widget__room-name {
    font-size: 0.8rem;
    color: #999;
    margin-top: 0.15rem;
}
.booking-widget {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    position: sticky;
    top: 80px;
}
.booking-widget__price { margin-bottom: 1.25rem; }
.booking-widget__price-old {
    font-size: 1rem;
    color: #aaa;
    text-decoration: line-through;
    margin-right: 0.4rem;
}
.booking-widget__price-current {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--color-primary);
}
.booking-widget__price-unit { font-size: 0.85rem; color: #999; }
.booking-widget__dates { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.booking-widget__field label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #666;
    display: block;
    margin-bottom: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.booking-widget__input-wrap {
    position: relative;
}
.booking-widget__input-wrap i {
    position: absolute;
    left: 0.7rem;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    font-size: 0.9rem;
    pointer-events: none;
}
.booking-widget__input-wrap .form-control,
.booking-widget__input-wrap .form-select {
    padding-left: 2rem;
    font-size: 0.875rem;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}
.booking-widget__input-wrap .form-control:focus,
.booking-widget__input-wrap .form-select:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(23,195,206,0.15);
}
.booking-guests-picker {
    position: relative;
}
.booking-guests-picker .guests-trigger {
    cursor: pointer;
}
.booking-guests-picker .guests-summary-input {
    background: #fff;
    cursor: pointer;
}
.booking-guests-picker .guests-menu {
    position: absolute;
    left: 0;
    right: 0;
    top: calc(100% + 0.45rem);
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 12px 26px rgba(0, 0, 0, 0.12);
    padding: 0.85rem;
    z-index: 25;
    display: none;
}
.booking-guests-picker.is-open .guests-menu {
    display: block;
}
.booking-guests-picker .guest-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.65rem;
}
.booking-guests-picker .guest-row:last-child {
    margin-bottom: 0;
}
.booking-guests-picker .guest-label {
    font-weight: 700;
    color: var(--color-dark);
    font-size: 0.92rem;
    line-height: 1.1;
}
.booking-guests-picker .guest-sub {
    color: #7b8496;
    font-size: 0.8rem;
    line-height: 1.1;
    margin-top: 0.25rem;
}
.booking-guests-picker .counter-wrap {
    display: flex;
    align-items: center;
    gap: 0.45rem;
}
.booking-guests-picker .counter-btn {
    width: 32px;
    height: 32px;
    border: 1px solid #d4dbe7;
    border-radius: 9px;
    background: #f9fbff;
    color: #1f2a44;
    font-size: 1rem;
    font-weight: 700;
    line-height: 1;
    padding: 0;
}
.booking-guests-picker .counter-val {
    min-width: 18px;
    text-align: center;
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-primary);
}
.booking-widget__breakdown {
    background: #f9fafb;
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
}
.booking-widget__breakdown-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    color: #555;
    margin-bottom: 0.4rem;
}
.booking-widget__breakdown-total {
    display: flex;
    justify-content: space-between;
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-dark);
    border-top: 1px solid #e0e0e0;
    padding-top: 0.6rem;
    margin-top: 0.4rem;
}
.btn-reserve {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    width: 100%;
    min-height: 48px;
    background: var(--color-primary, #17c3ce) !important;
    color: #fff !important;
    border: 1px solid var(--color-primary, #17c3ce) !important;
    padding: 0.85rem 1.5rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1rem;
    transition: background 0.2s;
}
.btn-reserve:hover {
    background: var(--color-primary-dark, #13a9b3) !important;
    border-color: var(--color-primary-dark, #13a9b3) !important;
    color: #fff !important;
}
.btn-reserve:disabled {
    opacity: 0.75;
    cursor: not-allowed;
}
.booking-widget__extras {
    margin-top: 1rem;
    border-top: 1px solid #f0f0f0;
    padding-top: 0.75rem;
}
.booking-widget__extras-title {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #888;
    margin-bottom: 0.5rem;
}
.booking-widget__extra-item {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    cursor: pointer;
    padding: 0.45rem 0;
    border-bottom: 1px solid #f5f5f5;
    margin: 0;
}
.booking-widget__extra-item:last-child { border-bottom: none; }
.booking-widget__extra-item input[type="checkbox"] {
    margin-top: 0.2rem;
    accent-color: var(--color-primary);
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}
.booking-widget__extra-info {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}
.booking-widget__extra-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--color-dark);
}
.booking-widget__extra-desc {
    font-size: 0.78rem;
    color: #888;
}
.booking-widget__trust {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
}
.booking-widget__trust-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: #666;
}

/* ── Why Book Section ──────────────────────────── */
.why-book {
    background: #f9fafb;
    padding: 3rem 0;
    margin-top: 2rem;
}
.why-book__title {
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 2rem;
    color: var(--color-dark);
}
.why-book__item { text-align: center; padding: 1rem; }
.why-book__icon {
    width: 64px; height: 64px;
    background: var(--color-primary);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
    color: #fff;
    font-size: 1.4rem;
    font-weight: 700;
}
.why-book__label { font-weight: 700; color: var(--color-dark); margin-bottom: 0.3rem; }
.why-book__desc  { font-size: 0.85rem; color: #777; }

@media (max-width: 768px) {
    .hotel-gallery__grid { grid-template-columns: 1fr; height: auto; }
    .hotel-gallery__side { display: none; }
    .hotel-gallery__main img { border-radius: 12px; height: 250px; }
    .amenities-grid { grid-template-columns: repeat(3, 1fr); }
    .highlights-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<div class="container">

    {{-- Top bar --}}
    <div class="detail-topbar">
        <a href="{{ route('frontend.hotels.index') }}" class="detail-topbar__back">
            <i class="bi bi-arrow-left"></i> Back to Hotels
        </a>
        <div class="detail-topbar__actions">
            <button class="detail-topbar__btn" title="Save"><i class="bi bi-heart"></i></button>
            <button class="detail-topbar__btn" title="Share"><i class="bi bi-share"></i></button>
        </div>
    </div>

    {{-- Hotel headline --}}
    <div class="hotel-hero">
        <h1 class="hotel-hero__title">
            {{ $hotel->title }}
            @if($hotel->is_featured)
                <span class="hotel-hero__badge">Best Seller</span>
            @endif
        </h1>
        <p class="hotel-hero__address">
            <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $hotel->address }}
        </p>
        <div class="hotel-hero__rating">
            <div class="hotel-hero__score">
                <i class="bi bi-star-fill"></i>
                <span>{{ number_format($hotel->star_rate, 1) }}</span>
            </div>
            <span class="hotel-hero__reviews">({{ $hotel->review_score ?? 0 }} reviews)</span>
            @foreach($hotel->propertyType as $type)
                <span class="hotel-hero__tag">
                    <i class="bi {{ $type->icon ?: 'bi-building' }} me-1"></i>{{ $type->name }}
                </span>
            @endforeach
            @if($isB2BDeal && $hotel->propertyType->isEmpty())
                <span class="hotel-hero__tag">Property type not available in B2B</span>
            @endif
        </div>
    </div>

    {{-- Photo Gallery --}}
    @include('frontend.hotels.partials._photo-gallery')

    {{-- Two-column layout --}}
    <div class="row g-4">

        {{-- Left: Details --}}
        <div class="col-lg-8">

            {{-- About --}}
            <div class="detail-section">
                <h2 class="detail-section__title">About This Hotel</h2>
                @if(!empty(trim(strip_tags((string) ($hotel->content ?? '')))))
                    <p>{!! $hotel->content !!}</p>
                @endif
            </div>

            {{-- Property Highlights --}}
            {{-- 
            <div class="detail-section">
                <h2 class="detail-section__title">Property Highlights</h2>
                <div class="highlights-grid">
                    @php
                        $highlights = [
                            'Rooftop bar with city views',
                            'World-class spa with indoor pool',
                            'Michelin-starred restaurant',
                            '24-hour concierge service',
                            'Complimentary airport transfers',
                            'Free high-speed WiFi',
                        ];
                    @endphp
                    @foreach($highlights as $item)
                    <div class="highlight-item">
                        <i class="bi bi-check2-circle"></i>
                        <span>{{ $item }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            --}}

            {{-- Facilities --}}
            @if($hotel->facilities->isNotEmpty() || $isB2BDeal)
            <div class="detail-section">
                <h2 class="detail-section__title">Facilities</h2>
                @if($hotel->facilities->isNotEmpty())
                    <div class="amenities-grid">
                        @foreach($hotel->facilities as $item)
                        <div class="amenity-item">
                            <i class="bi bi-check2-circle"></i>
                            <span>{{ $item->name }}</span>
                        </div>
                        @endforeach
                    </div>
                @elseif($isB2BDeal)
                    <p class="text-muted small mb-0">Facilities details are not available in B2B.</p>
                @endif
            </div>
            @endif

            {{-- Services --}}
            @if($hotel->services->isNotEmpty() || $isB2BDeal)
            <div class="detail-section">
                <h2 class="detail-section__title">Services</h2>
                @if($hotel->services->isNotEmpty())
                    <div class="amenities-grid">
                        @foreach($hotel->services as $item)
                        <div class="amenity-item">
                            <i class="bi {{ $item->icon ?: 'bi bi-check2-circle' }}"></i>
                            <span>{{ $item->name }}</span>
                        </div>
                        @endforeach
                    </div>
                @elseif($isB2BDeal)
                    <p class="text-muted small mb-0">Services details are not available in B2B.</p>
                @endif
            </div>
            @endif

            {{-- Available Rooms --}}
            @if($hotel->rooms->isNotEmpty())
            <div class="detail-section" id="availableRoomsSection">
                <h2 class="detail-section__title">Available Rooms</h2>
                @foreach($hotel->rooms as $room)
                    @include('frontend.hotels.partials._room-card', ['room' => $room])
                @endforeach
            </div>
            @elseif($isB2BDeal)
            <div class="detail-section" id="availableRoomsSection">
                <h2 class="detail-section__title">Available Rooms</h2>
                <div id="b2bRoomsContainer">
                    <div class="text-muted small">Select dates to check availability...</div>
                </div>
            </div>
            @endif

            {{-- Check-in / Check-out Policy --}}
            @if($hotel->check_in_time || $hotel->check_out_time || $isB2BDeal)
            <div class="detail-section">
                <h2 class="detail-section__title">Check-in &amp; Check-out</h2>
                @if($hotel->check_in_time || $hotel->check_out_time)
                    <div class="row g-3">
                        @if($hotel->check_in_time)
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-in-right fs-4 text-primary"></i>
                                <div>
                                    <div class="fw-bold" style="font-size:0.8rem;color:#999;text-transform:uppercase;">Check-in from</div>
                                    <div class="fw-semibold">{{ $hotel->check_in_time }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($hotel->check_out_time)
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right fs-4 text-danger"></i>
                                <div>
                                    <div class="fw-bold" style="font-size:0.8rem;color:#999;text-transform:uppercase;">Check-out until</div>
                                    <div class="fw-semibold">{{ $hotel->check_out_time }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                @elseif($isB2BDeal)
                    <p class="text-muted small mb-0">Check-in and check-out policy is not available in B2B.</p>
                @endif
            </div>
            @endif

        </div>

        {{-- Right: Booking Widget --}}
        <div class="col-lg-4">
            @if(!$isB2BDeal)
            @include('frontend.hotels.partials._booking-widget')
            @else
            <div class="booking-widget" id="bookingWidget">
                <h6 class="booking-widget__heading">Book Your Stay</h6>
                <div id="widgetNoRoom" class="booking-widget__no-room">
                    <i class="bi bi-cursor"></i>
                    <p>Select a room below to see pricing</p>
                </div>
                <div id="widgetPriceSection" style="display:none;" class="booking-widget__price">
                    <span class="booking-widget__price-current" id="widgetPriceAmount">${{ number_format((float) ($search['price'] ?? 0)) }}</span>
                    <span class="booking-widget__price-unit">/ night</span>
                    <div class="booking-widget__room-name" id="widgetRoomName"></div>
                </div>
                <form id="bookingForm">
                    <input type="hidden" id="selectedRoomId" value="">
                    <input type="hidden" id="selectedAgreementCode" value="">
                    <input type="hidden" id="selectedRoomTypeCode" value="">
                    <input type="hidden" id="selectedRoomTypeName" value="">
                    <input type="hidden" id="selectedMealBasisCode" value="">
                    <input type="hidden" id="selectedMealBasisName" value="">
                    <input type="hidden" id="selectedSearchNumber" value="">
                    <input type="hidden" id="selectedTokenId" value="">
                    <input type="hidden" id="selectedRateKey" value="">
                    <input type="hidden" id="selectedAgreementPrice" value="">
                    <input type="hidden" id="selectedCurrency" value="">

                    <div class="booking-widget__dates">
                        <div class="booking-widget__field">
                            <label>Check-in</label>
                            <div class="booking-widget__input-wrap">
                                <i class="bi bi-calendar3"></i>
                                <input type="date" id="checkinDate"
                                       value="{{ $search['check_in'] ?? '' }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="booking-widget__field">
                            <label>Check-out</label>
                            <div class="booking-widget__input-wrap">
                                <i class="bi bi-calendar3"></i>
                                <input type="date" id="checkoutDate"
                                       value="{{ $search['check_out'] ?? '' }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="booking-widget__field mt-3">
                        <label>Guests</label>
                        <div class="booking-guests-picker" id="detailGuestsPicker">
                            <div class="booking-widget__input-wrap guests-trigger" role="button" tabindex="0" aria-expanded="false">
                                <i class="bi bi-people"></i>
                                <input id="guestsSummaryDisplay" type="text" class="form-control guests-summary-input" readonly>
                            </div>
                            <div class="guests-menu" id="detailGuestsMenu">
                                <div class="guest-row">
                                    <div>
                                        <div class="guest-label">Adults</div>
                                        <div class="guest-sub">Above 12 years</div>
                                    </div>
                                    <div class="counter-wrap">
                                        <button type="button" class="counter-btn" data-counter="adults" data-delta="-1">-</button>
                                        <span class="counter-val" id="adultsCounterVal">1</span>
                                        <button type="button" class="counter-btn" data-counter="adults" data-delta="1">+</button>
                                    </div>
                                </div>
                                <div class="guest-row">
                                    <div>
                                        <div class="guest-label">Children</div>
                                        <div class="guest-sub">Below 12 years</div>
                                    </div>
                                    <div class="counter-wrap">
                                        <button type="button" class="counter-btn" data-counter="children" data-delta="-1">-</button>
                                        <span class="counter-val" id="childrenCounterVal">0</span>
                                        <button type="button" class="counter-btn" data-counter="children" data-delta="1">+</button>
                                    </div>
                                </div>
                                <div class="guest-row">
                                    <div>
                                        <div class="guest-label">Unit</div>
                                        <div class="guest-sub">Rooms</div>
                                    </div>
                                    <div class="counter-wrap">
                                        <button type="button" class="counter-btn" data-counter="rooms" data-delta="-1">-</button>
                                        <span class="counter-val" id="roomsCounterVal">1</span>
                                        <button type="button" class="counter-btn" data-counter="rooms" data-delta="1">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="adultsInput" value="{{ max(1, (int) ($search['adults'] ?? 1)) }}">
                    <input type="hidden" id="childrenInput" value="{{ max(0, (int) ($search['children'] ?? 0)) }}">
                    <input type="hidden" id="roomsInput" value="{{ max(1, (int) ($search['rooms'] ?? $search['unit'] ?? 1)) }}">
                    <input type="hidden" id="guestsTotalInput" value="{{ max(1, (int) ($search['adults'] ?? 1) + (int) ($search['children'] ?? 0)) }}">
                    <div class="booking-widget__breakdown" id="priceBreakdown" style="display:none;">
                        <div class="booking-widget__breakdown-row">
                            <span><span id="nightsPriceLabel">$0</span> x <span id="nightsCount">0</span> nights</span>
                            <span id="subtotalAmount">$0</span>
                        </div>
                        <div class="booking-widget__breakdown-row">
                            <span>Taxes &amp; fees (4%)</span>
                            <span id="taxAmount">$0</span>
                        </div>
                        <div class="booking-widget__breakdown-total">
                            <span>Total</span>
                            <span id="totalAmount">$0</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-reserve w-100 mt-3" id="b2bReserveBtn">
                        <i class="bi bi-credit-card me-2"></i>Reserve Now
                    </button>
                </form>
                <div class="booking-widget__trust">
                    <div class="booking-widget__trust-item">
                        <i class="bi bi-shield-check text-warning"></i>Secure Booking
                    </div>
                    <div class="booking-widget__trust-item">
                        <i class="bi bi-check-circle text-success"></i>Instant Confirmation
                    </div>
                    <div class="booking-widget__trust-item">
                        <i class="bi bi-patch-check text-info"></i>Best Price Guarantee
                    </div>
                    <div class="booking-widget__trust-item">
                        <i class="bi bi-headset text-primary"></i>24/7 Support
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>

</div>

{{-- Why Book With Us --}}
<div class="why-book">
    <div class="container">
        <h2 class="why-book__title">Why Book Homes With Us?</h2>
        <div class="row g-4">
            @php
                $reasons = [
                    ['bi-patch-check',   'Verified Properties', 'Every home is verified for quality and accuracy.'],
                    ['bi-tags',          'Best Price Guarantee', 'Find a lower price? We\'ll match it.'],
                    ['bi-headset',       '24/7 Support',         'Our team is here to help anytime you need.'],
                    ['bi-arrow-repeat',  'Flexible Cancellation','Plans change? Most bookings offer free cancellation.'],
                ];
            @endphp
            @foreach($reasons as [$icon, $label, $desc])
            <div class="col-6 col-md-3">
                <div class="why-book__item">
                    <div class="why-book__icon"><i class="bi {{ $icon }}"></i></div>
                    <div class="why-book__label">{{ $label }}</div>
                    <div class="why-book__desc">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
@if(!$isB2BDeal)
<script>
(function () {
    // ── Config ────────────────────────────────────────────────────
    const AVAILABILITY_URL = '{{ route('frontend.hotels.availability', ['slug' => $hotel->slug]) }}';
    const CSRF_TOKEN       = '{{ csrf_token() }}';

    // ── State ─────────────────────────────────────────────────────
    let selectedRoomId   = null;
    let availabilityData = {}; // keyed by room_id

    // ── Helpers ───────────────────────────────────────────────────

    function asNumber(value, fallback) {
        const n = Number(value);
        return Number.isFinite(n) ? n : fallback;
    }

    function getCheckin()  { return document.getElementById('checkinDate')?.value || ''; }
    function getCheckout() { return document.getElementById('checkoutDate')?.value || ''; }
    function getAdults()   { return Math.max(1, asNumber(document.getElementById('adultsInput')?.value, 1)); }
    function getChildren() { return Math.max(0, asNumber(document.getElementById('childrenInput')?.value, 0)); }
    function getRooms()    { return Math.max(1, asNumber(document.getElementById('roomsInput')?.value, 1)); }
    function getGuests()   { return Math.max(1, getAdults() + getChildren()); }

    function setRoomCardState(roomId, state) {
        // state: 'loading' | 'available' | 'unavailable' | 'default'
        const card = document.getElementById('room-card-' + roomId);
        if (!card) return;
        const btn = card.querySelector('.room-select-btn');
        card.classList.remove('room-card--unavailable', 'room-card--checking');

        if (state === 'loading') {
            card.classList.add('room-card--checking');
            if (btn) { btn.disabled = true; btn.textContent = 'Checking…'; }
        } else if (state === 'available') {
            if (btn) { btn.disabled = false; btn.textContent = card.dataset.roomId == selectedRoomId ? 'Selected' : 'Select'; }
        } else if (state === 'unavailable') {
            card.classList.add('room-card--unavailable');
            if (btn) { btn.disabled = true; btn.textContent = 'Unavailable'; }
            // Deselect if this was the selected room
            if (card.dataset.roomId == selectedRoomId) {
                card.classList.remove('room-card--selected');
                selectedRoomId = null;
                document.getElementById('widgetNoRoom').style.display       = 'block';
                document.getElementById('widgetPriceSection').style.display = 'none';
            }
        } else {
            // default: no dates selected
            if (btn) { btn.disabled = false; btn.textContent = card.dataset.roomId == selectedRoomId ? 'Selected' : 'Select'; }
        }
    }

    function applyAvailabilityResults(results) {
        availabilityData = {};
        results.forEach(function (room) {
            availabilityData[room.room_id] = room;
            setRoomCardState(room.room_id, room.available ? 'available' : 'unavailable');

            // Update price label in room card to reflect date-specific price
            const card = document.getElementById('room-card-' + room.room_id);
            if (card && room.available) {
                const priceEl = card.querySelector('.room-card__price-amount');
                if (priceEl) priceEl.textContent = '$' + room.price_per_night.toLocaleString();
                card.dataset.price = room.price_per_night;
            }
        });

        // If a room was already selected, refresh widget price with updated data
        if (selectedRoomId && availabilityData[selectedRoomId] && availabilityData[selectedRoomId].available) {
            const r = availabilityData[selectedRoomId];
            if (window.bookingWidget && window.bookingWidget.updatePrice) {
                window.bookingWidget.updatePrice(r.price_per_night, r.room_name, selectedRoomId);
            }
        }
    }

    function checkAvailability() {
        const checkin  = getCheckin();
        const checkout = getCheckout();
        if (!checkin || !checkout) return;

        // Set all rooms to loading state
        document.querySelectorAll('.room-card').forEach(function (card) {
            setRoomCardState(card.dataset.roomId, 'loading');
        });

        fetch(AVAILABILITY_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                checkin:  checkin,
                checkout: checkout,
                adults:   getAdults(),
                children: getChildren(),
                rooms:    getRooms(),
                guests:   getGuests(),
            }),
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            if (json.success && Array.isArray(json.data)) {
                applyAvailabilityResults(json.data);
            }
        })
        .catch(function () {
            // On network error, reset all rooms to default state
            document.querySelectorAll('.room-card').forEach(function (card) {
                setRoomCardState(card.dataset.roomId, 'default');
            });
        });
    }

    // ── Room Select ───────────────────────────────────────────────

    document.querySelectorAll('.room-select-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card     = this.closest('.room-card');
            const roomId   = card.dataset.roomId;
            const roomName = card.dataset.roomName || '';
            const price    = parseFloat(card.dataset.price) || 0;

            // Use availability data price if we have it
            const avail = availabilityData[roomId];
            const finalPrice = (avail && avail.available) ? avail.price_per_night : price;

            // Deselect all
            document.querySelectorAll('.room-card').forEach(function (c) {
                c.classList.remove('room-card--selected');
                const b = c.querySelector('.room-select-btn');
                if (b && !b.disabled) b.textContent = 'Select';
            });

            // Select this card
            card.classList.add('room-card--selected');
            this.textContent = 'Selected';
            selectedRoomId   = roomId;

            // Update booking widget
            if (window.bookingWidget && window.bookingWidget.updatePrice) {
                window.bookingWidget.updatePrice(finalPrice, roomName, roomId);
            }

            // Smooth scroll to widget on mobile
            if (window.innerWidth < 992) {
                const widget = document.getElementById('bookingWidget');
                if (widget) widget.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── Date Change Triggers ──────────────────────────────────────

    document.getElementById('checkinDate').addEventListener('change', checkAvailability);
    document.getElementById('checkoutDate').addEventListener('change', checkAvailability);
    document.getElementById('adultsInput')?.addEventListener('change', checkAvailability);
    document.getElementById('childrenInput')?.addEventListener('change', checkAvailability);
    document.getElementById('roomsInput')?.addEventListener('change', checkAvailability);

    // Run on load if dates are pre-filled from search query params
    if (getCheckin() && getCheckout()) checkAvailability();
})();
</script>
@else
<script>
(function () {
    const TAX_RATE = 0.04;
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const B2B_META = @json($b2bMeta);
    const fallbackPrice = Number({{ (float) ($search['price'] ?? 0) }}) || 0;

    const roomsContainer = document.getElementById('b2bRoomsContainer');
    const reserveBtn = document.getElementById('b2bReserveBtn');
    const form = document.getElementById('bookingForm');
    const checkinEl = document.getElementById('checkinDate');
    const checkoutEl = document.getElementById('checkoutDate');
    const guestsPicker = document.getElementById('detailGuestsPicker');
    const guestsTrigger = guestsPicker ? guestsPicker.querySelector('.guests-trigger') : null;
    const guestsMenu = document.getElementById('detailGuestsMenu');
    const guestsSummaryDisplay = document.getElementById('guestsSummaryDisplay');
    const adultsInput = document.getElementById('adultsInput');
    const childrenInput = document.getElementById('childrenInput');
    const roomsInput = document.getElementById('roomsInput');
    const guestsTotalInput = document.getElementById('guestsTotalInput');
    const adultsCounterVal = document.getElementById('adultsCounterVal');
    const childrenCounterVal = document.getElementById('childrenCounterVal');
    const roomsCounterVal = document.getElementById('roomsCounterVal');

    if (!roomsContainer || !form || !checkinEl || !checkoutEl || !adultsInput || !childrenInput || !roomsInput) return;

    const selectedRoomIdEl = document.getElementById('selectedRoomId');
    const selectedAgreementCodeEl = document.getElementById('selectedAgreementCode');
    const selectedRoomTypeCodeEl = document.getElementById('selectedRoomTypeCode');
    const selectedRoomTypeNameEl = document.getElementById('selectedRoomTypeName');
    const selectedMealBasisCodeEl = document.getElementById('selectedMealBasisCode');
    const selectedMealBasisNameEl = document.getElementById('selectedMealBasisName');
    const selectedSearchNumberEl = document.getElementById('selectedSearchNumber');
    const selectedTokenIdEl = document.getElementById('selectedTokenId');
    const selectedRateKeyEl = document.getElementById('selectedRateKey');
    const selectedAgreementPriceEl = document.getElementById('selectedAgreementPrice');
    const selectedCurrencyEl = document.getElementById('selectedCurrency');

    const widgetNoRoom = document.getElementById('widgetNoRoom');
    const widgetPriceSection = document.getElementById('widgetPriceSection');
    const widgetPriceAmount = document.getElementById('widgetPriceAmount');
    const widgetRoomName = document.getElementById('widgetRoomName');
    const breakdown = document.getElementById('priceBreakdown');
    const nightsPriceLabel = document.getElementById('nightsPriceLabel');
    const nightsCount = document.getElementById('nightsCount');
    const subtotalAmount = document.getElementById('subtotalAmount');
    const taxAmount = document.getElementById('taxAmount');
    const totalAmount = document.getElementById('totalAmount');
    let currentPricePerNight = 0;

    const esc = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const asNumber = (value, fallback = 0) => {
        const num = Number(value);
        return Number.isFinite(num) ? num : fallback;
    };

    const setReserveLoading = (loading) => {
        if (!reserveBtn) return;
        reserveBtn.disabled = loading;
        reserveBtn.innerHTML = loading
            ? '<i class="bi bi-hourglass-split me-2"></i>Please wait...'
            : '<i class="bi bi-credit-card me-2"></i>Reserve Now';
    };

    // Force visible initial state in B2B widget.
    setReserveLoading(false);

    const calcNights = (start, end) => {
        if (!start || !end) return 0;
        const diff = new Date(end) - new Date(start);
        return diff > 0 ? Math.round(diff / 86400000) : 0;
    };

    const getAdults = () => Math.max(1, asNumber(adultsInput.value, 1));
    const getChildren = () => Math.max(0, asNumber(childrenInput.value, 0));
    const getUnits = () => Math.max(1, asNumber(roomsInput.value, 1));
    const getTotalGuests = () => getAdults() + getChildren();
    const getOccupancyPerRoom = () => Math.max(1, Math.ceil(getTotalGuests() / getUnits()));

    const syncGuestSummary = () => {
        const adults = getAdults();
        const children = getChildren();
        const rooms = getUnits();
        if (adultsCounterVal) adultsCounterVal.textContent = String(adults);
        if (childrenCounterVal) childrenCounterVal.textContent = String(children);
        if (roomsCounterVal) roomsCounterVal.textContent = String(rooms);
        if (guestsSummaryDisplay) guestsSummaryDisplay.value = `${rooms} Unit - ${adults} Adult - ${children} Children`;
        if (guestsTotalInput) guestsTotalInput.value = String(adults + children);
    };

    const setupGuestsPicker = () => {
        if (!guestsPicker || !guestsTrigger || !guestsMenu) return;
        const closeGuestsPicker = () => {
            guestsPicker.classList.remove('is-open');
            guestsTrigger.setAttribute('aria-expanded', 'false');
        };
        const openGuestsPicker = () => {
            guestsPicker.classList.add('is-open');
            guestsTrigger.setAttribute('aria-expanded', 'true');
        };

        guestsTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            if (guestsPicker.classList.contains('is-open')) closeGuestsPicker();
            else openGuestsPicker();
        });
        guestsTrigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                guestsTrigger.click();
            }
        });

        guestsMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            const btn = e.target.closest('.counter-btn');
            if (!btn) return;
            const field = btn.dataset.counter;
            const delta = asNumber(btn.dataset.delta, 0);
            if (!field || !delta) return;

            const mins = { adults: 1, children: 0, rooms: 1 };
            const maxs = { adults: 9, children: 9, rooms: 9 };
            const targetInput = field === 'adults' ? adultsInput : (field === 'children' ? childrenInput : roomsInput);
            if (!targetInput) return;

            const current = asNumber(targetInput.value, mins[field] ?? 0);
            const next = Math.min(maxs[field] ?? 9, Math.max(mins[field] ?? 0, current + delta));
            targetInput.value = String(next);
            syncGuestSummary();
            updateBreakdown();
        });

        document.addEventListener('click', (e) => {
            if (!guestsPicker.contains(e.target)) closeGuestsPicker();
        });
    };

    const updateBreakdown = () => {
        if (!breakdown) return;
        const nights = calcNights(checkinEl.value, checkoutEl.value);
        if (!selectedRoomIdEl.value || currentPricePerNight <= 0 || nights <= 0) {
            breakdown.style.display = 'none';
            return;
        }

        const subtotal = currentPricePerNight * nights;
        const tax = Math.round(subtotal * TAX_RATE);
        const total = subtotal + tax;

        if (nightsPriceLabel) nightsPriceLabel.textContent = `$${Math.round(currentPricePerNight).toLocaleString()}`;
        if (nightsCount) nightsCount.textContent = nights;
        if (subtotalAmount) subtotalAmount.textContent = `$${Math.round(subtotal).toLocaleString()}`;
        if (taxAmount) taxAmount.textContent = `$${Math.round(tax).toLocaleString()}`;
        if (totalAmount) totalAmount.textContent = `$${Math.round(total).toLocaleString()}`;
        breakdown.style.display = 'block';
    };

    const resetRoomSelectionUI = () => {
        selectedRoomIdEl.value = '';
        selectedAgreementCodeEl.value = '';
        selectedRoomTypeCodeEl.value = '';
        selectedRoomTypeNameEl.value = '';
        selectedMealBasisCodeEl.value = '';
        selectedMealBasisNameEl.value = '';
        selectedSearchNumberEl.value = '';
        selectedTokenIdEl.value = '';
        selectedRateKeyEl.value = '';
        selectedAgreementPriceEl.value = '';
        selectedCurrencyEl.value = '';
        currentPricePerNight = 0;
        if (widgetNoRoom) widgetNoRoom.style.display = 'block';
        if (widgetPriceSection) widgetPriceSection.style.display = 'none';
        if (breakdown) breakdown.style.display = 'none';
    };

    const bindRoomSelection = () => {
        roomsContainer.querySelectorAll('.room-select-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const card = this.closest('.room-card');
                if (!card) return;

                roomsContainer.querySelectorAll('.room-card').forEach((c) => {
                    c.classList.remove('room-card--selected');
                    const b = c.querySelector('.room-select-btn');
                    if (b) b.textContent = 'Select';
                });

                card.classList.add('room-card--selected');
                this.textContent = 'Selected';

                selectedRoomIdEl.value = card.dataset.roomId || '';
                selectedAgreementCodeEl.value = card.dataset.agreementCode || '';
                selectedRoomTypeCodeEl.value = card.dataset.roomTypeCode || '';
                selectedRoomTypeNameEl.value = card.dataset.roomName || '';
                selectedMealBasisCodeEl.value = card.dataset.mealBasisCode || '';
                selectedMealBasisNameEl.value = card.dataset.mealBasisName || '';
                selectedSearchNumberEl.value = card.dataset.searchNumber || '';
                selectedTokenIdEl.value = card.dataset.tokenId || '';
                selectedRateKeyEl.value = card.dataset.rateKey || '';
                selectedAgreementPriceEl.value = card.dataset.agreementPrice || '';
                selectedCurrencyEl.value = card.dataset.currency || '';
                currentPricePerNight = asNumber(card.dataset.price, 0);

                if (widgetPriceAmount) widgetPriceAmount.textContent = `$${Math.round(currentPricePerNight).toLocaleString()}`;
                if (widgetRoomName) widgetRoomName.textContent = card.dataset.roomName || '';
                if (widgetNoRoom) widgetNoRoom.style.display = 'none';
                if (widgetPriceSection) widgetPriceSection.style.display = 'block';
                updateBreakdown();
            });
        });
    };

    const renderRooms = (rooms) => {
        if (!rooms.length) {
            roomsContainer.innerHTML = '<div class="text-muted small">No rooms available for selected dates.</div>';
            resetRoomSelectionUI();
            return;
        }

        roomsContainer.innerHTML = rooms.map((room, index) => {
            const roomId = esc(room.room_id || room.id || `${index + 1}`);
            const roomName = esc(room.room_type_name || room.room_name || room.name || 'Room');
            const roomCode = esc(room.room_type_code || room.room_code || 'dbl');
            const mealCode = esc(room.meal_basis_code || room.meal_code || '');
            const mealName = esc(room.meal_basis_name || room.meal_name || 'Room Only');
            const agreementCode = esc(room.agreement_code || '');
            const searchNumber = esc(room.search_number || '');
            const tokenId = esc(room.token_id || '');
            const rateKey = esc(room.rate_key || '');
            const currency = esc(room.currency || 'USD');
            const price = asNumber(room.total_price ?? room.price_per_night ?? room.price ?? room.agreement_price, fallbackPrice);
            const agreementPrice = asNumber(room.agreement_price, price);

            return `
                <div class="room-card"
                     data-room-id="${roomId}"
                     data-price="${price}"
                     data-room-name="${roomName}"
                     data-room-type-code="${roomCode}"
                     data-meal-basis-code="${mealCode}"
                     data-meal-basis-name="${mealName}"
                     data-agreement-code="${agreementCode}"
                     data-search-number="${searchNumber}"
                     data-token-id="${tokenId}"
                     data-rate-key="${rateKey}"
                     data-agreement-price="${agreementPrice}"
                     data-currency="${currency}">
                    <div class="room-card__info">
                        <div class="room-card__meta">
                            <h6 class="room-card__name">${roomName}</h6>
                            <div class="room-card__specs">${mealName}</div>
                        </div>
                    </div>
                    <div class="room-card__action">
                        <div class="room-card__price">
                            <span class="room-card__price-label">Per night</span>
                            <span class="room-card__price-amount">$${Math.round(price).toLocaleString()}</span>
                        </div>
                        <button type="button" class="btn btn-select room-select-btn">Select</button>
                    </div>
                </div>
            `;
        }).join('');

        bindRoomSelection();
    };

    const fetchRooms = async () => {
        const checkIn = checkinEl.value;
        const checkOut = checkoutEl.value;
        if (!checkIn || !checkOut) return;

        roomsContainer.innerHTML = '<div class="text-muted small">Checking availability...</div>';
        resetRoomSelectionUI();

        try {
            const response = await fetch(B2B_META.rooms_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    hotel_code: B2B_META.hotel_code,
                    city_code: B2B_META.city_code || '',
                    check_in: checkIn,
                    check_out: checkOut,
                }),
            });
            const payload = await response.json();
            const rooms = Array.isArray(payload.rooms) ? payload.rooms : [];
            renderRooms(rooms);
        } catch {
            roomsContainer.innerHTML = '<div class="text-danger small">Unable to load rooms. Please try again.</div>';
        }
    };

    checkinEl.addEventListener('change', () => {
        if (checkoutEl.value && checkoutEl.value <= checkinEl.value) {
            const next = new Date(checkinEl.value);
            next.setDate(next.getDate() + 1);
            checkoutEl.value = next.toISOString().split('T')[0];
        }
        checkoutEl.min = checkinEl.value;
        updateBreakdown();
        fetchRooms();
    });
    checkoutEl.addEventListener('change', () => {
        updateBreakdown();
        fetchRooms();
    });
    setupGuestsPicker();
    syncGuestSummary();

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!selectedRoomIdEl.value) {
            alert('Please select a room first.');
            document.getElementById('availableRoomsSection')?.scrollIntoView({ behavior: 'smooth' });
            return;
        }

        if (!checkinEl.value || !checkoutEl.value) {
            alert('Please select check-in and check-out dates.');
            return;
        }

        const nights = calcNights(checkinEl.value, checkoutEl.value);
        const totalPrice = currentPricePerNight > 0 && nights > 0
            ? currentPricePerNight * nights
            : fallbackPrice;

        setReserveLoading(true);
        const params = new URLSearchParams({
            hotel_code: String(B2B_META.hotel_code || ''),
            hotel_name: String(B2B_META.hotel_name || ''),
            address: String(B2B_META.address || ''),
            hotel_image_url: String(B2B_META.hotel_image_url || ''),
            city_code: String(B2B_META.city_code || ''),
            star_rate: String(B2B_META.star_rate || 0),
            checkin: checkinEl.value,
            checkout: checkoutEl.value,
            guests: String(getTotalGuests()),
            adults: String(getAdults()),
            children: String(getChildren()),
            rooms: String(getUnits()),
            occupancy: String(getOccupancyPerRoom()),
            nationality: String(B2B_META.nationality || 'AE'),
            room_id: String(selectedRoomIdEl.value || ''),
            agreement_code: String(selectedAgreementCodeEl.value || ''),
            room_type_code: String(selectedRoomTypeCodeEl.value || ''),
            room_type_name: String(selectedRoomTypeNameEl.value || ''),
            meal_basis_code: String(selectedMealBasisCodeEl.value || ''),
            meal_basis_name: String(selectedMealBasisNameEl.value || ''),
            search_number: String(selectedSearchNumberEl.value || ''),
            token_id: String(selectedTokenIdEl.value || ''),
            rate_key: String(selectedRateKeyEl.value || ''),
            agreement_price: String(selectedAgreementPriceEl.value || ''),
            currency: String(selectedCurrencyEl.value || 'USD'),
            price_per_night: String(currentPricePerNight || fallbackPrice),
            total_price: String(totalPrice),
        });

        window.location.href = `${B2B_META.checkout_form_url}?${params.toString()}`;
    });

    if (checkinEl.value && checkoutEl.value) {
        fetchRooms();
    }
})();
</script>
@endif
@endpush
