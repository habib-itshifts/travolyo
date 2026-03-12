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

@endsection
