@extends('frontend.layouts.app')

@section('title', 'Search Hotels')

@section('content')

    {{-- ═══════════════════════════════════════════
         HERO / SEARCH SECTION
    ════════════════════════════════════════════ --}}
    <section class="hero-section hero-section--hotels hero-section--compact d-flex align-items-center">
        <div class="container text-center text-white">
            <h1 class="hero-title fw-bold mb-2">Find Your Perfect Stay</h1>
            <p class="hero-subtitle mb-4">
                Discover amazing hotels, resorts, and destinations around the world<br class="d-none d-md-block" />
                at the best prices
            </p>

            @include('frontend.hotels.partials._search-form')

            {{-- Quick Links --}}
            <div class="hero-quick-links mt-4">
                <a href="#">Popular Destinations</a>
                <span class="dot"></span>
                <a href="#">Last Minute Deals</a>
                <span class="dot"></span>
                <a href="#">Luxury Hotels</a>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         RESULTS AREA
    ════════════════════════════════════════════ --}}
    <section class="section-padding bg-light-gray">
        <div class="container">
            <div class="row g-4">

                {{-- ── LEFT: Filters Sidebar ──────── --}}
                <div class="col-12 col-lg-3">
                    @include('frontend.hotels.partials._filters')
                </div>

                {{-- ── RIGHT: Hotel Results ────────── --}}
                <div class="col-12 col-lg-9">

                    {{-- Results Header --}}
                    <div class="results-header d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="results-header__title mb-0">Available Hotels</h5>
                            <p class="text-muted small mb-0">
                                Showing {{ $hotels->count() }} {{ Str::plural('property', $hotels->count()) }}
                                @if(request('location'))
                                    in <strong>{{ request('location') }}</strong>
                                @endif
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            {{-- View Toggle --}}
                            <div class="view-toggle">
                                <button class="view-btn active" id="listViewBtn" title="List view">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                                <button class="view-btn" id="gridViewBtn" title="Grid view">
                                    <i class="bi bi-grid"></i>
                                </button>
                            </div>
                            {{-- Sort --}}
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
                                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}"
                                        {{ request('sort') === 'rating' ? 'selected' : '' }}>
                                        Rating
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Hotel Cards --}}
                    <div id="hotelResults">
                        @forelse($hotels as $hotel)
                            @include('frontend.hotels.partials._hotel-card', ['hotel' => $hotel])
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-building-slash text-muted" style="font-size:3rem;"></i>
                                <h5 class="mt-3 text-muted">No hotels found</h5>
                                <p class="text-muted small">Try adjusting your filters or search in a different location.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if($hotels->hasPages())
                        <div class="flight-pagination mt-4">
                            {{ $hotels->appends(request()->except('page'))->links('vendor.pagination.flight') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         WHY BOOK WITH US
    ════════════════════════════════════════════ --}}
    <section class="section-padding why-section" style="background:#f5f6f8;">
        <div class="container text-center">
            <h2 class="section-title mb-5">Why Book With Us?</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-3">
                    <div class="why-card">
                        <div class="why-card__icon"><i class="bi bi-house-check-fill"></i></div>
                        <h6 class="why-card__title">Verified Properties</h6>
                        <p class="why-card__desc">Every hotel is verified for quality and accuracy</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="why-card">
                        <div class="why-card__icon"><i class="bi bi-currency-dollar"></i></div>
                        <h6 class="why-card__title">Best Price Guarantee</h6>
                        <p class="why-card__desc">Find a lower price? We'll match it</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="why-card">
                        <div class="why-card__icon"><span style="font-size:1.1rem;font-weight:800;">24/7</span></div>
                        <h6 class="why-card__title">24/7 Support</h6>
                        <p class="why-card__desc">Our team is here to help anytime you need</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="why-card">
                        <div class="why-card__icon"><i class="bi bi-patch-check-fill"></i></div>
                        <h6 class="why-card__title">Flexible Cancellation</h6>
                        <p class="why-card__desc">Plans change? Most bookings offer free cancellation</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/custom/hotels.js') }}"></script>
@endpush
