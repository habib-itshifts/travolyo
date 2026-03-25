@extends('layouts.master')

@section('title', 'Booking Confirmation — ' . $booking->code)

@php
    $h = $hotelDetails ?? [];
    $activities = $activities ?? [];
    $activitySearchParams = $activitySearchParams ?? [];
    $statusClass = match(strtolower((string) $booking->status)) {
        'confirmed', 'paid', 'completed' => 'completed',
        'draft', 'unpaid'                => 'draft',
        default                          => 'failed',
    };
    $isPaid  = $statusClass === 'completed';
    $nights  = 0;
    if (!empty($h['check_in']) && !empty($h['check_out'])) {
        $nights = \Carbon\Carbon::parse($h['check_in'])->diffInDays(\Carbon\Carbon::parse($h['check_out']));
    }
    $activitySearchUrl = route('activities.index', $activitySearchParams);
    $activityLocation = trim((string) (($activitySearchParams['city'] ?? '') ?: ($h['city'] ?? '')));
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

.booking-status-badge { display: inline-flex; align-items: center; gap: .45rem; padding: .45rem 1.1rem; border-radius: 2rem; font-size: .85rem; font-weight: 700; letter-spacing: .03em; }
.status--completed { background: #dcfce7; color: #15803d; }
.status--draft     { background: #fef9c3; color: #a16207; }
.status--failed    { background: #fee2e2; color: #b91c1c; }

.detail-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.25rem; }
.detail-card__header { padding: 1.1rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: .75rem; }
.detail-card__icon { width: 36px; height: 36px; border-radius: 50%; background: var(--primary-light, #e0f7fa); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1rem; flex-shrink: 0; }
.detail-card__title    { font-size: 1rem; font-weight: 700; color: #1a2942; margin: 0; }
.detail-card__subtitle { font-size: .8rem; color: #6c757d; margin: 0; }
.detail-card__body     { padding: 1.5rem; }

.hotel-banner { background: linear-gradient(135deg, var(--primary) 0%, #0e9aa7 100%); padding: 1.5rem 1.75rem; color: #fff; border-radius: 12px 12px 0 0; }
.hb-name { font-size: 1.2rem; font-weight: 800; }
.hb-meta { font-size: .82rem; opacity: .85; margin-top: .4rem; }

.fd-row { display: flex; align-items: flex-start; gap: .75rem; padding: .8rem 0; border-bottom: 1px solid #e5e7eb; }
.fd-row:last-child { border-bottom: none; padding-bottom: 0; }
.fd-icon { width: 32px; height: 32px; border-radius: 50%; background: var(--primary-light, #e0f7fa); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: .85rem; flex-shrink: 0; margin-top: .1rem; }
.fd-label { font-size: .75rem; color: #6c757d; font-weight: 500; }
.fd-value { font-size: .9rem; color: #1a2942; font-weight: 600; }

.price-row { display: flex; justify-content: space-between; font-size: .88rem; color: #6c757d; padding: .45rem 0; }
.price-row.total { font-size: 1.15rem; font-weight: 700; color: #1a2942; border-top: 2px solid #e5e7eb; margin-top: .5rem; padding-top: .75rem; }
.price-row.total span:last-child { color: var(--primary); }

.btn-action { border-radius: .65rem; padding: .65rem 1.25rem; font-size: .88rem; font-weight: 600; display: inline-flex; align-items: center; gap: .5rem; text-decoration: none; transition: all .2s; }
.btn-action--primary { background: var(--primary); color: #fff; border: none; }
.btn-action--primary:hover { background: var(--primary-dark, #0097a7); color: #fff; }
.btn-action--outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
.btn-action--outline:hover { background: var(--primary-light, #e0f7fa); }

.activity-card { display: flex; gap: .8rem; padding: .85rem; border: 1px solid #e5e7eb; border-radius: 12px; text-decoration: none; color: inherit; transition: all .2s ease; }
.activity-card + .activity-card { margin-top: .85rem; }
.activity-card:hover { border-color: #b6eff5; box-shadow: 0 10px 22px rgba(14, 154, 167, .12); transform: translateY(-1px); }
.activity-card__image { width: 86px; height: 86px; border-radius: 12px; object-fit: cover; flex-shrink: 0; background: #f1f5f9; }
.activity-card__title { font-size: .95rem; font-weight: 700; line-height: 1.35; color: #1a2942; margin: 0 0 .2rem; }
.activity-card__meta { font-size: .77rem; color: #6c757d; margin-bottom: .35rem; }
.activity-card__tag { display: inline-flex; align-items: center; gap: .25rem; padding: .24rem .55rem; border-radius: 999px; background: #ecfeff; color: #0f766e; font-size: .72rem; font-weight: 600; margin-right: .35rem; margin-bottom: .35rem; }
.activity-card__bottom { display: flex; align-items: center; justify-content: space-between; gap: .75rem; margin-top: .45rem; }
.activity-card__price { font-size: .72rem; color: #6c757d; }
.activity-card__price strong { display: block; color: var(--primary); font-size: 1.05rem; line-height: 1.1; }
.activity-card__cta { display: inline-flex; align-items: center; justify-content: center; min-width: 102px; padding: .45rem .8rem; border-radius: 999px; background: #dff9fd; color: #11bcd6; font-size: .8rem; font-weight: 700; }
.activity-sidebar-btn { width: 100%; justify-content: center; margin-top: 1rem; }
</style>
@endpush

@section('content')

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
                Your hotel has been booked. A confirmation will be sent to <strong>{{ $booking->email }}</strong>.
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

        {{-- Hotel Card --}}
        <div class="detail-card">
            <div class="hotel-banner">
                <div class="hb-name">{{ $h['hotel_name'] ?? 'Hotel' }}</div>
                <div class="hb-meta">
                    <i class="bi bi-geo-alt me-1"></i>{{ $h['city'] ?? '' }}{{ !empty($h['country']) ? ', ' . $h['country'] : '' }}
                </div>
            </div>
            <div class="detail-card__body">
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-door-open"></i></div>
                    <div>
                        <div class="fd-label">Check-in</div>
                        <div class="fd-value">{{ $h['check_in'] ?? '—' }}</div>
                    </div>
                </div>
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-door-closed"></i></div>
                    <div>
                        <div class="fd-label">Check-out</div>
                        <div class="fd-value">{{ $h['check_out'] ?? '—' }}</div>
                    </div>
                </div>
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-moon"></i></div>
                    <div>
                        <div class="fd-label">Duration</div>
                        <div class="fd-value">{{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</div>
                    </div>
                </div>
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="fd-label">Room</div>
                        <div class="fd-value">{{ $h['room_name'] ?? '—' }}</div>
                    </div>
                </div>
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-people"></i></div>
                    <div>
                        <div class="fd-label">Guests</div>
                        <div class="fd-value">
                            {{ $h['adults'] ?? 1 }} Adult{{ ($h['adults'] ?? 1) != 1 ? 's' : '' }}
                            @if(($h['children'] ?? 0) > 0)
                                , {{ $h['children'] }} Child{{ $h['children'] != 1 ? 'ren' : '' }}
                            @endif
                        </div>
                    </div>
                </div>
                @if(!empty($booking->customer_notes))
                <div class="fd-row">
                    <div class="fd-icon"><i class="bi bi-chat-dots"></i></div>
                    <div>
                        <div class="fd-label">Special Requests</div>
                        <div class="fd-value">{{ $booking->customer_notes }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Guest Info --}}
        <div class="detail-card">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-person-check"></i></div>
                <div><p class="detail-card__title">Guest Information</p></div>
            </div>
            <div class="detail-card__body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="fd-label">Full Name</div>
                        <div class="fd-value">{{ trim($booking->first_name . ' ' . $booking->last_name) ?: '—' }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="fd-label">Email</div>
                        <div class="fd-value">{{ $booking->email ?: '—' }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="fd-label">Phone</div>
                        <div class="fd-value">{{ $booking->phone ?: '—' }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="fd-label">Booked On</div>
                        <div class="fd-value">{{ $booking->created_at?->format('d M Y, H:i') ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Important Info --}}
        <div class="detail-card">
            <div class="detail-card__header">
                <div class="detail-card__icon"><i class="bi bi-info-circle"></i></div>
                <div><p class="detail-card__title">Important Information</p></div>
            </div>
            <div class="detail-card__body">
                <ul class="list-unstyled mb-0" style="font-size:.875rem;">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>Your booking confirmation will be sent to your email address.</li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>Displayed prices include all applicable taxes and fees.</li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0 mt-1"></i>Guest name must match the ID presented at check-in.</li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0 mt-1"></i>Please review the hotel's cancellation policy before your stay.</li>
                    <li class="d-flex gap-2"><i class="bi bi-clock-fill text-primary flex-shrink-0 mt-1"></i>Contact the hotel directly for early check-in or late check-out requests.</li>
                </ul>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('hotels.index') }}" class="btn-action btn-action--outline">
                <i class="bi bi-search"></i> Search More Hotels
            </a>
            <a href="javascript:window.print()" class="btn-action btn-action--outline">
                <i class="bi bi-printer"></i> Print / Save PDF
            </a>
        </div>

    </div>

    {{-- RIGHT ──────────────────────────── --}}
    <div class="col-12 col-lg-4">
        <div style="position:sticky; top:80px;">

            @if(!empty($activities))
            <div class="detail-card">
                <div class="detail-card__header">
                    <div class="detail-card__icon"><i class="bi bi-map"></i></div>
                    <div>
                        <p class="detail-card__title">Recommended Activities</p>
                        <p class="detail-card__subtitle">Top things to do in {{ $activityLocation ?: 'your destination' }}</p>
                    </div>
                </div>
                <div class="detail-card__body">
                    @foreach($activities as $activity)
                        @php
                            $activityImage = $activity->imageUrl ?: asset('assets/images/favicon/favicon1.png');
                            $activityCheckoutUrl = route('activities.checkout', [
                                'activity' => $activity->dbActivityId ?: $activity->offerId,
                                'city' => $activitySearchParams['city'] ?? '',
                                'date' => $activitySearchParams['activity_date'] ?? now()->toDateString(),
                                'participants' => $activitySearchParams['participants'] ?? 1,
                            ]);
                        @endphp
                        <a href="{{ $activityCheckoutUrl }}" class="activity-card">
                            <img src="{{ $activityImage }}" alt="{{ $activity->title }}" class="activity-card__image" loading="lazy">
                            <div class="flex-grow-1">
                                <h6 class="activity-card__title">{{ $activity->title }}</h6>
                                <div class="activity-card__meta">
                                    {{ $activity->city ?: ($activityLocation ?: '-') }}{{ $activity->country ? ', ' . $activity->country : '' }}
                                </div>
                                <div>
                                    @if($activity->category)
                                        <span class="activity-card__tag">{{ $activity->category }}</span>
                                    @endif
                                    @if($activity->duration)
                                        <span class="activity-card__tag"><i class="bi bi-clock"></i>{{ $activity->duration }}</span>
                                    @endif
                                </div>
                                <div class="activity-card__bottom">
                                    <div class="activity-card__price">
                                        from
                                        <strong>{{ $activity->currency }} {{ number_format((float) $activity->pricePerPerson, 2) }}</strong>
                                    </div>
                                    <span class="activity-card__cta">Book Now</span>
                                </div>
                            </div>
                        </a>
                    @endforeach

                    <a href="{{ $activitySearchUrl }}" class="btn-action btn-action--outline activity-sidebar-btn">
                        <i class="bi bi-search"></i> Explore More Activities
                    </a>
                </div>
            </div>
            @endif

            <div class="detail-card">
                <div class="detail-card__header">
                    <div class="detail-card__icon"><i class="bi bi-receipt"></i></div>
                    <div><p class="detail-card__title">Price Summary</p></div>
                </div>
                <div class="detail-card__body pt-2">
                    @php
                        $unitP  = (float) ($h['unit_price'] ?? $booking->total);
                        $totalP = (float) $booking->total;
                    @endphp
                    <div class="price-row">
                        <span>{{ $h['currency'] ?? $booking->currency }} {{ number_format($unitP, 2) }} × {{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</span>
                        <span>{{ $booking->currency }} {{ number_format($totalP, 2) }}</span>
                    </div>
                    <div class="price-row">
                        <span>Taxes &amp; fees</span>
                        <span>Included</span>
                    </div>
                    <div class="price-row total">
                        <span>Total {{ $isPaid ? 'Paid' : 'Due' }}</span>
                        <span>{{ $booking->currency }} {{ number_format($totalP, 2) }}</span>
                    </div>
                    <div class="mt-3 p-2 rounded" style="background:#f8f9fa;font-size:.8rem;color:#6c757d;">
                        <div class="d-flex justify-content-between">
                            <span>Currency</span>
                            <strong>{{ $booking->currency ?? 'USD' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Payment via</span>
                            <strong>{{ ucfirst($gateway) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Provider</span>
                            <strong>{{ str_replace('_', ' ', ucwords($h['provider'] ?? '—', '_')) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 p-3 rounded mt-2"
                 style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:.82rem;color:#15803d;">
                <i class="bi bi-shield-fill-check" style="font-size:1.1rem;"></i>
                <span>Your booking is confirmed and payment secured.</span>
            </div>

            <div class="detail-card mt-3">
                <div class="detail-card__body">
                    <p class="mb-2" style="font-size:.9rem;font-weight:600;color:#1a2942;">Need help?</p>
                    <p class="text-muted mb-0" style="font-size:.82rem;">
                        <i class="bi bi-headset me-1"></i>Our support team is available 24/7.
                        Quote reference <strong>{{ $booking->code }}</strong> when you contact us.
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
</section>

@endsection
