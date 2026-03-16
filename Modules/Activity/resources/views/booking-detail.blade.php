@extends('layouts.master')

@section('title', 'Activity Booking - ' . $booking->code)

@push('styles')
<style>
    .activity-booking-shell {
        background: #f8fafc;
        padding: 0 0 4rem;
    }
    .hotel-booking-hero {
        background: linear-gradient(135deg, var(--color-primary, #17c3ce) 0%, #0e9aa7 100%);
        color: #fff;
        padding: 2rem 0 2.75rem;
        margin-bottom: 1.5rem;
    }
    .hotel-booking-hero__title {
        font-size: 1.55rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .hotel-booking-hero__sub {
        opacity: 0.92;
        font-size: 0.92rem;
        margin: 0;
    }
    .hb-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .hb-card__head {
        border-bottom: 1px solid #f0f0f0;
        padding: 1rem 1.2rem;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
    }
    .hb-card__body {
        padding: 1.2rem;
    }
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
    .hb-status--ok {
        background: #ecfdf3;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .hb-status--warn {
        background: #fff7ed;
        color: #b45309;
        border: 1px solid #fdba74;
    }
    .hb-status--danger {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }
    .hb-list-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.9rem;
        padding: 0.55rem 0;
        border-bottom: 1px dashed #f1f5f9;
    }
    .hb-list-row:last-child {
        border-bottom: 0;
    }
    .hb-list-row__label {
        color: #6b7280;
    }
    .hb-list-row__value {
        color: #111827;
        font-weight: 600;
        text-align: right;
    }
    .hb-total {
        border-top: 1px solid #e5e7eb;
        margin-top: 0.6rem;
        padding-top: 0.75rem;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }
    .hb-btn:hover {
        color: #fff;
        background: var(--color-primary-dark, #13a9b3);
        border-color: var(--color-primary-dark, #13a9b3);
    }
    .hb-btn--ghost {
        background: #fff;
        color: var(--color-primary, #17c3ce);
    }
    .hb-btn--ghost:hover {
        color: var(--color-primary, #17c3ce);
        background: #f0fdfe;
        border-color: var(--color-primary, #17c3ce);
    }
    .ab-overview {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }
    .ab-overview__image {
        width: 140px;
        height: 120px;
        border-radius: 12px;
        object-fit: cover;
        background: #f3f4f6;
    }
    .ab-overview__placeholder {
        width: 140px;
        height: 120px;
        border-radius: 12px;
        background: linear-gradient(135deg, #cffafe 0%, #99f6e4 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f766e;
        font-size: 2rem;
    }
    .ab-overview__eyebrow {
        font-size: 0.72rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #0f766e;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .ab-overview__title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 700;
        color: #111827;
    }
    .ab-overview__meta {
        margin-top: 0.45rem;
        color: #6b7280;
        font-size: 0.9rem;
    }
    .ab-flash {
        border-radius: 12px;
        padding: 0.9rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.92rem;
        border: 1px solid transparent;
    }
    .ab-flash--success {
        background: #ecfdf3;
        border-color: #86efac;
        color: #166534;
    }
    .ab-flash--error {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #991b1b;
    }
    .ab-flash--info {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }
    @media (max-width: 991.98px) {
        .hb-col-right {
            margin-top: 1rem;
        }
    }
    @media (max-width: 767.98px) {
        .ab-overview {
            grid-template-columns: 1fr;
        }
        .ab-overview__image,
        .ab-overview__placeholder {
            width: 100%;
            height: 190px;
        }
        .hb-list-row {
            flex-direction: column;
            gap: 0.25rem;
        }
        .hb-list-row__value {
            text-align: left;
        }
    }
</style>
@endpush

@section('content')
@php
    $passengers = is_array($passengers ?? null) ? array_values($passengers) : [];
    $activityTitle = $booking->getMeta('activity_title', 'Activity Experience');
    $activityCity = $booking->getMeta('activity_city', '-');
    $activityCountry = $booking->getMeta('activity_country', '-');
    $activityDate = $booking->getMeta('activity_date', '-');
    $activityDuration = $booking->getMeta('activity_duration', '-');
    $activityCategory = $booking->getMeta('activity_category', 'Activity');
    $participants = (int) $booking->getMeta('activity_participants', 1);
    $currency = strtoupper((string) $booking->getMeta('activity_currency', $booking->currency));
    $unitPrice = (float) $booking->getMeta('activity_unit_price', 0);
    $specialRequests = $booking->getMeta('activity_special_requests', $booking->customer_notes ?: '');
    $paymentGateway = strtoupper((string) ($booking->getMeta('payment_gateway') ?: data_get($passengers, '0.payment_gateway') ?: $passenger?->payment_gateway ?: '-'));
    $status = strtolower((string) $booking->status);
    $isPaid = in_array($status, ['paid', 'confirmed', 'completed'], true);
    $isPending = in_array($status, ['draft', 'unpaid'], true);
    $statusClass = $isPaid ? 'hb-status--ok' : ($isPending ? 'hb-status--warn' : 'hb-status--danger');
    $statusText = strtoupper(str_replace('_', ' ', (string) $booking->status));
    $heroTitle = $isPaid ? 'Booking Confirmed' : ($isPending ? 'Booking Pending' : 'Booking Update Required');
    $heroSub = $isPaid
        ? 'Your activity reservation is completed successfully.'
        : ($isPending ? 'Your activity booking is waiting for payment confirmation.' : 'Your payment did not complete successfully for this booking.');
@endphp

<div class="activity-booking-shell">
    <div class="hotel-booking-hero">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="hotel-booking-hero__title">
                    <i class="bi bi-check2-circle me-2"></i>{{ $heroTitle }}
                </div>
                <p class="hotel-booking-hero__sub">{{ $heroSub }}</p>
            </div>
            <a href="{{ route('activities.index') }}" class="text-white text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Back to Activities
            </a>
        </div>
    </div>

    <div class="container pb-4">
        @if (session('success'))
            <div class="ab-flash ab-flash--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="ab-flash ab-flash--error">{{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="ab-flash ab-flash--info">{{ session('info') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8 hb-col-left">
                <div class="hb-card mb-3">
                    <div class="hb-card__head d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>Booking Overview</span>
                        <span class="hb-status {{ $statusClass }}">
                            <i class="bi bi-circle-fill" style="font-size:8px;"></i>
                            {{ $statusText }}
                        </span>
                    </div>
                    <div class="hb-card__body">
                        <div class="ab-overview">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $activityTitle }}" class="ab-overview__image">
                            @else
                                <div class="ab-overview__placeholder">
                                    <i class="bi bi-map"></i>
                                </div>
                            @endif
                            <div>
                                <div class="ab-overview__eyebrow">{{ $activityCategory ?: 'Activity' }}</div>
                                <h2 class="ab-overview__title">{{ $activityTitle }}</h2>
                                <div class="ab-overview__meta">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $activityCity ?: '-' }}{{ $activityCountry && $activityCountry !== '-' ? ', ' . $activityCountry : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Booking Code</div>
                            <div class="hb-list-row__value">{{ $booking->code }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Activity Date</div>
                            <div class="hb-list-row__value">{{ $activityDate ?: '-' }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Participants</div>
                            <div class="hb-list-row__value">{{ $participants }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Duration</div>
                            <div class="hb-list-row__value">{{ $activityDuration ?: '-' }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Contact Name</div>
                            <div class="hb-list-row__value">{{ trim($booking->first_name . ' ' . $booking->last_name) ?: '-' }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Contact Email</div>
                            <div class="hb-list-row__value">{{ $booking->email ?: '-' }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Phone</div>
                            <div class="hb-list-row__value">{{ $booking->phone ?: '-' }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Special Requests</div>
                            <div class="hb-list-row__value">{{ $specialRequests ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="hb-card__foot">
                        <div class="hb-actions">
                            <a class="hb-btn hb-btn--ghost" href="{{ route('activities.index') }}">
                                <i class="bi bi-grid me-1"></i>Browse Activities
                            </a>
                            @if($booking->getMeta('activity_slug'))
                                <a class="hb-btn" href="{{ route('activities.show', ['activity' => $booking->getMeta('activity_slug'), 'activity_date' => $activityDate]) }}">
                                    <i class="bi bi-compass me-1"></i>View Activity
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="hb-card">
                    <div class="hb-card__head">Traveler Details</div>
                    <div class="hb-card__body">
                        @if(!empty($passengers))
                            @foreach($passengers as $index => $traveler)
                                <div class="fw-bold text-dark {{ $index > 0 ? 'mt-4' : '' }} mb-2">
                                    {{ $index === 0 ? 'Lead Passenger' : 'Passenger ' . ($index + 1) }}
                                </div>
                                <div class="hb-list-row">
                                    <div class="hb-list-row__label">Title</div>
                                    <div class="hb-list-row__value">{{ $traveler['title'] ?: '-' }}</div>
                                </div>
                                <div class="hb-list-row">
                                    <div class="hb-list-row__label">First Name</div>
                                    <div class="hb-list-row__value">{{ $traveler['first_name'] ?: '-' }}</div>
                                </div>
                                <div class="hb-list-row">
                                    <div class="hb-list-row__label">Last Name</div>
                                    <div class="hb-list-row__value">{{ $traveler['last_name'] ?: '-' }}</div>
                                </div>
                                <div class="hb-list-row">
                                    <div class="hb-list-row__label">Date of Birth</div>
                                    <div class="hb-list-row__value">{{ $traveler['dob'] ?: '-' }}</div>
                                </div>
                                <div class="hb-list-row">
                                    <div class="hb-list-row__label">Nationality</div>
                                    <div class="hb-list-row__value">{{ $traveler['nationality'] ?: '-' }}</div>
                                </div>
                                <div class="hb-list-row">
                                    <div class="hb-list-row__label">Gender</div>
                                    <div class="hb-list-row__value">{{ $traveler['gender'] ?: '-' }}</div>
                                </div>
                                <div class="hb-list-row">
                                    <div class="hb-list-row__label">Passport Number</div>
                                    <div class="hb-list-row__value">{{ $traveler['passport'] ?: '-' }}</div>
                                </div>
                                <div class="hb-list-row">
                                    <div class="hb-list-row__label">Passport Expiry</div>
                                    <div class="hb-list-row__value">{{ $traveler['passport_expiry'] ?: '-' }}</div>
                                </div>
                            @endforeach
                        @elseif($passenger)
                            <div class="hb-list-row">
                                <div class="hb-list-row__label">Title</div>
                                <div class="hb-list-row__value">{{ $passenger->title ?: '-' }}</div>
                            </div>
                            <div class="hb-list-row">
                                <div class="hb-list-row__label">First Name</div>
                                <div class="hb-list-row__value">{{ $passenger->first_name ?: '-' }}</div>
                            </div>
                            <div class="hb-list-row">
                                <div class="hb-list-row__label">Last Name</div>
                                <div class="hb-list-row__value">{{ $passenger->last_name ?: '-' }}</div>
                            </div>
                        @else
                            <p class="text-muted mb-0">Traveler details are not available for this booking.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 hb-col-right">
                <div class="hb-card">
                    <div class="hb-card__head">Payment Summary</div>
                    <div class="hb-card__body">
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Payment Method</div>
                            <div class="hb-list-row__value">{{ $paymentGateway }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Payment Status</div>
                            <div class="hb-list-row__value">{{ $statusText }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Price / Person</div>
                            <div class="hb-list-row__value">{{ $currency }} {{ number_format($unitPrice, 2) }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Participants</div>
                            <div class="hb-list-row__value">{{ $participants }}</div>
                        </div>
                        <div class="hb-list-row">
                            <div class="hb-list-row__label">Booked On</div>
                            <div class="hb-list-row__value">{{ optional($booking->created_at)->format('d M Y') ?: '-' }}</div>
                        </div>
                        @if((float) $booking->paid > 0)
                            <div class="hb-list-row">
                                <div class="hb-list-row__label">Paid</div>
                                <div class="hb-list-row__value">{{ $currency }} {{ number_format((float) $booking->paid, 2) }}</div>
                            </div>
                        @endif
                        <div class="hb-total">
                            <span>Total</span>
                            <span>{{ $currency }} {{ number_format((float) $booking->total, 2) }}</span>
                        </div>
                    </div>
                    <div class="hb-card__foot">
                        <div class="d-grid gap-2">
                            <a href="{{ route('activities.index') }}" class="hb-btn hb-btn--ghost">Back to Activities</a>
                            @if($booking->getMeta('activity_slug'))
                                <a href="{{ route('activities.show', ['activity' => $booking->getMeta('activity_slug'), 'activity_date' => $activityDate]) }}" class="hb-btn">View Activity</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
