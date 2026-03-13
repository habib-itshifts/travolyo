<div class="hotel-card">
    @php
        $isLocalHotel = $hotel->source === 'local_db' && !empty($hotel->slug);
        $b2bCode = \Illuminate\Support\Str::after($hotel->id, 'b2b_');
        $selectedAdults = max(1, (int) request('adults', 1));
        $selectedChildren = max(0, (int) request('children', 0));
        $selectedUnits = max(1, (int) request('unit', request('rooms', 1)));
        $selectedCheckIn = (string) request('check_in', request('checkin', ''));
        $selectedCheckOut = (string) request('check_out', request('checkout', ''));

        $dealUrl = $isLocalHotel
            ? route('frontend.hotels.detail', [
                'slug' => $hotel->slug,
                'location' => request('location', ''),
                'country_code' => request('country_code', ''),
                'checkin' => $selectedCheckIn,
                'checkout' => $selectedCheckOut,
                'adults' => $selectedAdults,
                'children' => $selectedChildren,
                'rooms' => $selectedUnits,
                'unit' => $selectedUnits,
                'guests' => $selectedAdults + $selectedChildren,
            ])
            : route('frontend.hotels.b2b.deal', [
                'code' => $b2bCode !== '' ? $b2bCode : $hotel->id,
                'location' => request('location', ''),
                'check_in' => $selectedCheckIn,
                'check_out' => $selectedCheckOut,
                'adults' => $selectedAdults,
                'children' => $selectedChildren,
                'unit' => $selectedUnits,
                'country_code' => request('country_code', 'AE'),
                'name' => $hotel->name,
                'address' => $hotel->address,
                'image' => $hotel->image_url,
                'star_rate' => $hotel->star_rate,
                'price' => $hotel->price,
            ]);
    @endphp

    {{-- Image --}}
    <div class="hotel-card__img-wrap">
        <img src="{{ $hotel->image_url }}"
             alt="{{ $hotel->name }}" loading="lazy" />
    </div>

    {{-- Body --}}
    <div class="hotel-card__body">
        <div class="hotel-card__top">

            {{-- Info --}}
            <div class="hotel-card__info">
                <h6 class="hotel-card__name">{{ $hotel->name }}</h6>
                <p class="hotel-card__location">
                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $hotel->address }}
                </p>

                {{-- Star Rating --}}
                @if($hotel->star_rate > 0)
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $hotel->star_rate ? '-fill' : '' }} text-warning"
                               style="font-size:.75rem;"></i>
                        @endfor
                    </div>
                @endif

                {{-- Source Badge --}}
                <span class="amenity-tag">
                    <i class="bi bi-database me-1"></i>
                    {{ $hotel->source === 'local_db' ? 'Local' : 'B2B' }}
                </span>
            </div>

        </div>

        {{-- Price Row --}}
        <div class="hotel-card__price-row">
            <div class="hotel-card__price">
                <span class="price-per-night">Per night</span>
                <div class="d-flex align-items-baseline gap-2">
                    @if($hotel->original_price)
                        <span class="price-original">${{ number_format($hotel->original_price) }}</span>
                    @endif
                    <span class="price-current">${{ number_format($hotel->price) }}</span>
                </div>
            </div>
            <a href="{{ $dealUrl }}" class="btn btn-view-deal">View Deal</a>
        </div>
    </div>

</div>
