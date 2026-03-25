@extends('layouts.master')

@section('title', 'Activities')

@section('content')
<section class="hero-section hero-section--compact d-flex align-items-center">
    <div class="container text-center text-white">
        <h1 class="hero-title fw-bold mb-2">Find Amazing Activities</h1>
        <p class="hero-subtitle mb-4">
            Search city-wise activities and experiences<br class="d-none d-md-block">
            for your selected date
        </p>

        @include('website.partials._search-widget', ['activeTab' => 'activities'])
    </div>
</section>

<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="row g-4">
            <div class="col-12 col-lg-3">
                @include('activity::partials._filters')
            </div>

            <div class="col-12 col-lg-9">
                <div class="results-header d-flex justify-content-between align-items-center mb-3">
                    <div>
                        @php
                            $searchedLocation = collect([request('city'), request('country')])
                                ->filter(fn ($value) => filled($value))
                                ->implode(', ');
                        @endphp
                        <h5 class="results-header__title mb-0">Available Activities</h5>
                        <p class="text-muted small mb-0">
                            Showing {{ $activities->count() }} {{ \Illuminate\Support\Str::plural('activity', $activities->count()) }}
                            @if ($searchedLocation)
                                in <strong>{{ $searchedLocation }}</strong>
                            @endif
                            @if (request('activity_date'))
                                for <strong>{{ request('activity_date') }}</strong>
                            @endif
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="view-toggle">
                            <button class="view-btn active" id="listViewBtn" title="List view">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button class="view-btn" id="gridViewBtn" title="Grid view">
                                <i class="bi bi-grid"></i>
                            </button>
                        </div>
                        <div class="sort-wrap">
                            <i class="bi bi-arrow-down-up me-1 text-muted"></i>
                            <span class="text-muted small me-1">Sort by:</span>
                            <select class="sort-select" onchange="window.location.href=this.value">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'recommended']) }}"
                                    {{ request('sort', 'recommended') === 'recommended' ? 'selected' : '' }}>
                                    Recommended
                                </option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}"
                                    {{ request('sort') === 'price_asc' ? 'selected' : '' }}>
                                    Price: Low to High
                                </option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}"
                                    {{ request('sort') === 'price_desc' ? 'selected' : '' }}>
                                    Price: High to Low
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="hotelResults">
                    @forelse ($activities as $activity)
                        @include('activity::partials._activity-card', ['activity' => $activity])
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size:3rem;"></i>
                            <h5 class="mt-3 text-muted">No activities found</h5>
                            <p class="text-muted small">Try adjusting your filters or search in a different city.</p>
                        </div>
                    @endforelse
                </div>

                @if ($activities->hasPages())
                    <div class="flight-pagination mt-4">
                        {{ $activities->appends(request()->except('page'))->links('vendor.pagination.flight') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    (() => {
        const listBtn = document.getElementById('listViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');
        const results = document.getElementById('hotelResults');

        if (!listBtn || !gridBtn || !results) {
            return;
        }

        listBtn.addEventListener('click', () => {
            results.classList.remove('grid-view');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        });

        gridBtn.addEventListener('click', () => {
            results.classList.add('grid-view');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        });
    })();
</script>
@endpush
