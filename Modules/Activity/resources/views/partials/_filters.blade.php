<div class="filter-panel">
    <h5 class="filter-panel__title">Filters</h5>

    <form action="{{ route('activities.index') }}" method="GET" id="activityFiltersForm">
        @foreach (request()->except(['price_max', 'category', 'instant_confirmation', 'page']) as $key => $value)
            @if (is_array($value))
                @foreach (\Illuminate\Support\Arr::dot([$key => $value]) as $nestedKey => $nestedValue)
                    @php
                        $segments = explode('.', $nestedKey);
                        $inputName = array_shift($segments);
                        foreach ($segments as $segment) {
                            $inputName .= '[' . $segment . ']';
                        }
                    @endphp
                    <input type="hidden" name="{{ $inputName }}" value="{{ $nestedValue }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <div class="filter-group">
            <h6 class="filter-group__label">Price per Person</h6>
            <input type="range" class="price-slider" id="activityPriceRange" name="price_max"
                   min="0" max="{{ $maxPrice }}" step="10"
                   value="{{ (int) request('price_max', $maxPrice) }}">
            <div class="d-flex justify-content-between mt-1">
                <span class="text-muted small">AED 0</span>
                <span class="text-muted small" id="activityPriceRangeValue">
                    AED {{ (int) request('price_max', $maxPrice) }}
                </span>
            </div>
        </div>

        <div class="filter-group">
            <h6 class="filter-group__label">Category</h6>
            <div class="filter-radio">
                <input class="form-check-input" type="radio" name="category" id="acat_all" value="" {{ request('category', '') === '' ? 'checked' : '' }}>
                <label class="form-check-label" for="acat_all">All Categories</label>
            </div>
            @foreach ($categories as $category)
                <div class="filter-radio">
                    <input class="form-check-input" type="radio" name="category" id="acat_{{ \Illuminate\Support\Str::slug($category) }}"
                           value="{{ $category }}" {{ request('category') === $category ? 'checked' : '' }}>
                    <label class="form-check-label" for="acat_{{ \Illuminate\Support\Str::slug($category) }}">{{ $category }}</label>
                </div>
            @endforeach
        </div>

        <div class="filter-group mb-0">
            <h6 class="filter-group__label">Booking Options</h6>
            <div class="filter-check">
                <input class="form-check-input" type="checkbox" name="instant_confirmation"
                       id="activityInstantConfirmation" value="1" {{ request('instant_confirmation') ? 'checked' : '' }}>
                <label class="form-check-label" for="activityInstantConfirmation">Instant Confirmation</label>
            </div>
        </div>

        <div class="mt-4 d-grid gap-2">
            <button type="submit" class="btn btn-search py-2">
                <i class="bi bi-funnel me-2"></i>Apply Filters
            </button>
            <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-sm text-center">
                Clear Filters
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    (() => {
        const activitySlider = document.getElementById('activityPriceRange');
        const activityDisplay = document.getElementById('activityPriceRangeValue');

        if (!activitySlider || !activityDisplay) {
            return;
        }

        const syncActivityPriceUI = () => {
            const val = activitySlider.value;
            activityDisplay.textContent = 'AED ' + val;
            const pct = ((val - activitySlider.min) / (activitySlider.max - activitySlider.min || 1)) * 100;
            activitySlider.style.background = `linear-gradient(to right, var(--primary) 0%, var(--primary) ${pct}%, #e5e7eb ${pct}%)`;
        };

        activitySlider.addEventListener('input', syncActivityPriceUI);
        syncActivityPriceUI();
    })();
</script>
@endpush
