@extends('layouts.master')

@section('title', ($params['hotel_name'] ?? 'Hotel') . ' — Available Rooms')

@php
    $nights = 0;
    if (!empty($params['check_in']) && !empty($params['check_out'])) {
        $nights = \Carbon\Carbon::parse($params['check_in'])->diffInDays(\Carbon\Carbon::parse($params['check_out']));
    }
    $nights   = max(1, (int) $nights);
    $currency = $params['currency'] ?? 'USD';
    $adults    = (int) ($params['adults'] ?? 1);
    $children  = (int) ($params['children'] ?? 0);
    $childAges = $params['child_ages'] ?? [];
@endphp

@push('styles')
<style>
.rooms-hero {
    background: linear-gradient(135deg, var(--primary, #17c3ce) 0%, #0e9aa7 100%);
    padding: 2rem 0 3.5rem; color: #fff;
}
.rooms-hero__title { font-size: 1.4rem; font-weight: 700; }
.rooms-hero__meta  { font-size: .88rem; opacity: .85; margin-top: .35rem; }

.room-card { background: #fff; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 14px; transition: border-color .15s, box-shadow .15s; }
.room-card:hover { border-color: var(--primary, #17c3ce); box-shadow: 0 4px 16px rgba(0,0,0,.08); }
.room-card__name  { font-weight: 700; font-size: 1rem; color: #1a2942; }
.room-card__meta  { font-size: .82rem; color: #6c757d; margin: 6px 0; }
.room-card__price { font-size: 1.2rem; font-weight: 700; color: var(--primary, #17c3ce); }
.room-card__original { font-size: .78rem; color: #b91c1c; text-decoration: line-through; }
.room-card__total  { font-size: .78rem; color: #6c757d; }

.btn-select-room {
    background: var(--primary, #17c3ce); color: #fff; border: none;
    border-radius: 8px; padding: 8px 22px; font-size: .87rem; font-weight: 600;
    transition: background .15s, transform .15s;
}
.btn-select-room:hover { background: #0e9aa7; transform: translateY(-1px); color: #fff; }
.btn-select-room:disabled { opacity: .65; transform: none; }

.hotel-info-bar { background: #fff; border-radius: 12px; padding: 18px 22px; box-shadow: 0 1px 6px rgba(0,0,0,.07); margin-top: -1.75rem; margin-bottom: 1.5rem; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="rooms-hero">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div>
                <h1 class="rooms-hero__title mb-1">
                    <i class="bi bi-building me-2 opacity-75"></i>{{ $params['hotel_name'] ?? 'Hotel' }}
                </h1>
                <div class="rooms-hero__meta">
                    @if($params['hotel_stars'] ?? 0)
                        @for($i = 1; $i <= 5; $i++)
                            <span style="color:{{ $i <= ($params['hotel_stars'] ?? 0) ? '#fbbf24' : 'rgba(255,255,255,.3)' }}">&#9733;</span>
                        @endfor
                        &nbsp;
                    @endif
                    @if(!empty($params['city']))
                        <i class="bi bi-geo-alt me-1"></i>{{ $params['city'] }}{{ !empty($params['country']) ? ', ' . $params['country'] : '' }}
                    @endif
                </div>
            </div>
            <a href="{{ url()->previous(route('hotels.index')) }}" class="text-white text-decoration-none small opacity-75">
                <i class="bi bi-arrow-left me-1"></i>Back to results
            </a>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 0; padding-bottom: 3rem;">

    {{-- Hotel info bar --}}
    <div class="hotel-info-bar d-flex flex-wrap gap-4">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar3 text-primary"></i>
            <div>
                <div style="font-size:.7rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Check-in</div>
                <div style="font-weight:600;font-size:.9rem">
                    {{ $params['check_in'] }}
                    @if(!empty($params['check_in_time']))
                        <span class="text-muted fw-normal" style="font-size:.8rem">at {{ $params['check_in_time'] }}</span>
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
                    @if(!empty($params['check_out_time']))
                        <span class="text-muted fw-normal" style="font-size:.8rem">by {{ $params['check_out_time'] }}</span>
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
                <div style="font-weight:600;font-size:.9rem">
                    {{ $adults }} Adult{{ $adults !== 1 ? 's' : '' }}
                    @if($children > 0), {{ $children }} Child{{ $children !== 1 ? 'ren' : '' }}@endif
                </div>
                @if(!empty($childAges))
                    <div style="font-size:.75rem;color:#6c757d;margin-top:2px">
                        Ages: {{ implode(', ', array_map(fn($a) => (int)$a === 0 ? '< 1' : (int)$a, $childAges)) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(!empty($params['hotel_description']))
        <p class="text-muted mb-4" style="font-size:.9rem">{{ $params['hotel_description'] }}</p>
    @endif

    {{-- Rooms --}}
    <h5 class="fw-bold mb-3">Available Rooms</h5>

    @if(empty($rooms))
        <div class="alert alert-warning">No rooms available for the selected dates. Please try different dates.</div>
    @else
        @foreach($rooms as $room)
        @php
            $isDiscounted = $room->isDiscounted();
        @endphp
        <div class="room-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">

                {{-- Left: room info --}}
                <div class="flex-fill">
                    <div class="room-card__name mb-1">{{ $room->name }}</div>
                    <div class="room-card__meta">
                        @if($room->bedConfiguration)
                            <i class="bi bi-moon me-1"></i>
                            {{ is_array($room->bedConfiguration) ? implode(', ', array_map(fn($k,$v) => "$v $k", array_keys($room->bedConfiguration), $room->bedConfiguration)) : $room->bedConfiguration }}
                            &nbsp;
                        @endif
                        @if($room->maxAdults)
                            <i class="bi bi-person me-1"></i>{{ $room->maxAdults }} Adults
                        @endif
                        @if($room->sizeSqm)
                            &nbsp;<i class="bi bi-aspect-ratio me-1"></i>{{ $room->sizeSqm }} m²
                        @endif
                        @if($room->viewType)
                            &nbsp;<i class="bi bi-eye me-1"></i>{{ $room->viewType }}
                        @endif
                    </div>

                    @if(!empty($room->amenityNames))
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            @foreach(array_slice($room->amenityNames, 0, 6) as $amenity)
                                <span class="badge bg-light text-dark border" style="font-size:.72rem">{{ $amenity }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($room->description)
                        <p class="text-muted mt-2 mb-0" style="font-size:.82rem">{{ $room->description }}</p>
                    @endif
                </div>

                {{-- Right: price + button --}}
                <div class="text-md-end flex-shrink-0" style="min-width:160px">
                    @if($isDiscounted)
                        <div class="room-card__original">{{ $room->convertedCurrency }} {{ number_format($room->convertedOriginalPrice, 0) }}</div>
                    @endif
                    <div class="room-card__price">
                        {{ $room->convertedCurrency }} {{ number_format($room->convertedCurrentPrice, 0) }}
                        @if($isDiscounted)
                            <span class="badge bg-danger" style="font-size:.65rem;vertical-align:middle">Discount</span>
                        @endif
                        @if($room->dealId)
                            <span class="badge bg-success" style="font-size:.65rem;vertical-align:middle">Deal</span>
                        @endif
                    </div>
                    <div class="room-card__total">{{ $nights }} nights total: {{ $room->convertedCurrency }} {{ number_format($room->convertedTotalPrice, 0) }}</div>

                    <button class="btn-select-room mt-3 js-select-room"
                        data-offer-id="{{ $params['offer_id'] }}"
                        data-room-id="{{ $room->roomId }}"
                        data-provider="{{ $params['provider'] }}"
                        data-hotel-name="{{ $params['hotel_name'] ?? '' }}"
                        data-room-name="{{ $room->name }}"
                        data-city="{{ $params['city'] ?? '' }}"
                        data-country="{{ $params['country'] ?? '' }}"
                        data-check-in="{{ $params['check_in'] }}"
                        data-check-out="{{ $params['check_out'] }}"
                        data-adults="{{ $params['adults'] }}"
                        data-children="{{ $params['children'] }}"
                        data-deal-id="{{ $room->dealId ?? '' }}">
                        Select Room
                    </button>
                </div>

            </div>
        </div>
        @endforeach
    @endif

</div>

@endsection

@push('scripts')
<script>
(function () {
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    const prebookUrl = '{{ route('api.hotels.prebook') }}';

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.js-select-room');
        if (!btn) return;

        if (!isLoggedIn) {
            window.openAuthModal?.('signin');
            return;
        }

        btn.disabled    = true;
        btn.textContent = 'Please wait…';

        const payload = {
            offer_id:   btn.dataset.offerId,
            room_id:    btn.dataset.roomId,
            provider:   btn.dataset.provider,
            hotel_name: btn.dataset.hotelName,
            room_name:  btn.dataset.roomName,
            city:       btn.dataset.city,
            country:    btn.dataset.country,
            check_in:   btn.dataset.checkIn,
            check_out:  btn.dataset.checkOut,
            adults:     parseInt(btn.dataset.adults ?? 1, 10),
            children:   parseInt(btn.dataset.children ?? 0, 10),
            currency:   document.querySelector('meta[name="currency"]')?.content ?? 'USD',
        };

        try {
            const res  = await fetch(prebookUrl, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (data.success && data.checkout_url) {
                window.location.href = data.checkout_url;
                return;
            }

            alert(data.message ?? 'Could not select this room. Please try again.');
        } catch {
            alert('Network error. Please try again.');
        }

        btn.disabled    = false;
        btn.textContent = 'Select Room';
    });
})();
</script>
@endpush
