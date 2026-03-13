<div class="filter-panel">
    <h5 class="filter-panel__title">Filters</h5>

    <form action="{{ request()->url() }}" method="GET" id="filtersForm">
        {{-- Preserve existing search params --}}
        @foreach(request()->except(['price_max', 'stars', 'property_type', 'amenities', 'guest_rating', 'free_cancel', 'pay_at_property', 'breakfast', 'page']) as $key => $value)
            @if(is_array($value))
                @foreach(\Illuminate\Support\Arr::dot([$key => $value]) as $nestedKey => $nestedValue)
                    @php
                        $segments = explode('.', $nestedKey);
                        $inputName = array_shift($segments);
                        foreach ($segments as $segment) {
                            $inputName .= '[' . $segment . ']';
                        }
                    @endphp
                    <input type="hidden" name="{{ $inputName }}" value="{{ $nestedValue }}" />
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
            @endif
        @endforeach

        {{-- Price per Night --}}
        <div class="filter-group">
            <h6 class="filter-group__label">Price per Night</h6>
            <input type="range" class="price-slider" id="priceRange" name="price_max"
                   min="0" max="1000" step="25"
                   value="{{ request('price_max', 1000) }}" />
            <div class="d-flex justify-content-between mt-1">
                <span class="text-muted small">$0</span>
                <span class="text-muted small" id="priceRangeValue">
                    ${{ request('price_max', 1000) }}
                </span>
            </div>
        </div>

        {{-- Star Rating --}}
        <div class="filter-group">
            <h6 class="filter-group__label">Star Rating</h6>
            @foreach([5, 4, 3, 2] as $star)
                <div class="filter-radio">
                    <input class="form-check-input" type="radio" name="stars"
                           id="stars{{ $star }}" value="{{ $star }}"
                           {{ request('stars') == $star ? 'checked' : '' }} />
                    <label class="form-check-label" for="stars{{ $star }}">
                        <span class="star-row">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $star ? '-fill' : '' }}"></i>
                            @endfor
                        </span>
                        {{ $star }} Stars
                    </label>
                </div>
            @endforeach
        </div>

        {{-- Property Type --}}
        <div class="filter-group">
            <h6 class="filter-group__label">Property Type</h6>
            @foreach(['hotel' => 'Hotels', 'apartment' => 'Apartments', 'resort' => 'Resorts', 'villa' => 'Villas', 'hostel' => 'Hostels'] as $value => $label)
                <div class="filter-radio">
                    <input class="form-check-input" type="radio" name="property_type"
                           id="pt_{{ $value }}" value="{{ $value }}"
                           {{ request('property_type') === $value ? 'checked' : '' }} />
                    <label class="form-check-label" for="pt_{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        {{-- Amenities --}}
        <div class="filter-group">
            <h6 class="filter-group__label">Amenities</h6>
            @foreach(['wifi' => 'Free WiFi', 'parking' => 'Parking', 'pool' => 'Pool', 'gym' => 'Gym', 'restaurant' => 'Restaurant', 'spa' => 'Spa', 'ac' => 'Air Conditioning'] as $value => $label)
                <div class="filter-check">
                    <input class="form-check-input" type="checkbox" name="amenities[]"
                           id="amen_{{ $value }}" value="{{ $value }}"
                           {{ in_array($value, (array) request('amenities', [])) ? 'checked' : '' }} />
                    <label class="form-check-label" for="amen_{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        {{-- Guest Rating --}}
        <div class="filter-group">
            <h6 class="filter-group__label">Guest Rating</h6>
            @foreach(['9' => 'Excellent (9+)', '8' => 'Very Good (8+)', '7' => 'Good (7+)'] as $value => $label)
                <div class="filter-radio">
                    <input class="form-check-input" type="radio" name="guest_rating"
                           id="gr_{{ $value }}" value="{{ $value }}"
                           {{ request('guest_rating') === $value ? 'checked' : '' }} />
                    <label class="form-check-label" for="gr_{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        {{-- Booking Options --}}
        <div class="filter-group mb-0">
            <h6 class="filter-group__label">Booking Options</h6>
            <div class="filter-check">
                <input class="form-check-input" type="checkbox" name="free_cancel"
                       id="freeCancellation" value="1"
                       {{ request('free_cancel') ? 'checked' : '' }} />
                <label class="form-check-label" for="freeCancellation">Free Cancellation</label>
            </div>
            <div class="filter-check">
                <input class="form-check-input" type="checkbox" name="pay_at_property"
                       id="payAtProperty" value="1"
                       {{ request('pay_at_property') ? 'checked' : '' }} />
                <label class="form-check-label" for="payAtProperty">Pay at Property</label>
            </div>
            <div class="filter-check">
                <input class="form-check-input" type="checkbox" name="breakfast"
                       id="breakfastIncluded" value="1"
                       {{ request('breakfast') ? 'checked' : '' }} />
                <label class="form-check-label" for="breakfastIncluded">Breakfast Included</label>
            </div>
        </div>

        <div class="mt-4 d-grid gap-2">
            <button type="submit" class="btn btn-search py-2">
                <i class="bi bi-funnel me-2"></i>Apply Filters
            </button>
            <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm text-center">
                Clear Filters
            </a>
        </div>

    </form>
</div>

@push('scripts')
<script>
    // Live price range display
    const slider = document.getElementById('priceRange');
    const display = document.getElementById('priceRangeValue');
    if (slider && display) {
        const syncPriceRangeUI = () => {
            const val = slider.value;
            display.textContent = val >= 1000 ? '$1000+' : '$' + val;
            const pct = ((val - slider.min) / (slider.max - slider.min)) * 100;
            slider.style.background = `linear-gradient(to right, var(--primary) 0%, var(--primary) ${pct}%, #e5e7eb ${pct}%)`;
        };

        slider.addEventListener('input', syncPriceRangeUI);
        syncPriceRangeUI();
    }
</script>
@endpush
