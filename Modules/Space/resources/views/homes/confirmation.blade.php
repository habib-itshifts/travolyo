@extends('layouts.master')

@section('title', 'Booking Confirmation — ' . $booking->code)

@php
    $sd = $spaceDetails ?? [];
    $statusClass = match(strtolower((string) $booking->status)) {
        'confirmed', 'paid', 'completed' => 'completed',
        'draft', 'unpaid'                => 'draft',
        default                          => 'failed',
    };
    $isPaid  = $statusClass === 'completed';
    $nights  = 0;
    if (!empty($sd['check_in']) && !empty($sd['check_out'])) {
        $nights = \Carbon\Carbon::parse($sd['check_in'])->diffInDays(\Carbon\Carbon::parse($sd['check_out']));
    }
@endphp

@push('styles')
<style>
.booking-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%);
    padding: 2.5rem 0 4.5rem; color: #fff;
}
.booking-hero__icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: 1rem; }
.booking-hero__title { font-size: 1.6rem; font-weight: 800; margin-bottom: .35rem; }
.booking-hero__sub   { font-size: .9rem; opacity: .85; }

.booking-status-badge { display: inline-flex; align-items: center; gap: .45rem; padding: .45rem 1.1rem; border-radius: 2rem; font-size: .85rem; font-weight: 700; }
.status--completed { background: #dcfce7; color: #15803d; }
.status--draft     { background: #fef9c3; color: #a16207; }
.status--failed    { background: #fee2e2; color: #b91c1c; }

.detail-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.25rem; }
.detail-card__header { padding: 1.1rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: .75rem; }
.detail-card__icon { width: 36px; height: 36px; border-radius: 50%; background: #e0f7fa; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1rem; flex-shrink: 0; }
.detail-card__title { font-size: 1rem; font-weight: 700; color: #1a2942; margin: 0; }
.detail-card__body  { padding: 1.5rem; }

.fd-row { display: flex; align-items: flex-start; gap: .75rem; padding: .8rem 0; border-bottom: 1px solid #e5e7eb; }
.fd-row:last-child { border-bottom: none; padding-bottom: 0; }
.fd-icon { width: 32px; height: 32px; border-radius: 50%; background: #e0f7fa; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: .85rem; flex-shrink: 0; }
.fd-label { font-size: .75rem; color: #6c757d; font-weight: 500; }
.fd-value { font-size: .9rem; color: #1a2942; font-weight: 600; }

.price-row { display: flex; justify-content: space-between; font-size: .88rem; color: #6c757d; padding: .45rem 0; }
.price-row.total { font-size: 1.15rem; font-weight: 700; color: #1a2942; border-top: 2px solid #e5e7eb; margin-top: .5rem; padding-top: .75rem; }
.price-row.total span:last-child { color: var(--primary); }

.btn-action { border-radius: .65rem; padding: .65rem 1.25rem; font-size: .88rem; font-weight: 600; display: inline-flex; align-items: center; gap: .5rem; text-decoration: none; transition: all .2s; }
.btn-action--primary { background: var(--primary); color: #fff; border: none; }
.btn-action--primary:hover { background: #0e9aa7; color: #fff; }
.btn-action--outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
.btn-action--outline:hover { background: #e0f7fa; }
</style>
@endpush

@section('content')

{{-- Hero --}}
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
                Your stay has been booked. A confirmation will be sent to <strong>{{ $booking->email }}</strong>.
            @elseif($statusClass === 'draft')
                We're still waiting for payment confirmation.
            @else
                Something went wrong. Please try again or contact support.
            @endif
        </p>
        <span class="booking-status-badge status--{{ $statusClass }}">
            @if($isPaid)<i class="bi bi-check-circle-fill"></i> Confirmed
            @elseif($statusClass === 'draft')<i class="bi bi-clock-fill"></i> Pending
            @else<i class="bi bi-x-circle-fill"></i> Failed
            @endif
        </span>
    </div>
</div>

{{-- Main --}}
<section class="py-4" style="background:#f8f9fa; margin-top:-2rem;">
<div class="container">
<div class="row g-4">

    <div class="col-12 col-lg-8">

        {{-- Booking reference --}}
        <div class="detail-card">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-bookmark-check"></i></div>
                <div><p class="detail-card__title">Booking Reference</p></div>
            </div>
            <div class="detail-card__body">
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-hash"></i></div>
                    <div>
                        <div class="fd-label">Booking Code</div>
                        <div class="fd-value" style="font-size:1.1rem;letter-spacing:.03em">{{ $booking->code }}</div>
                    </div>
                </div>
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-credit-card"></i></div>
                    <div>
                        <div class="fd-label">Payment Method</div>
                        <div class="fd-value">{{ ucfirst($gateway) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stay details --}}
        <div class="detail-card">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-house-door"></i></div>
                <div><p class="detail-card__title">Stay Details</p></div>
            </div>
            <div class="detail-card__body">
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="fd-label">Property</div>
                        <div class="fd-value">{{ $sd['space_name'] ?? '-' }}</div>
                    </div>
                </div>
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <div class="fd-label">Location</div>
                        <div class="fd-value">{{ $sd['city'] ?? '' }}{{ !empty($sd['country']) ? ', ' . $sd['country'] : '' }}</div>
                    </div>
                </div>
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <div class="fd-label">Check-in / Check-out</div>
                        <div class="fd-value">{{ $sd['check_in'] ?? '-' }} &rarr; {{ $sd['check_out'] ?? '-' }} ({{ $nights }} night{{ $nights !== 1 ? 's' : '' }})</div>
                    </div>
                </div>
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-people"></i></div>
                    <div>
                        <div class="fd-label">Guests</div>
                        <div class="fd-value">{{ $sd['guests'] ?? 1 }} Guest{{ ($sd['guests'] ?? 1) !== 1 ? 's' : '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-3 flex-wrap">
            <a href="{{ route('homes.index') }}" class="btn-action btn-action--primary">
                <i class="bi bi-search"></i> Search More
            </a>
            <button class="btn-action btn-action--outline" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>

    </div>

    {{-- Price summary sidebar --}}
    <div class="col-12 col-lg-4">
        <div class="detail-card">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-receipt"></i></div>
                <div><p class="detail-card__title">Price Summary</p></div>
            </div>
            <div class="detail-card__body pt-2">
                <div class="price-row">
                    <span>Accommodation</span>
                    <span>{{ $booking->currency ?? 'USD' }} {{ number_format((float) $booking->total, 2) }}</span>
                </div>
                <div class="price-row total">
                    <span>Total Paid</span>
                    <span>{{ $booking->currency ?? 'USD' }} {{ number_format((float) $booking->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
</section>

@endsection
