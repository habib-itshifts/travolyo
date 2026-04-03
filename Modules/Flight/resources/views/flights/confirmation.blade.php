@extends('layouts.master')

@section('title', 'Booking Confirmation — ' . $booking->code)

@push('styles')
<style>
/* ── Booking confirmation page ─────────────── */
.booking-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%);
    padding: 2.5rem 0 4.5rem;
    color: #fff;
}
.booking-hero__icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem; margin-bottom: 1rem;
}
.booking-hero__title { font-size: 1.6rem; font-weight: 800; margin-bottom: .35rem; }
.booking-hero__sub   { font-size: .9rem; opacity: .85; }

/* Status badge */
.booking-status-badge {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .45rem 1.1rem; border-radius: 2rem;
    font-size: .85rem; font-weight: 700; letter-spacing: .03em;
}
.status--confirmed, .status--paid, .status--completed { background: #dcfce7; color: #15803d; }
.status--draft, .status--unpaid                       { background: #fef9c3; color: #a16207; }
.status--cancelled, .status--failed                   { background: #fee2e2; color: #b91c1c; }

/* Cards */
.detail-card {
    background: #fff; border-radius: var(--radius-lg);
    box-shadow: var(--shadow-card); border: 1px solid var(--border);
    overflow: hidden; margin-bottom: 1.25rem;
}
.detail-card__header {
    padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: .75rem;
}
.detail-card__icon {
    width: 36px; height: 36px; border-radius: 50%;
    background: var(--primary-light);
    display: flex; align-items: center; justify-content: center;
    color: var(--primary); font-size: 1rem; flex-shrink: 0;
}
.detail-card__title    { font-size: 1rem; font-weight: 700; color: var(--text-dark); margin: 0; }
.detail-card__subtitle { font-size: .8rem; color: var(--text-muted); margin: 0; }
.detail-card__body     { padding: 1.5rem; }

/* PNR ref badge */
.ref-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    background: var(--primary-light); color: var(--primary);
    font-size: .9rem; font-weight: 700;
    padding: .5rem 1.1rem; border-radius: .5rem;
    letter-spacing: .04em; border: 1.5px dashed var(--primary);
}

/* Flight route banner inside card */
.route-banner {
    background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%);
    padding: 1.5rem 1.75rem; color: #fff;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}
.rb-airports { display: flex; align-items: center; gap: 1rem; font-size: 2rem; font-weight: 800; }
.rb-sep { flex: 1; display: flex; flex-direction: column; align-items: center; }
.rb-sep-line { width: 100%; height: 1px; background: rgba(255,255,255,.4); position: relative; }
.rb-sep-plane { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: .9rem; }
.rb-meta { font-size: .82rem; opacity: .85; margin-top: .5rem; }

/* Detail rows */
.fd-row {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .8rem 0; border-bottom: 1px solid var(--border);
}
.fd-row:last-child { border-bottom: none; padding-bottom: 0; }
.fd-icon {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--primary-light);
    display: flex; align-items: center; justify-content: center;
    color: var(--primary); font-size: .85rem; flex-shrink: 0; margin-top: .1rem;
}
.fd-label { font-size: .75rem; color: var(--text-muted); font-weight: 500; }
.fd-value { font-size: .9rem; color: var(--text-dark); font-weight: 600; }

/* Passenger table */
.pax-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.pax-table th {
    padding: .55rem .75rem; background: #f8f9fa;
    border-bottom: 2px solid var(--border);
    color: var(--text-muted); font-size: .75rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
}
.pax-table td { padding: .65rem .75rem; border-bottom: 1px solid var(--border); color: var(--text-dark); }
.pax-table tr:last-child td { border-bottom: none; }

/* Price rows */
.price-row { display: flex; justify-content: space-between; font-size: .88rem; color: var(--text-muted); padding: .45rem 0; }
.price-row.total { font-size: 1.15rem; font-weight: 700; color: var(--text-dark); border-top: 2px solid var(--border); margin-top: .5rem; padding-top: .75rem; }
.price-row.total span:last-child { color: var(--primary); }

/* Action buttons */
.btn-action {
    border-radius: .65rem; padding: .65rem 1.25rem;
    font-size: .88rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: .5rem;
    text-decoration: none; transition: all var(--transition);
}
.btn-action--primary { background: var(--primary); color: #fff; border: none; }
.btn-action--primary:hover { background: var(--primary-dark); color: #fff; }
.btn-action--outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
.btn-action--outline:hover { background: var(--primary-light); }
.hotel-suggestion-card {
    display: block; height: 100%; text-decoration: none; color: inherit;
    border: 1px solid var(--border); border-radius: 1rem; overflow: hidden;
    background: #fff; transition: transform var(--transition), box-shadow var(--transition);
}
.hotel-suggestion-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
    color: inherit;
}
.hotel-suggestion-card__media {
    height: 120px; background: linear-gradient(135deg, #d9f4f7 0%, #f5fbfc 100%);
    display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.hotel-suggestion-card__media img {
    width: 100%; height: 100%; object-fit: cover;
}
.hotel-suggestion-card__body {
    padding: .8rem .85rem .85rem;
}
.hotel-suggestion-card__name {
    font-size: .9rem; font-weight: 700; color: var(--text-dark); margin-bottom: .25rem;
}
.hotel-suggestion-card__meta,
.hotel-suggestion-card__desc {
    font-size: .74rem; color: var(--text-muted);
}
.hotel-suggestion-card__desc {
    min-height: 2rem; margin: .45rem 0 .55rem;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.hotel-suggestion-card__amenities {
    display: flex; flex-wrap: wrap; gap: .3rem; margin-bottom: .6rem;
}
.hotel-suggestion-card__amenity {
    font-size: .63rem; line-height: 1;
    padding: .32rem .45rem; border-radius: 999px;
    background: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1;
}
.hotel-suggestion-card__footer {
    display: flex; justify-content: space-between; align-items: center; gap: .6rem;
}
.hotel-suggestion-card__price {
    font-size: 1rem; font-weight: 800; color: var(--primary); line-height: 1.1;
}
.hotel-suggestion-card__price-note,
.hotel-suggestion-card__link {
    font-size: .68rem; color: var(--text-muted);
}
.hotel-suggestion-card__link {
    font-weight: 700; color: var(--primary);
    padding: .38rem .55rem; border-radius: 999px; background: var(--primary-light);
    white-space: nowrap;
}
.hotel-sidebar-list {
    display: flex; flex-direction: column; gap: .75rem;
}
.hotel-sidebar-item {
    display: grid; grid-template-columns: 84px minmax(0, 1fr); gap: .75rem;
    text-decoration: none; color: inherit; padding: .7rem;
    border: 1px solid var(--border); border-radius: .9rem; background: #fff;
    transition: transform var(--transition), box-shadow var(--transition);
}
.hotel-sidebar-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
    color: inherit;
}
.hotel-sidebar-item__media {
    width: 84px; height: 84px; border-radius: .75rem; overflow: hidden;
    background: linear-gradient(135deg, #d9f4f7 0%, #f5fbfc 100%);
    display: flex; align-items: center; justify-content: center;
}
.hotel-sidebar-item__media img {
    width: 100%; height: 100%; object-fit: cover;
}
.hotel-sidebar-item__name {
    font-size: .85rem; font-weight: 700; color: var(--text-dark); margin-bottom: .15rem;
}
.hotel-sidebar-item__meta,
.hotel-sidebar-item__desc,
.hotel-sidebar-item__note {
    font-size: .72rem; color: var(--text-muted);
}
.hotel-sidebar-item__desc {
    margin: .3rem 0 .45rem;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.hotel-sidebar-item__footer {
    display: flex; align-items: center; justify-content: space-between; gap: .5rem;
}
.hotel-sidebar-item__price {
    font-size: .95rem; font-weight: 800; color: var(--primary); line-height: 1.1;
}
.hotel-sidebar-item__cta {
    font-size: .68rem; font-weight: 700; color: var(--primary);
    padding: .32rem .5rem; border-radius: 999px; background: var(--primary-light);
    white-space: nowrap; border: none; cursor: pointer;
}
.hotel-room-modal .modal-dialog { max-width: 860px; }
.hotel-room-modal .modal-header {
    background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%);
    color: #fff;
}
.hotel-room-modal .modal-header .btn-close { filter: invert(1); }
.hotel-room-gallery {
    display: grid; grid-template-columns: 1fr 1fr; gap: 6px;
    border-radius: 10px; overflow: hidden; max-height: 220px; margin-bottom: 16px;
}
.hotel-room-gallery img { width: 100%; height: 110px; object-fit: cover; }
.hotel-room-gallery img:first-child { grid-row: 1 / 3; height: 100%; }
.room-card {
    border: 1px solid var(--border); border-radius: 12px; padding: 14px; margin-bottom: 12px;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.room-card:hover {
    border-color: var(--primary);
    box-shadow: 0 10px 22px rgba(15, 23, 42, .06);
}
.room-card__name {
    font-size: .92rem; font-weight: 700; color: var(--text-dark);
}
.room-card__meta,
.room-card__sub {
    font-size: .78rem; color: var(--text-muted);
}
.room-card__price {
    font-size: 1rem; font-weight: 800; color: var(--primary);
}
.btn-select-room {
    padding: .48rem .9rem; font-size: .78rem; border-radius: .7rem; font-weight: 700;
}
</style>
@endpush

@section('content')

@php
    $f = $booking->getJsonMeta('flight_details');
    $statusClass = match(strtolower((string) $booking->status)) {
        'confirmed', 'paid', 'completed' => 'completed',
        'draft', 'unpaid'                => 'draft',
        default                          => 'failed',
    };
    $isPaid  = $statusClass === 'completed';
    $symbol  = match(strtoupper($booking->currency ?? 'USD')) {
        'EUR' => '€', 'GBP' => '£', 'AED' => 'AED ', default => '$',
    };
    $gateway = $booking->getMeta('payment_gateway') ?? '—';
    $hotelOffers = collect($offers ?? [])->take(3)->values();
    $hotelCheckInDate = null;
    $hotelCheckOutDate = null;
    $hotelDestination = $hotelOffers->isNotEmpty()
        ? trim(($hotelOffers->first()->city ?? '') . (($hotelOffers->first()->country ?? '') ? ', ' . $hotelOffers->first()->country : ''))
        : '';
    $hotelTripDates = '';

    if (!empty($f['arr_date'])) {
        try {
            $hotelCheckInDate = \Carbon\Carbon::parse($f['arr_date']);
            $hotelCheckOutDate = \Carbon\Carbon::parse($f['arr_date'])->addDays(2);
            $hotelTripDates = $hotelCheckInDate->format('d M') . ' - ' . $hotelCheckOutDate->format('d M Y');
        } catch (\Throwable) {
            $hotelTripDates = '';
        }
    }

    $hotelSearchUrl = $hotelOffers->isNotEmpty()
        ? route('hotels.index', array_filter([
            'destination' => $hotelOffers->first()->city ?: ($hotelOffers->first()->country ?: ''),
            'city' => $hotelOffers->first()->city ?: '',
            'check_in' => $hotelCheckInDate?->format('Y-m-d'),
            'check_out' => $hotelCheckOutDate?->format('Y-m-d'),
            'adults' => max(1, (int) ($f['adults'] ?? 1)),
            'children' => max(0, (int) ($f['children'] ?? 0)),
            'rooms' => max(1, (int) ceil(((int) ($f['adults'] ?? 1) + (int) ($f['children'] ?? 0)) / 2)),
        ], fn ($value) => $value !== null && $value !== ''))
        : route('hotels.index');
@endphp

{{-- ── Hero ─────────────────────────────── --}}
<div class="booking-hero">
    <div class="container">
        <div class="booking-hero__icon">
            @if($isPaid)<i class="bi bi-check2-circle"></i>
            @elseif($statusClass === 'draft')<i class="bi bi-hourglass-split"></i>
            @else<i class="bi bi-x-circle"></i>
            @endif
        </div>
        <h1 class="booking-hero__title">
            @if($isPaid)Booking Confirmed!
            @elseif($statusClass === 'draft')Payment Pending
            @else Booking Failed
            @endif
        </h1>
        <p class="booking-hero__sub mb-3">
            @if($isPaid)
                Your flight has been booked. A confirmation will be sent to <strong>{{ $booking->email }}</strong>.
            @elseif($statusClass === 'draft')
                Your payment is being processed. We will notify you once confirmed.
            @else
                Unfortunately your booking could not be completed. Please try again.
            @endif
        </p>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="booking-status-badge status--{{ $statusClass }}">
                <i class="bi bi-circle-fill" style="font-size:.55rem;"></i>
                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
            </span>
            <span class="opacity-75 small">
                <i class="bi bi-hash me-1"></i>Ref: <strong>{{ $booking->code }}</strong>
            </span>
        </div>
    </div>
</div>

{{-- ── Body ─────────────────────────────── --}}
<section class="py-4" style="background:#f8f9fa; margin-top:-2rem;">
<div class="container">
<div class="row g-4">

    {{-- LEFT ───────────────────────────── --}}
    <div class="col-12 col-lg-8">

        {{-- PNR --}}
        @if($isPaid && !empty($orderRef))
        <div class="detail-card">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-bookmark-check"></i></div>
                <div>
                    <p class="detail-card__title">Booking Reference (PNR)</p>
                    <p class="detail-card__subtitle">Use this reference at the airport check-in</p>
                </div>
            </div>
            <div class="detail-card__body text-center py-3">
                <div class="ref-badge mx-auto" style="max-width:fit-content;">
                    <i class="bi bi-ticket-perforated-fill"></i>
                    {{ strtoupper($orderRef) }}
                </div>
                <p class="text-muted small mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Present your PNR at check-in along with a valid photo ID and passport.
                </p>
            </div>
        </div>
        @endif

        @if(!empty($hotelOffers))
        <div class="detail-card d-none">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-buildings"></i></div>
                <div>
                    <p class="detail-card__title">Recommended Stays</p>
                    <p class="detail-card__subtitle">
                        Best stay options for your trip{{ $hotelDestination !== '' ? ' in ' . $hotelDestination : '' }}{{ $hotelTripDates !== '' ? ' &bull; ' . $hotelTripDates : '' }}
                    </p>
                </div>
            </div>
            <div class="detail-card__body">
                <div class="row g-2">
                    @foreach($hotelOffers as $hotel)
                        @php
                            $hotelImage = $hotel->images[0] ?? null;
                            $hotelCurrency = strtoupper($hotel->convertedCurrency ?: ($booking->currency ?? 'USD'));
                            $hotelSymbol = match($hotelCurrency) {
                                'EUR' => '€', 'GBP' => '£', 'AED' => 'AED ', default => '$',
                            };
                            $hotelAmenities = array_slice($hotel->amenityNames ?? [], 0, 2);
                        @endphp
                        <div class="col-12 col-md-4">
                            <a href="{{ $hotelSearchUrl }}" class="hotel-suggestion-card">
                                <div class="hotel-suggestion-card__media">
                                    @if($hotelImage)
                                        <img src="{{ $hotelImage }}" alt="{{ $hotel->name }}">
                                    @else
                                        <i class="bi bi-building fs-3 text-muted"></i>
                                    @endif
                                </div>
                                <div class="hotel-suggestion-card__body">
                                    <div class="hotel-suggestion-card__name">{{ $hotel->name }}</div>
                                    <div class="hotel-suggestion-card__meta">
                                        {{ str_repeat('★', max(0, (int) $hotel->starRating)) }}
                                        @if($hotel->city || $hotel->country)
                                            &bull; {{ $hotel->city ?: '-' }}{{ $hotel->country ? ', ' . $hotel->country : '' }}
                                        @endif
                                    </div>
                                    <div class="hotel-suggestion-card__desc">
                                        {{ \Illuminate\Support\Str::limit($hotel->shortDescription ?: ($hotel->description ?: $hotel->address), 72) }}
                                    </div>
                                    @if(!empty($hotelAmenities))
                                    <div class="hotel-suggestion-card__amenities">
                                        @foreach($hotelAmenities as $amenity)
                                            <span class="hotel-suggestion-card__amenity">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="hotel-suggestion-card__footer">
                                        <div>
                                            <div class="hotel-suggestion-card__price">{{ $hotelSymbol }}{{ number_format((float) $hotel->convertedLowestPrice, 0) }}</div>
                                            <div class="hotel-suggestion-card__price-note">per night</div>
                                        </div>
                                        <div class="hotel-suggestion-card__link">View stay</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <a href="{{ $hotelSearchUrl }}" class="btn-action btn-action--outline" style="padding:.55rem 1rem;font-size:.82rem;">
                        <i class="bi bi-search"></i> Explore More Stays
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Flight route card --}}
        @include('flight::flights.partials._booking-flight-card', ['f' => $f, 'booking' => $booking, 'symbol' => $symbol])

        {{-- Passengers --}}
        @include('flight::flights.partials._passengers-table', ['passengers' => $passengers])

        {{-- Contact --}}
        <div class="detail-card">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-envelope"></i></div>
                <div><p class="detail-card__title">Contact Information</p></div>
            </div>
            <div class="detail-card__body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="fd-label">Email</div>
                        <div class="fd-value">{{ $booking->email ?: '—' }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="fd-label">Phone</div>
                        <div class="fd-value">{{ $booking->phone ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Important info --}}
        <div class="detail-card">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-info-circle"></i></div>
                <div><p class="detail-card__title">Important Information</p></div>
            </div>
            <div class="detail-card__body">
                <ul class="list-unstyled mb-0" style="font-size:.875rem;">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>Your e-ticket will be sent to your email address.</li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>Displayed prices include all applicable taxes and fees.</li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0 mt-1"></i>Passenger names must exactly match your passport details.</li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0 mt-1"></i>Please check airline baggage policy before travel.</li>
                    <li class="d-flex gap-2"><i class="bi bi-clock-fill text-primary flex-shrink-0 mt-1"></i>Arrive at the airport at least 2 hours before departure.</li>
                </ul>
            </div>
        </div>

        @if(!empty($hotelOffers))
        <div class="detail-card d-none">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-buildings"></i></div>
                <div>
                    <p class="detail-card__title">Recommended Stays</p>
                    <p class="detail-card__subtitle">
                        Best stay options for your trip{{ $hotelDestination !== '' ? ' in ' . $hotelDestination : '' }}{{ $hotelTripDates !== '' ? ' • ' . $hotelTripDates : '' }}
                    </p>
                </div>
            </div>
            <div class="detail-card__body">
                <div class="row g-3">
                    @foreach($hotelOffers as $hotel)
                        @php
                            $hotelImage = $hotel->images[0] ?? null;
                            $hotelCurrency = strtoupper($hotel->convertedCurrency ?: ($booking->currency ?? 'USD'));
                            $hotelSymbol = match($hotelCurrency) {
                                'EUR' => '€', 'GBP' => '£', 'AED' => 'AED ', default => '$',
                            };
                            $hotelAmenities = array_slice($hotel->amenityNames ?? [], 0, 3);
                        @endphp
                        <div class="col-12 col-md-4">
                            <a href="{{ $hotelSearchUrl }}" class="hotel-suggestion-card">
                                <div class="hotel-suggestion-card__media">
                                    @if($hotelImage)
                                        <img src="{{ $hotelImage }}" alt="{{ $hotel->name }}">
                                    @else
                                        <i class="bi bi-building fs-1 text-muted"></i>
                                    @endif
                                </div>
                                <div class="hotel-suggestion-card__body">
                                    <div class="hotel-suggestion-card__name">{{ $hotel->name }}</div>
                                    <div class="hotel-suggestion-card__meta">
                                        {{ str_repeat('★', max(0, (int) $hotel->starRating)) }}
                                        @if($hotel->city || $hotel->country)
                                            • {{ $hotel->city ?: '-' }}{{ $hotel->country ? ', ' . $hotel->country : '' }}
                                        @endif
                                    </div>
                                    <div class="hotel-suggestion-card__desc">
                                        {{ \Illuminate\Support\Str::limit($hotel->shortDescription ?: ($hotel->description ?: $hotel->address), 90) }}
                                    </div>
                                    @if(!empty($hotelAmenities))
                                    <div class="hotel-suggestion-card__amenities">
                                        @foreach($hotelAmenities as $amenity)
                                            <span class="hotel-suggestion-card__amenity">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="hotel-suggestion-card__footer">
                                        <div>
                                            <div class="hotel-suggestion-card__price">{{ $hotelSymbol }}{{ number_format((float) $hotel->convertedLowestPrice, 0) }}</div>
                                            <div class="hotel-suggestion-card__price-note">per night</div>
                                        </div>
                                        <div class="hotel-suggestion-card__link">View hotels</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <a href="{{ $hotelSearchUrl }}" class="btn-action btn-action--outline">
                        <i class="bi bi-search"></i> Search More Hotels
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('flights.index') }}" class="btn-action btn-action--outline">
                <i class="bi bi-search"></i> Search More Flights
            </a>
            <a href="javascript:window.print()" class="btn-action btn-action--outline">
                <i class="bi bi-printer"></i> Print / Save PDF
            </a>
        </div>

    </div>{{-- /left --}}


    {{-- RIGHT ──────────────────────────── --}}
    <div class="col-12 col-lg-4">
        <div style="position:sticky; top:80px;">

            @if(!empty($hotelOffers))
            <div class="detail-card">
                <div class="detail-card__header">
                    <div class="detail-card__icon"><i class="bi bi-buildings"></i></div>
                    <div>
                        <p class="detail-card__title">Recommended Stays</p>
                        <p class="detail-card__subtitle">
                            {{ $hotelDestination !== '' ? $hotelDestination : 'Your destination' }}{{ $hotelTripDates !== '' ? ' • ' . $hotelTripDates : '' }}
                        </p>
                    </div>
                </div>
                <div class="detail-card__body" style="padding:1rem;">
                    <div class="hotel-sidebar-list">
                        @foreach($hotelOffers as $hotel)
                            @php
                                $hotelImage = $hotel->images[0] ?? null;
                                $hotelCurrency = strtoupper($hotel->convertedCurrency ?: ($booking->currency ?? 'USD'));
                                $hotelSymbol = match($hotelCurrency) {
                                    'EUR' => '€', 'GBP' => '£', 'AED' => 'AED ', default => '$',
                                };
                            @endphp
                            @php
                                $hotelCheckIn = !empty($f['arr_date']) ? \Carbon\Carbon::parse($f['arr_date'])->format('Y-m-d') : now()->addDay()->format('Y-m-d');
                                $hotelCheckOut = !empty($f['arr_date']) ? \Carbon\Carbon::parse($f['arr_date'])->addDays(2)->format('Y-m-d') : now()->addDays(3)->format('Y-m-d');
                                $hotelPayload = [
                                    'id' => $hotel->offerId,
                                    'provider' => $hotel->provider->value,
                                    'name' => $hotel->name,
                                    'star_rating' => $hotel->starRating,
                                    'city' => $hotel->city,
                                    'country' => $hotel->country,
                                    'address' => $hotel->address,
                                    'description' => $hotel->description,
                                    'short_description' => $hotel->shortDescription,
                                    'check_in_time' => $hotel->checkInTime,
                                    'check_out_time' => $hotel->checkOutTime,
                                    'images' => $hotel->images,
                                    'amenities' => $hotel->amenityNames,
                                    'lowest_price' => $hotel->convertedLowestPrice,
                                    'currency' => $hotelCurrency,
                                    'check_in' => $hotelCheckIn,
                                    'check_out' => $hotelCheckOut,
                                    'adults' => (int) ($f['adults'] ?? 1),
                                    'children' => (int) ($f['children'] ?? 0),
                                    'rooms' => collect($hotel->rooms ?? [])->map(fn ($room) => [
                                        'id' => $room->roomId,
                                        'name' => $room->name,
                                        'room_type' => $room->roomType,
                                        'bed_configuration' => is_array($room->bedConfiguration)
                                            ? collect($room->bedConfiguration)->map(fn ($count, $type) => $count . ' ' . ucfirst((string) $type))->implode(', ')
                                            : (string) $room->bedConfiguration,
                                        'max_adults' => $room->maxAdults,
                                        'max_children' => $room->maxChildren,
                                        'base_price' => $room->basePrice,
                                        'total_price' => $room->totalPrice,
                                        'nights' => $room->nights,
                                        'currency' => $room->currency,
                                        'is_available' => $room->isAvailable,
                                        'amenities' => $room->amenityNames,
                                        'size_sqm' => $room->sizeSqm,
                                        'description' => $room->description,
                                    ])->values()->all(),
                                ];
                            @endphp
                            <a href="{{ $hotelSearchUrl }}" class="hotel-sidebar-item">
                                <div class="hotel-sidebar-item__media">
                                    @if($hotelImage)
                                        <img src="{{ $hotelImage }}" alt="{{ $hotel->name }}">
                                    @else
                                        <i class="bi bi-building fs-4 text-muted"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="hotel-sidebar-item__name">{{ \Illuminate\Support\Str::limit($hotel->name, 38) }}</div>
                                    <div class="hotel-sidebar-item__meta">
                                        {{ str_repeat('★', max(0, (int) $hotel->starRating)) }}
                                        @if($hotel->city || $hotel->country)
                                            • {{ $hotel->city ?: '-' }}{{ $hotel->country ? ', ' . $hotel->country : '' }}
                                        @endif
                                    </div>
                                    <div class="hotel-sidebar-item__desc">
                                        {{ \Illuminate\Support\Str::limit($hotel->shortDescription ?: ($hotel->description ?: $hotel->address), 56) }}
                                    </div>
                                    <div class="hotel-sidebar-item__footer">
                                        <div>
                                            <div class="hotel-sidebar-item__price">{{ $hotelSymbol }}{{ number_format((float) $hotel->convertedLowestPrice, 0) }}</div>
                                            <div class="hotel-sidebar-item__note">per night</div>
                                        </div>
                                        <button
                                            type="button"
                                            class="hotel-sidebar-item__cta js-view-hotel-rooms"
                                            data-hotel='@json($hotelPayload)'
                                        >Select Room</button>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <a href="{{ $hotelSearchUrl }}" class="btn-action btn-action--outline w-100 justify-content-center" style="padding:.55rem .8rem;font-size:.8rem;">
                            <i class="bi bi-search"></i> Explore More Stays
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="detail-card">
                <div class="detail-card__header">
                    <div class="detail-card__icon"><i class="bi bi-receipt"></i></div>
                    <div><p class="detail-card__title">Price Summary</p></div>
                </div>
                <div class="detail-card__body pt-2">
                    <div class="price-row">
                        <span>Base fare × {{ max(1, count($passengers)) }}</span>
                        <span>{{ $symbol }}{{ number_format((float) $booking->total, 2) }}</span>
                    </div>
                    <div class="price-row">
                        <span>Taxes & fees</span>
                        <span>Included</span>
                    </div>
                    <div class="price-row total">
                        <span>Total Paid</span>
                        <span>{{ $symbol }}{{ number_format((float) $booking->total, 2) }}</span>
                    </div>
                    <div class="mt-3 p-2 rounded" style="background:#f8f9fa; font-size:.8rem; color:var(--text-muted);">
                        <div class="d-flex justify-content-between">
                            <span>Currency</span>
                            <strong>{{ $booking->currency ?? 'USD' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Payment via</span>
                            <strong>{{ ucfirst($gateway) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Booked on</span>
                            <strong>{{ $booking->created_at?->format('d M Y') ?? '—' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 p-3 rounded mt-2"
                 style="background:#f0fdf4; border:1px solid #bbf7d0; font-size:.82rem; color:#15803d;">
                <i class="bi bi-shield-fill-check" style="font-size:1.1rem;"></i>
                <span>Your booking is confirmed and payment secured.</span>
            </div>

            <div class="detail-card mt-3">
                <div class="detail-card__body">
                    <p class="mb-2" style="font-size:.9rem; font-weight:600; color:var(--text-dark);">Need help?</p>
                    <p class="text-muted mb-0" style="font-size:.82rem;">
                        <i class="bi bi-headset me-1"></i>Our support team is available 24/7.
                        Quote reference <strong>{{ $booking->code }}</strong> when you contact us.
                    </p>
                </div>
            </div>

        </div>
    </div>{{-- /right --}}

</div>
</div>
</section>

<div class="modal fade hotel-room-modal" id="hotelRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="hotelRoomModalTitle">Select Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="hotelRoomModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    const roomsUrl = '{{ route('api.hotels.rooms') }}';
    const prebookUrl = '{{ route('api.hotels.prebook') }}';
    const modalEl = document.getElementById('hotelRoomModal');
    const modalTitleEl = document.getElementById('hotelRoomModalTitle');
    const modalBodyEl = document.getElementById('hotelRoomModalBody');
    const hotelModal = modalEl ? new bootstrap.Modal(modalEl) : null;

    function stars(count) {
        let html = '';
        for (let i = 1; i <= 5; i++) html += i <= (count ?? 0) ? '★' : '☆';
        return html;
    }

    function renderHotelModal(hotel) {
        modalTitleEl.textContent = hotel.name ?? 'Select Room';

        const galleryHtml = (hotel.images ?? []).length
            ? `<div class="hotel-room-gallery">
                ${hotel.images.slice(0, 3).map(url => `<img src="${url}" alt="" loading="lazy">`).join('')}
               </div>`
            : '';

        const amenitiesHtml = (hotel.amenities ?? []).length
            ? `<div class="mb-3">
                <div class="fw-semibold mb-2" style="font-size:.85rem;">Amenities</div>
                <div class="d-flex flex-wrap gap-2">
                    ${hotel.amenities.slice(0, 8).map(a => `<span class="badge bg-light text-dark border">${a}</span>`).join('')}
                </div>
               </div>`
            : '';

        const rooms = (hotel.rooms ?? []).filter(room => room.is_available !== false);
        const roomsHtml = rooms.length
            ? rooms.map(room => `
                <div class="room-card">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="room-card__name">${room.name ?? 'Room'}</div>
                            <div class="room-card__meta mt-1">
                                ${room.bed_configuration ? `<i class="bi bi-moon me-1"></i>${room.bed_configuration}` : ''}
                                ${room.max_adults ? ` ${room.bed_configuration ? '&bull;' : ''} <i class="bi bi-person me-1"></i>${room.max_adults} Adults` : ''}
                                ${room.max_children ? ` &bull; ${room.max_children} Children` : ''}
                                ${room.size_sqm ? ` &bull; ${room.size_sqm} m²` : ''}
                            </div>
                            ${(room.amenities ?? []).length ? `<div class="d-flex flex-wrap gap-1 mt-2">
                                ${room.amenities.slice(0, 5).map(a => `<span class="badge bg-light text-dark border" style="font-size:.7rem">${a}</span>`).join('')}
                            </div>` : ''}
                            ${room.description ? `<div class="room-card__sub mt-2">${room.description}</div>` : ''}
                        </div>
                        <div class="text-end" style="min-width:140px;">
                            <div class="room-card__price">${room.currency ?? hotel.currency ?? 'USD'} ${parseFloat(room.base_price ?? room.total_price ?? 0).toLocaleString()}</div>
                            <div class="room-card__sub">${room.nights ? `${room.nights} nights total: ${(parseFloat(room.total_price ?? 0)).toLocaleString()}` : 'per night'}</div>
                            <button
                                type="button"
                                class="btn btn-primary btn-select-room mt-2 js-select-confirmation-room"
                                data-offer-id="${hotel.id}"
                                data-room-id="${room.id}"
                                data-provider="${hotel.provider}"
                                data-hotel-name="${hotel.name ?? ''}"
                                data-room-name="${room.name ?? ''}"
                                data-city="${hotel.city ?? ''}"
                                data-country="${hotel.country ?? ''}"
                                data-check-in="${hotel.check_in ?? ''}"
                                data-check-out="${hotel.check_out ?? ''}"
                                data-adults="${hotel.adults ?? 1}"
                                data-children="${hotel.children ?? 0}"
                                data-currency="${hotel.currency ?? 'USD'}"
                            >Select Room</button>
                        </div>
                    </div>
                </div>`).join('')
            : `<div class="alert alert-warning mb-0">No rooms available for the selected stay.</div>`;

        modalBodyEl.innerHTML = `
            ${galleryHtml}
            <div class="mb-2">
                <span style="color:#f59e0b">${stars(hotel.star_rating ?? 0)}</span>
                <span class="ms-2 text-muted small">${hotel.city ?? ''}${hotel.country ? ', ' + hotel.country : ''}</span>
            </div>
            ${(hotel.check_in_time || hotel.check_out_time) ? `
                <div class="d-flex gap-3 mb-3 small text-muted">
                    ${hotel.check_in_time ? `<span><i class="bi bi-door-open me-1"></i>Check-in: <strong>${hotel.check_in_time}</strong></span>` : ''}
                    ${hotel.check_out_time ? `<span><i class="bi bi-door-closed me-1"></i>Check-out: <strong>${hotel.check_out_time}</strong></span>` : ''}
                </div>` : ''}
            ${(hotel.short_description || hotel.description) ? `<p class="text-muted small mb-3">${hotel.short_description ?? hotel.description}</p>` : ''}
            ${amenitiesHtml}
            <hr>
            <h6 class="fw-bold mb-3">Available Rooms</h6>
            ${roomsHtml}
        `;
    }

    document.addEventListener('click', async function (event) {
        const button = event.target.closest('.js-view-hotel-rooms');
        if (!button) return;

        event.preventDefault();
        event.stopPropagation();

        let hotel;
        try {
            hotel = JSON.parse(button.dataset.hotel);
        } catch {
            return;
        }

        modalTitleEl.textContent = hotel.name ?? 'Select Room';
        modalBodyEl.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-3 small mb-0">Checking available rooms...</p>
            </div>`;
        hotelModal?.show();

        try {
            if (hotel.provider !== 'local') {
                const response = await fetch(roomsUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({
                        offer_id: hotel.id,
                        city_code: hotel.city ?? '',
                        check_in: hotel.check_in ?? '',
                        check_out: hotel.check_out ?? '',
                        adults: parseInt(hotel.adults ?? 1, 10),
                        children: parseInt(hotel.children ?? 0, 10),
                        provider: hotel.provider,
                    }),
                });

                const data = await response.json();
                if (response.ok && data.success && Array.isArray(data.data)) {
                    hotel.rooms = data.data;
                }
            }
        } catch {}

        renderHotelModal(hotel);
    });

    document.addEventListener('click', async function (event) {
        const button = event.target.closest('.js-select-confirmation-room');
        if (!button) return;

        event.preventDefault();
        event.stopPropagation();

        if (!isLoggedIn && typeof window.openAuthModal === 'function') {
            hotelModal?.hide();
            window.openAuthModal('signin');
            return;
        }

        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Please wait...';

        try {
            const response = await fetch(prebookUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    offer_id: button.dataset.offerId,
                    room_id: button.dataset.roomId,
                    provider: button.dataset.provider,
                    hotel_name: button.dataset.hotelName,
                    room_name: button.dataset.roomName,
                    city: button.dataset.city,
                    country: button.dataset.country,
                    check_in: button.dataset.checkIn,
                    check_out: button.dataset.checkOut,
                    adults: parseInt(button.dataset.adults ?? '1', 10),
                    children: parseInt(button.dataset.children ?? '0', 10),
                    currency: button.dataset.currency ?? 'USD',
                }),
            });

            const data = await response.json();

            if (response.ok && data.success && data.checkout_url) {
                window.location.href = data.checkout_url;
                return;
            }

            alert(data.message ?? 'Could not open hotel checkout. Please try again.');
        } catch {
            alert('Could not open hotel checkout. Please try again.');
        }

        button.disabled = false;
        button.textContent = originalText;
    });
})();
</script>
@endpush
