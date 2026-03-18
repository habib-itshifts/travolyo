@extends('layouts.master')

@php
    $directoryTabs = collect(data_get($topCitiesConfig ?? [], 'tabs', []))->values();
    $topCitiesTab = $directoryTabs->firstWhere('key', 'top_cities_to_book') ?? [];
    $topCityRegions = collect(data_get($topCitiesTab, 'regions', []))->values();
    $heroQuickCities = $topCityRegions
        ->flatMap(fn (array $region) => data_get($region, 'items', []))
        ->take(6)
        ->values();
@endphp

@section('title', 'Travolyo - Where Your Journey Takes Off')

@push('styles')
<style>
.top-cities-directory-wrap { margin-top: -48px; position: relative; z-index: 2; }
.top-cities-directory {
    background: #fff;
    border: 1px solid #e9eef5;
    border-radius: 28px;
    box-shadow: 0 24px 60px rgba(18, 38, 63, .08);
    padding: 28px 32px 34px;
}
.top-cities-directory__tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    border-bottom: 1px solid #e9eef5;
    padding-bottom: 16px;
    margin-bottom: 28px;
}
.top-cities-directory__tab {
    appearance: none;
    border: 0;
    border-bottom: 3px solid transparent;
    background: transparent;
    color: #697586;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 500;
    padding: 0 4px 14px;
    transition: color .2s ease, border-color .2s ease;
}
.top-cities-directory__tab:hover,
.top-cities-directory__tab.is-active {
    color: #17c3ce;
    border-color: #17c3ce;
}
.top-cities-directory__panel { display: none; }
.top-cities-directory__panel.is-active { display: block; }
.top-cities-directory__note {
    color: #5f6c7b;
    font-size: .95rem;
    margin: -6px 0 20px;
}
.top-cities-region + .top-cities-region { margin-top: 34px; }
.top-cities-region__title {
    color: #12314d;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: .12em;
    margin-bottom: 18px;
    text-transform: uppercase;
}
.top-cities-region__grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}
.top-cities-chip {
    background: #fff;
    border: 1px solid #dbe4ef;
    border-radius: 18px;
    color: #465568;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 54px;
    min-width: 104px;
    padding: 10px 22px;
    text-decoration: none;
    transition: transform .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
}
.top-cities-chip:hover {
    border-color: #17c3ce;
    box-shadow: 0 10px 24px rgba(23, 195, 206, .12);
    color: #17c3ce;
    transform: translateY(-1px);
}
.top-cities-placeholder {
    background: linear-gradient(180deg, #f8fbfd 0%, #f3f7fb 100%);
    border: 1px dashed #d8e3ee;
    border-radius: 20px;
    color: #5f6c7b;
    padding: 22px 24px;
}

@media (max-width: 991.98px) {
    .top-cities-directory-wrap { margin-top: 32px; }
    .top-cities-directory { padding: 24px 20px 26px; }
}

@media (max-width: 767.98px) {
    .top-cities-directory__tabs {
        gap: 8px;
        margin-bottom: 22px;
        overflow-x: auto;
        padding-bottom: 10px;
        scrollbar-width: none;
    }
    .top-cities-directory__tabs::-webkit-scrollbar { display: none; }
    .top-cities-directory__tab {
        border: 1px solid #dbe4ef;
        border-radius: 999px;
        flex: 0 0 auto;
        padding: 10px 14px;
        white-space: nowrap;
    }
    .top-cities-directory__tab.is-active {
        background: rgba(23, 195, 206, .08);
    }
    .top-cities-region__title {
        font-size: 1rem;
        letter-spacing: .1em;
        margin-bottom: 14px;
    }
    .top-cities-region__grid { gap: 10px; }
    .top-cities-chip {
        min-height: 48px;
        min-width: calc(50% - 5px);
        padding: 10px 16px;
    }
}
</style>
@endpush

@section('content')

<section class="hero-section d-flex flex-column justify-content-center">
    <div class="container text-center text-white">

        <h1 class="hero-title text-white mb-3">
            Explore the World, <br class="d-none d-md-block"> Your Way
        </h1>
        <p class="hero-subtitle text-white mb-5">
            Flights, hotels, activities &amp; more - all in one place.
        </p>

        @include('website.partials._search-widget', [
            'defaultHotelCheckIn' => $defaultHotelCheckIn,
            'defaultHotelCheckOut' => $defaultHotelCheckOut,
        ])

        @if($heroQuickCities->isNotEmpty())
            <div class="hero-quick-links mt-4">
                @foreach($heroQuickCities as $quickCity)
                    @php
                        $quickCityUrl = route('hotels.index') . '?' . http_build_query([
                            'country' => data_get($quickCity, 'country', ''),
                            'country_code' => data_get($quickCity, 'country_code', ''),
                            'city' => data_get($quickCity, 'city', data_get($quickCity, 'label', '')),
                            'location' => data_get($quickCity, 'location', ''),
                            'check_in' => $defaultHotelCheckIn,
                            'check_out' => $defaultHotelCheckOut,
                            'adults' => 1,
                            'children' => 0,
                        ]);
                    @endphp
                    <a href="{{ $quickCityUrl }}">{{ data_get($quickCity, 'label', data_get($quickCity, 'city', '')) }}</a>
                    @if(! $loop->last)
                        <span class="dot"></span>
                    @endif
                @endforeach
            </div>
        @endif

    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Featured Hotels</h2>
            <a href="{{ Route::has('hotels.index') ? route('hotels.index') : '#' }}" class="view-all-link">
                View all <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">

            <div class="col-6 col-md-4 col-lg-3">
                <div class="dest-card">
                    <div class="dest-card__img-wrap">
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80"
                             alt="Burj Al Arab" class="dest-card__img">
                        <span class="dest-card__badge badge--yellow">Best Seller</span>
                    </div>
                    <div class="dest-card__body">
                        <div class="dest-card__name">Burj Al Arab</div>
                        <div class="dest-card__location"><i class="bi bi-geo-alt me-1"></i>Dubai, UAE</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="dest-card__rating"><i class="bi bi-star-fill text-warning me-1"></i>4.9</span>
                            <span class="dest-card__price">$420<small class="text-muted fw-400" style="font-size:.75rem">/night</small></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="dest-card">
                    <div class="dest-card__img-wrap">
                        <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=80"
                             alt="Santorini Villa" class="dest-card__img">
                        <span class="dest-card__badge badge--green">Top Rated</span>
                    </div>
                    <div class="dest-card__body">
                        <div class="dest-card__name">Santorini Cliff Villa</div>
                        <div class="dest-card__location"><i class="bi bi-geo-alt me-1"></i>Santorini, Greece</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="dest-card__rating"><i class="bi bi-star-fill text-warning me-1"></i>4.8</span>
                            <span class="dest-card__price">$310<small class="text-muted fw-400" style="font-size:.75rem">/night</small></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="dest-card">
                    <div class="dest-card__img-wrap">
                        <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600&q=80"
                             alt="Maldives Resort" class="dest-card__img">
                        <span class="dest-card__badge badge--orange">Limited Deal</span>
                    </div>
                    <div class="dest-card__body">
                        <div class="dest-card__name">Maldives Water Bungalow</div>
                        <div class="dest-card__location"><i class="bi bi-geo-alt me-1"></i>Maldives</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="dest-card__rating"><i class="bi bi-star-fill text-warning me-1"></i>5.0</span>
                            <span class="dest-card__price">$680<small class="text-muted fw-400" style="font-size:.75rem">/night</small></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="dest-card">
                    <div class="dest-card__img-wrap">
                        <img src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600&q=80"
                             alt="Paris Hotel" class="dest-card__img">
                    </div>
                    <div class="dest-card__body">
                        <div class="dest-card__name">Le Grand Paris Hotel</div>
                        <div class="dest-card__location"><i class="bi bi-geo-alt me-1"></i>Paris, France</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="dest-card__rating"><i class="bi bi-star-fill text-warning me-1"></i>4.7</span>
                            <span class="dest-card__price">$195<small class="text-muted fw-400" style="font-size:.75rem">/night</small></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="promo-banner text-white text-center">
    <div class="container">
        <p class="text-uppercase fw-700 mb-2" style="letter-spacing:1.5px;opacity:.85;font-size:.85rem;">
            Limited Time Offer
        </p>
        <h2 class="mb-3">Get 20% Off Your First Booking</h2>
        <p class="opacity-90 mb-4">
            Sign up today and use code <strong>TRAVOLYO20</strong> at checkout to save on hotels &amp; flights.
        </p>
        <a href="{{ route('register') }}" class="btn btn-promo">
            Claim Your Discount
        </a>
    </div>
</section>

@if($directoryTabs->isNotEmpty())
<section class="top-cities-directory-wrap">
    <div class="container">
        <div class="top-cities-directory">
            <div class="top-cities-directory__tabs" role="tablist" aria-label="Travel directory">
                @foreach($directoryTabs as $tab)
                    <button
                        type="button"
                        class="top-cities-directory__tab {{ $loop->first ? 'is-active' : '' }}"
                        data-directory-tab="{{ data_get($tab, 'key') }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <i class="bi {{ data_get($tab, 'icon', 'bi-grid') }}"></i>
                        <span>{{ data_get($tab, 'label') }}</span>
                    </button>
                @endforeach
            </div>

            @foreach($directoryTabs as $tab)
                <div class="top-cities-directory__panel {{ $loop->first ? 'is-active' : '' }}" data-directory-panel="{{ data_get($tab, 'key') }}">
                    @if(data_get($tab, 'key') === 'top_cities_to_book')
                        <p class="top-cities-directory__note mb-0">
                            Choose any city and we will open the hotel listing page with your city plus the current search dates preselected.
                        </p>
                        @foreach(data_get($tab, 'regions', []) as $region)
                            <div class="top-cities-region">
                                <div class="top-cities-region__title">{{ data_get($region, 'label') }}</div>
                                <div class="top-cities-region__grid">
                                    @foreach(data_get($region, 'items', []) as $city)
                                        @php
                                            $cityUrl = route('hotels.index') . '?' . http_build_query([
                                                'country' => data_get($city, 'country', ''),
                                                'country_code' => data_get($city, 'country_code', ''),
                                                'city' => data_get($city, 'city', data_get($city, 'label', '')),
                                                'location' => data_get($city, 'location', ''),
                                                'check_in' => $defaultHotelCheckIn,
                                                'check_out' => $defaultHotelCheckOut,
                                                'adults' => 1,
                                                'children' => 0,
                                            ]);
                                        @endphp
                                        <a class="top-cities-chip" href="{{ $cityUrl }}">
                                            {{ data_get($city, 'label', data_get($city, 'city', '')) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="top-cities-placeholder">
                            {{ data_get($tab, 'description', 'This section is ready for the next content set.') }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Popular Destinations</h2>
            <a href="#" class="view-all-link">View all <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">

            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="city-card">
                    <img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80" alt="Dubai">
                    <div class="city-card__overlay">
                        <div>
                            <div class="city-card__name">Dubai</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.75rem;">United Arab Emirates</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="city-card">
                    <img src="https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&q=80" alt="Paris">
                    <div class="city-card__overlay">
                        <div>
                            <div class="city-card__name">Paris</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.75rem;">France</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="city-card">
                    <img src="https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&q=80" alt="Tokyo">
                    <div class="city-card__overlay">
                        <div>
                            <div class="city-card__name">Tokyo</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.75rem;">Japan</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="city-card">
                    <img src="https://images.unsplash.com/photo-1522083165195-3424ed129620?w=600&q=80" alt="New York">
                    <div class="city-card__overlay">
                        <div>
                            <div class="city-card__name">New York</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.75rem;">United States</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="city-card">
                    <img src="https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=600&q=80" alt="Bangkok">
                    <div class="city-card__overlay">
                        <div>
                            <div class="city-card__name">Bangkok</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.75rem;">Thailand</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="city-card">
                    <img src="https://images.unsplash.com/photo-1555993539-1732b0258235?w=600&q=80" alt="Bali">
                    <div class="city-card__overlay">
                        <div>
                            <div class="city-card__name">Bali</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.75rem;">Indonesia</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="city-card">
                    <img src="https://images.unsplash.com/photo-1533929736458-ca588d08c8be?w=600&q=80" alt="London">
                    <div class="city-card__overlay">
                        <div>
                            <div class="city-card__name">London</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.75rem;">United Kingdom</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="city-card">
                    <img src="https://images.unsplash.com/photo-1549180030-48bf079fb38a?w=600&q=80" alt="Santorini">
                    <div class="city-card__overlay">
                        <div>
                            <div class="city-card__name">Santorini</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.75rem;">Greece</div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

<section class="section-padding why-section" style="background:#f8f9fa;">
    <div class="container">
        <h2 class="section-title text-center mb-5">Why Book with Travolyo?</h2>
        <div class="row g-4">

            <div class="col-6 col-md-3">
                <div class="why-card">
                    <div class="why-card__icon" style="background:#17c3ce;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="why-card__title">Secure Booking</div>
                    <p class="why-card__desc">Your payments and personal data are always protected with industry-grade encryption.</p>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="why-card">
                    <div class="why-card__icon" style="background:#ffc107;">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div class="why-card__title">Best Price Guarantee</div>
                    <p class="why-card__desc">We compare thousands of deals so you always get the lowest price available.</p>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="why-card">
                    <div class="why-card__icon" style="background:#28a745;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="why-card__title">24/7 Support</div>
                    <p class="why-card__desc">Our travel experts are available around the clock to assist you wherever you are.</p>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="why-card">
                    <div class="why-card__icon" style="background:#6f42c1;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <div class="why-card__title">Free Cancellation</div>
                    <p class="why-card__desc">Plans change. Enjoy flexible cancellation on thousands of hotels and flights.</p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    const tabs = document.querySelectorAll('[data-directory-tab]');
    const panels = document.querySelectorAll('[data-directory-panel]');

    if (!tabs.length || !panels.length) {
        return;
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const key = tab.dataset.directoryTab || '';

            tabs.forEach((btn) => {
                const active = btn === tab;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach((panel) => {
                panel.classList.toggle('is-active', panel.dataset.directoryPanel === key);
            });
        });
    });
})();
</script>
@endpush
