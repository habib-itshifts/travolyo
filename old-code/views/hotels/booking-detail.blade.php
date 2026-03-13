@extends('frontend.layouts.app')

@section('title', 'Hotel Booking Confirmation')

@push('styles')
<style>
.hotel-booking-hero {
    background: linear-gradient(135deg, var(--color-primary, #17c3ce) 0%, #0e9aa7 100%);
    color: #fff;
    padding: 2rem 0 2.75rem;
    margin-bottom: 1.5rem;
}
.hotel-booking-hero__title { font-size: 1.55rem; font-weight: 700; margin-bottom: 0.35rem; }
.hotel-booking-hero__sub { opacity: 0.9; font-size: 0.92rem; margin: 0; }

.hb-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.hb-card__head {
    border-bottom: 1px solid #f0f0f0;
    padding: 1rem 1.2rem;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}
.hb-card__body { padding: 1.2rem; }
.hb-card__foot {
    border-top: 1px solid #f0f0f0;
    padding: 0.95rem 1.2rem 1.05rem;
}

.hb-status {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border-radius: 999px;
    padding: 0.3rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 600;
}
.hb-status--ok { background: #ecfdf3; color: #15803d; border: 1px solid #86efac; }
.hb-status--warn { background: #fff7ed; color: #b45309; border: 1px solid #fdba74; }

.hb-list-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    font-size: 0.9rem;
    padding: 0.45rem 0;
    border-bottom: 1px dashed #f1f5f9;
}
.hb-list-row:last-child { border-bottom: 0; }
.hb-list-row__label { color: #6b7280; }
.hb-list-row__value { color: #111827; font-weight: 600; text-align: right; }

.hb-total {
    border-top: 1px solid #e5e7eb;
    margin-top: 0.6rem;
    padding-top: 0.75rem;
    display: flex;
    justify-content: space-between;
    font-size: 1.08rem;
    font-weight: 700;
    color: #111827;
}

.hb-actions {
    display: flex;
    gap: 0.6rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}
.hb-btn {
    border: 1px solid var(--color-primary, #17c3ce);
    background: var(--color-primary, #17c3ce);
    color: #fff;
    padding: 0.65rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
}
.hb-btn--ghost {
    background: #fff;
    color: var(--color-primary, #17c3ce);
}

.hb-activity-grid {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 0.8rem;
}
@media (min-width: 768px) {
    .hb-activity-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (min-width: 1200px) {
    .hb-activity-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}
.hb-activity-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
}
.hb-activity-card__img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    background: #f3f4f6;
}
.hb-activity-card__body { padding: 0.7rem; }
.hb-activity-card__title {
    margin: 0;
    font-size: 0.86rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
}
.hb-activity-card__meta {
    margin: 0.25rem 0 0;
    font-size: 0.75rem;
    color: #6b7280;
}
.hb-activity-card__price {
    margin-top: 0.4rem;
    font-size: 0.82rem;
    font-weight: 700;
    color: #111827;
}
.hb-activity-card__btn {
    margin-top: 0.45rem;
    display: inline-block;
    border: 1px solid var(--color-primary, #17c3ce);
    color: var(--color-primary, #17c3ce);
    border-radius: 8px;
    padding: 0.28rem 0.55rem;
    font-size: 0.73rem;
    font-weight: 600;
    text-decoration: none;
}
.hb-side-activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}
.hb-side-activity-item {
    display: grid;
    grid-template-columns: 84px 1fr;
    gap: 0.55rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.35rem;
    background: #fff;
    min-height: 82px;
}
.hb-side-activity-item__img {
    width: 84px;
    height: 74px;
    border-radius: 8px;
    object-fit: cover;
    background: #f3f4f6;
}
.hb-side-activity-item__title {
    margin: 0;
    font-size: 0.78rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.25;
}
.hb-side-activity-item__meta {
    margin: 0.2rem 0 0;
    font-size: 0.68rem;
    color: #6b7280;
}
.hb-side-activity-item__price {
    margin-top: 0.2rem;
    font-size: 0.72rem;
    font-weight: 700;
    color: #111827;
}
.hb-side-activity-item__btn {
    margin-top: 0.28rem;
    display: inline-block;
    border: 1px solid var(--color-primary, #17c3ce);
    color: var(--color-primary, #17c3ce);
    border-radius: 7px;
    padding: 0.2rem 0.48rem;
    font-size: 0.66rem;
    font-weight: 600;
    text-decoration: none;
    margin-left: auto;
}
.hb-side-activity-item__content {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.hb-side-view-more {
    margin-top: 0.7rem;
    display: flex;
    justify-content: flex-end;
}
.hb-side-view-more .hb-btn {
    padding: 0.4rem 0.78rem;
    font-size: 0.72rem;
    border-radius: 8px;
}
@media (min-width: 992px) {
    .hb-layout-row { align-items: stretch; }
    .hb-col-left,
    .hb-col-right {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .hb-col-left .hb-card.hb-booking-main {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .hb-col-left .hb-card.hb-booking-main .hb-card__body {
        flex: 1;
    }
    .hb-col-right { gap: 1rem; }
    .hb-col-right .hb-card { margin: 0; }
}
</style>
@endpush

@section('content')
@php
    $resolvedPaymentStatus = trim((string) ($bookingData['payment_status'] ?? ''));
    if ($resolvedPaymentStatus === '') {
        $bookingStatus = strtolower((string) ($bookingData['status'] ?? ''));
        $resolvedPaymentStatus = in_array($bookingStatus, ['paid', 'completed', 'confirmed'], true)
            ? 'paid'
            : ($bookingStatus !== '' ? $bookingStatus : '-');
    }
@endphp
<div class="hotel-booking-hero">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <div class="hotel-booking-hero__title">
                <i class="bi bi-check2-circle me-2"></i>Booking Confirmed
            </div>
            <p class="hotel-booking-hero__sub">Your hotel reservation is completed successfully.</p>
        </div>
        <a href="{{ route('frontend.hotels.index') }}" class="text-white text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to Hotels
        </a>
    </div>
</div>

<div class="container pb-4">
    <div class="row g-4 hb-layout-row">
        <div class="col-lg-8 hb-col-left">
            <div class="hb-card mb-3 hb-booking-main">
                <div class="hb-card__head d-flex align-items-center justify-content-between">
                    <span>Booking Overview</span>
                    <span class="hb-status {{ in_array(strtolower($bookingData['status']), ['paid','completed','confirmed']) ? 'hb-status--ok' : 'hb-status--warn' }}">
                        <i class="bi bi-circle-fill" style="font-size:8px;"></i>
                        {{ strtoupper($bookingData['status']) }}
                    </span>
                </div>
                <div class="hb-card__body">
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Booking Code</div>
                        <div class="hb-list-row__value">{{ $bookingData['code'] }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Hotel</div>
                        <div class="hb-list-row__value">{{ $bookingData['hotel_name'] }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Address</div>
                        <div class="hb-list-row__value">{{ $bookingData['hotel_address'] ?: '-' }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Room</div>
                        <div class="hb-list-row__value">{{ $bookingData['room_type'] }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Meal Plan</div>
                        <div class="hb-list-row__value">{{ $bookingData['meal_basis'] ?: '-' }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Check-in</div>
                        <div class="hb-list-row__value">{{ $bookingData['check_in'] ? $bookingData['check_in']->format('Y-m-d') : '-' }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Check-out</div>
                        <div class="hb-list-row__value">{{ $bookingData['check_out'] ? $bookingData['check_out']->format('Y-m-d') : '-' }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Guests</div>
                        <div class="hb-list-row__value">{{ $bookingData['adults'] }} Adult{{ $bookingData['adults'] > 1 ? 's' : '' }}{{ $bookingData['children'] > 0 ? ', '.$bookingData['children'].' Child' : '' }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Rooms / Nights</div>
                        <div class="hb-list-row__value">{{ $bookingData['rooms'] }} Room, {{ $bookingData['nights'] }} Night{{ $bookingData['nights'] > 1 ? 's' : '' }}</div>
                    </div>
                </div>
                <div class="hb-card__foot">
                    <div class="hb-actions">
                        <a class="hb-btn" href="{{ route('frontend.hotels.index') }}">
                            <i class="bi bi-search me-1"></i>Book Another Hotel
                        </a>
                        <a class="hb-btn hb-btn--ghost" href="{{ url('/booking/'.$bookingData['code']) }}">
                            <i class="bi bi-receipt me-1"></i>Open Invoice View
                        </a>
                    </div>
                </div>
            </div>

            @if($bookingData['is_b2b'])
            <div class="hb-card mb-3">
                <div class="hb-card__head">Supplier Confirmation</div>
                <div class="hb-card__body">
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Supplier Status</div>
                        <div class="hb-list-row__value">{{ $bookingData['supplier_status'] ?: '-' }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Supplier Reference</div>
                        <div class="hb-list-row__value">{{ $bookingData['supplier_reference'] ?: '-' }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Supplier Booking Code</div>
                        <div class="hb-list-row__value">{{ $bookingData['supplier_booking_code'] ?: '-' }}</div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <div class="col-lg-4 hb-col-right">
            <div class="hb-card">
                <div class="hb-card__head">Payment Summary</div>
                <div class="hb-card__body">
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Payment Method</div>
                        <div class="hb-list-row__value">{{ strtoupper($bookingData['gateway']) }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Payment Status</div>
                        <div class="hb-list-row__value">{{ strtoupper($resolvedPaymentStatus) }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">${{ number_format($bookingData['price_per_night'], 2) }} x {{ $bookingData['nights'] }} night{{ $bookingData['nights'] > 1 ? 's' : '' }}</div>
                        <div class="hb-list-row__value">${{ number_format($bookingData['subtotal'], 2) }}</div>
                    </div>
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">Taxes &amp; fees</div>
                        <div class="hb-list-row__value">${{ number_format($bookingData['taxes'], 2) }}</div>
                    </div>
                    @foreach($bookingData['extra_price_items'] ?? [] as $extraItem)
                    <div class="hb-list-row">
                        <div class="hb-list-row__label">{{ $extraItem['name'] ?? 'Add-on' }}</div>
                        <div class="hb-list-row__value">${{ number_format($extraItem['amount'] ?? 0, 2) }}</div>
                    </div>
                    @endforeach
                    <div class="hb-total">
                        <span>Total</span>
                        <span>${{ number_format($bookingData['total'], 2) }}</span>
                    </div>
                </div>
            </div>

            @if(($canShowActivities ?? false) && $nearbyActivities->count() > 0)
            <div class="hb-card">
                <div class="hb-card__head d-flex align-items-center justify-content-between">
                    <span>Top 3 Activities</span>
                    <small class="text-muted">{{ $activityCity }}</small>
                </div>
                <div class="hb-card__body">
                    <div class="hb-side-activity-list">
                        @foreach($nearbyActivities as $activity)
                            @php
                                $activityImage = '';
                                if (!empty($activity->image_id)) {
                                    $activityImage = get_file_url($activity->image_id, 'medium');
                                }
                                if (empty($activityImage) && !empty($activity->gallery)) {
                                    $gallery = $activity->gallery;
                                    if (is_string($gallery)) {
                                        $decoded = json_decode($gallery, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                            $gallery = $decoded;
                                        } else {
                                            $gallery = array_filter(array_map('trim', explode(',', $gallery)));
                                        }
                                    }
                                    if (is_array($gallery) && !empty($gallery)) {
                                        $firstGalleryId = (int) reset($gallery);
                                        if ($firstGalleryId > 0) {
                                            $activityImage = get_file_url($firstGalleryId, 'medium');
                                        }
                                    }
                                }
                                if (empty($activityImage)) {
                                    $activityImage = asset('assets/logo/travolyo-logo.svg');
                                }
                                $activityDate = $bookingData['check_in'] ? $bookingData['check_in']->format('Y-m-d') : now()->format('Y-m-d');
                            @endphp
                            <div class="hb-side-activity-item">
                                <img src="{{ $activityImage }}" alt="{{ $activity->title }}" class="hb-side-activity-item__img" loading="lazy">
                                <div class="hb-side-activity-item__content">
                                    <p class="hb-side-activity-item__title">{{ $activity->title }}</p>
                                    <p class="hb-side-activity-item__meta">{{ $activity->city ?: '-' }}</p>
                                    <div class="hb-side-activity-item__price">{{ $activity->currency ?: 'AED' }} {{ number_format((float) ($activity->price_per_person ?: 0), 2) }}</div>
                                    <a href="{{ route('frontend.activities.checkout', ['id' => $activity->id, 'city' => $activityCity, 'date' => $activityDate]) }}" class="hb-side-activity-item__btn">View Deal</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="hb-side-view-more">
                        <a href="{{ $viewMoreActivitiesUrl }}" class="hb-btn hb-btn--ghost">
                            <i class="bi bi-grid-3x3-gap me-1"></i>View More Activities
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function syncBookingOverviewHeight() {
        var desktop = window.matchMedia('(min-width: 992px)').matches;
        var leftCard = document.querySelector('.hb-booking-main');
        var rightCol = document.querySelector('.hb-col-right');
        if (!leftCard || !rightCol) return;

        // Reset before recalculation.
        leftCard.style.minHeight = '';

        if (!desktop) return;

        var rightHeight = rightCol.getBoundingClientRect().height;
        if (rightHeight > 0) {
            leftCard.style.minHeight = Math.ceil(rightHeight) + 'px';
        }
    }

    window.addEventListener('load', syncBookingOverviewHeight);
    window.addEventListener('resize', syncBookingOverviewHeight);
})();
</script>
@endpush
