@php
    $activityImage = $activity->image_url ?: asset('assets/images/favicon/favicon1.png');
@endphp

<div class="hotel-card">
    <div class="hotel-card__img-wrap">
        <img src="{{ $activityImage }}" alt="{{ $activity->title }}" loading="lazy">
    </div>

    <div class="hotel-card__body">
        <div class="hotel-card__top">
            <div class="hotel-card__info">
                <h6 class="hotel-card__name">{{ $activity->title }}</h6>
                <p class="hotel-card__location">
                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $activity->city ?: '-' }}, {{ $activity->country ?: '-' }}
                </p>

                <div class="hotel-card__tags">
                    @if ($activity->category)
                        <span class="amenity-tag">{{ $activity->category }}</span>
                    @endif
                    @if ($activity->duration)
                        <span class="amenity-tag"><i class="bi bi-clock me-1"></i>{{ $activity->duration }}</span>
                    @endif
                    @if ($activity->instant_confirmation)
                        <span class="amenity-tag"><i class="bi bi-check2-circle me-1"></i>Instant</span>
                    @endif
                    @if ($activity->max_participants)
                        <span class="amenity-tag d-none" hidden><i class="bi bi-people me-1"></i>{{ $activity->max_participants }} max</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="hotel-card__price-row">
            <div class="hotel-card__price">
                <span class="price-per-night">Per person</span>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="price-current">{{ $activity->convertedCurrency ?: $activity->baseCurrency ?: 'AED' }} {{ number_format((float) ($activity->price_per_person ?: 0), 2) }}</span>
                </div>
            </div>
            <a href="{{ route('activities.checkout', ['activity' => $activity, 'city' => request('city'), 'date' => request('activity_date', now()->format('Y-m-d')), 'participants' => max(1, (int) request('participants', 1))]) }}"
               class="btn btn-view-deal">View Deal</a>
        </div>
    </div>
</div>
