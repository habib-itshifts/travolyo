@extends('layouts.master')

@section('title', ($space->name ?? 'Space') . ' — Travolyo')

@php
    $nights = 0;
    if (!empty($params['check_in']) && !empty($params['check_out'])) {
        $nights = \Carbon\Carbon::parse($params['check_in'])->diffInDays(\Carbon\Carbon::parse($params['check_out']));
    }
    $nights   = max(1, (int) $nights);
    $currency = $params['currency'] ?? ($space->currency ?? 'USD');
    $guests   = (int) ($params['guests'] ?? 1);

    $pricePerNight = (float) ($space->sale_price ?: $space->price_per_night);
    $cleaningFee   = (float) ($space->cleaning_fee ?? 0);
    $serviceFee    = (float) ($space->service_fee ?? 0);
    $totalPrice    = ($pricePerNight * $nights) + $cleaningFee + $serviceFee;
    $isDiscounted  = $space->sale_price && $space->sale_price < $space->price_per_night;
@endphp

@push('styles')
<style>
.detail-hero {
    background: linear-gradient(135deg, var(--primary, #17c3ce) 0%, #0e9aa7 100%);
    padding: 2rem 0 3.5rem; color: #fff;
}
.detail-hero__title { font-size: 1.4rem; font-weight: 700; }
.detail-hero__meta  { font-size: .88rem; opacity: .85; margin-top: .35rem; }

.detail-info-bar { background: #fff; border-radius: 12px; padding: 18px 22px; box-shadow: 0 1px 6px rgba(0,0,0,.07); margin-top: -1.75rem; margin-bottom: 1.5rem; }

.space-gallery { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; border-radius: 12px; overflow: hidden; max-height: 320px; margin-bottom: 24px; }
.space-gallery img { width: 100%; height: 160px; object-fit: cover; cursor: pointer; transition: opacity .15s; }
.space-gallery img:hover { opacity: .85; }
.space-gallery img:first-child { grid-row: 1/3; height: 100%; }

.amenity-badge { display: inline-flex; align-items: center; gap: 6px; background: #f0f4ff; border: 1px solid #e0e7ff; border-radius: 8px; padding: 6px 12px; font-size: .82rem; color: #1a2942; }
.amenity-badge i { color: var(--primary, #17c3ce); }

.type-label { display: inline-block; background: #e0f2fe; color: #0369a1; font-size: .75rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; }

.booking-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); border: 1px solid #e5e7eb; overflow: hidden; position: sticky; top: 80px; }
.booking-card__header { background: linear-gradient(135deg, var(--primary, #17c3ce) 0%, #0e9aa7 100%); padding: 1.25rem 1.5rem; color: #fff; }
.booking-card__body { padding: 1.25rem 1.5rem; }
.price-row { display: flex; justify-content: space-between; font-size: .88rem; color: #6c757d; padding: .45rem 0; }
.price-row.total { font-size: 1.1rem; font-weight: 700; color: #1a2942; border-top: 2px solid #e5e7eb; margin-top: .5rem; padding-top: .75rem; }
.price-row.total span:last-child { color: var(--primary, #17c3ce); }

.btn-book {
    background: var(--primary, #17c3ce); color: #fff; border: none;
    border-radius: .75rem; padding: .875rem 2rem; font-size: 1rem; font-weight: 700;
    width: 100%; transition: background .15s, transform .15s;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.btn-book:hover { background: #0e9aa7; transform: translateY(-1px); color: #fff; }
.btn-book:disabled { opacity: .65; transform: none; }

@media (max-width: 576px) {
    .space-gallery { grid-template-columns: 1fr; max-height: none; }
    .space-gallery img:first-child { grid-row: auto; height: 200px; }
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="detail-hero">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div>
                <h1 class="detail-hero__title mb-1">
                    <i class="bi bi-house-door me-2 opacity-75"></i>{{ $space->name }}
                </h1>
                <div class="detail-hero__meta">
                    <span class="type-label" style="background:rgba(255,255,255,.2);color:#fff">{{ ucfirst($space->type ?? 'Space') }}</span>
                    &nbsp;
                    @if($space->city)
                        <i class="bi bi-geo-alt me-1"></i>{{ $space->city }}{{ $space->country ? ', ' . $space->country : '' }}
                    @endif
                </div>
            </div>
            <a href="{{ url()->previous(route('homes.index')) }}" class="text-white text-decoration-none small opacity-75">
                <i class="bi bi-arrow-left me-1"></i>Back to results
            </a>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 0; padding-bottom: 3rem;">

    {{-- Info bar --}}
    <div class="detail-info-bar d-flex flex-wrap gap-4">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar3 text-primary"></i>
            <div>
                <div style="font-size:.7rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Check-in</div>
                <div style="font-weight:600;font-size:.9rem">
                    {{ $params['check_in'] }}
                    @if($space->check_in_time)
                        <span class="text-muted fw-normal" style="font-size:.8rem">at {{ $space->check_in_time }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar-check text-primary"></i>
            <div>
                <div style="font-size:.7rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Check-out</div>
                <div style="font-weight:600;font-size:.9rem">
                    {{ $params['check_out'] }}
                    @if($space->check_out_time)
                        <span class="text-muted fw-normal" style="font-size:.8rem">by {{ $space->check_out_time }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-moon text-primary"></i>
            <div>
                <div style="font-size:.7rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Duration</div>
                <div style="font-weight:600;font-size:.9rem">{{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people text-primary"></i>
            <div>
                <div style="font-size:.7rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Guests</div>
                <div style="font-weight:600;font-size:.9rem">{{ $guests }} Guest{{ $guests !== 1 ? 's' : '' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: details --}}
        <div class="col-12 col-lg-8">

            {{-- Gallery --}}
            @if(count($images))
                <div class="space-gallery mb-4">
                    @foreach(array_slice($images, 0, 3) as $img)
                        <img src="{{ $img }}" alt="{{ $space->name }}" loading="lazy">
                    @endforeach
                </div>
            @endif

            {{-- Description --}}
            @if($space->description)
                <h5 class="fw-bold mb-2">About this place</h5>
                <p class="text-muted" style="font-size:.9rem">{{ $space->description }}</p>
            @endif

            {{-- Details grid --}}
            <div class="d-flex flex-wrap gap-3 mb-4">
                @if($space->bedrooms)
                    <div class="amenity-badge"><i class="bi bi-door-open"></i> {{ $space->bedrooms }} Bedroom{{ $space->bedrooms > 1 ? 's' : '' }}</div>
                @endif
                @if($space->bathrooms)
                    <div class="amenity-badge"><i class="bi bi-droplet"></i> {{ $space->bathrooms }} Bathroom{{ $space->bathrooms > 1 ? 's' : '' }}</div>
                @endif
                @if($space->beds)
                    <div class="amenity-badge"><i class="bi bi-moon"></i> {{ $space->beds }} Bed{{ $space->beds > 1 ? 's' : '' }}</div>
                @endif
                @if($space->max_guests)
                    <div class="amenity-badge"><i class="bi bi-people"></i> Up to {{ $space->max_guests }} guests</div>
                @endif
            </div>

            {{-- Amenities --}}
            @if($space->amenities->count())
                <h5 class="fw-bold mb-2">Amenities</h5>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach($space->amenities as $amenity)
                        <span class="amenity-badge"><i class="bi bi-check2"></i> {{ $amenity->name }}</span>
                    @endforeach
                </div>
            @endif

            {{-- Policies --}}
            @if($space->cancellation_policy || $space->house_rules || $space->min_stay_nights)
                <h5 class="fw-bold mb-2">Policies</h5>
                <div class="mb-4" style="font-size:.88rem;color:#6c757d">
                    @if($space->min_stay_nights)
                        <p class="mb-1"><i class="bi bi-calendar-range me-2 text-primary"></i>Minimum stay: {{ $space->min_stay_nights }} night{{ $space->min_stay_nights > 1 ? 's' : '' }}</p>
                    @endif
                    @if($space->max_stay_nights)
                        <p class="mb-1"><i class="bi bi-calendar-range me-2 text-primary"></i>Maximum stay: {{ $space->max_stay_nights }} night{{ $space->max_stay_nights > 1 ? 's' : '' }}</p>
                    @endif
                    @if($space->cancellation_policy)
                        <p class="mb-1"><i class="bi bi-shield-check me-2 text-primary"></i>{{ $space->cancellation_policy }}</p>
                    @endif
                    @if($space->house_rules)
                        <p class="mb-1"><i class="bi bi-house-gear me-2 text-primary"></i>{{ $space->house_rules }}</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right: booking sidebar --}}
        <div class="col-12 col-lg-4">
            <div class="booking-card">
                <div class="booking-card__header">
                    <div style="font-size:1.3rem;font-weight:700">
                        @if($isDiscounted)
                            <span style="text-decoration:line-through;opacity:.6;font-size:.9rem">{{ $space->currency }} {{ number_format($space->price_per_night, 0) }}</span>
                        @endif
                        {{ $space->currency }} {{ number_format($pricePerNight, 0) }}
                        <span style="font-size:.8rem;font-weight:400;opacity:.8">/ night</span>
                    </div>
                </div>
                <div class="booking-card__body">
                    <div class="price-row"><span>{{ $space->currency }} {{ number_format($pricePerNight, 0) }} x {{ $nights }} night{{ $nights > 1 ? 's' : '' }}</span> <span>{{ $space->currency }} {{ number_format($pricePerNight * $nights, 0) }}</span></div>
                    @if($cleaningFee > 0)
                        <div class="price-row"><span>Cleaning fee</span> <span>{{ $space->currency }} {{ number_format($cleaningFee, 0) }}</span></div>
                    @endif
                    @if($serviceFee > 0)
                        <div class="price-row"><span>Service fee</span> <span>{{ $space->currency }} {{ number_format($serviceFee, 0) }}</span></div>
                    @endif
                    <div class="price-row total"><span>Total</span> <span>{{ $space->currency }} {{ number_format($totalPrice, 0) }}</span></div>

                    <button class="btn-book mt-3" id="bookNowBtn"
                        data-space-id="{{ $space->id }}"
                        data-check-in="{{ $params['check_in'] }}"
                        data-check-out="{{ $params['check_out'] }}"
                        data-guests="{{ $guests }}"
                        data-currency="{{ $currency }}">
                        <i class="bi bi-lightning-charge-fill"></i> Book Now
                    </button>
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
    const bookUrl    = '{{ route('api.spaces.book') }}';

    document.getElementById('bookNowBtn')?.addEventListener('click', async function () {
        if (!isLoggedIn) {
            window.openAuthModal?.('signin');
            return;
        }

        const btn = this;
        btn.disabled    = true;
        btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-2"></span> Please wait…';

        try {
            const res = await fetch(bookUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    space_id:  parseInt(btn.dataset.spaceId, 10),
                    check_in:  btn.dataset.checkIn,
                    check_out: btn.dataset.checkOut,
                    guests:    parseInt(btn.dataset.guests, 10),
                    currency:  btn.dataset.currency,
                }),
            });

            const data = await res.json();

            if (data.success && data.checkout_url) {
                window.location.href = data.checkout_url;
                return;
            }

            alert(data.message ?? 'Could not book this space. Please try again.');
        } catch {
            alert('Network error. Please try again.');
        }

        btn.disabled  = false;
        btn.innerHTML = '<i class="bi bi-lightning-charge-fill"></i> Book Now';
    });
})();
</script>
@endpush
