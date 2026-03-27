@extends('layouts.master')

@php
    $directoryTabs = collect(data_get($topCitiesConfig ?? [], 'tabs', []))->values();
    $topCitiesTab = $directoryTabs->firstWhere('key', 'top_cities_to_book') ?? [];
    $topCityRegions = collect(data_get($topCitiesTab, 'regions', []))->values();
    $buildExploreHotelUrl = function (array $entry) use ($defaultHotelCheckIn, $defaultHotelCheckOut) {
        $isCountry = ($entry['type'] ?? 'city') === 'country';

        $query = $isCountry
            ? [
                'destination' => $entry['value'] ?? '',
                'country' => $entry['country'] ?? ($entry['value'] ?? ''),
                'country_code' => $entry['country_code'] ?? '',
                'check_in' => $defaultHotelCheckIn,
                'check_out' => $defaultHotelCheckOut,
                'adults' => 1,
                'children' => 0,
            ]
            : [
                'city' => $entry['value'] ?? '',
                'country' => $entry['country'] ?? '',
                'country_code' => $entry['country_code'] ?? '',
                'location' => $entry['location'] ?? '',
                'check_in' => $defaultHotelCheckIn,
                'check_out' => $defaultHotelCheckOut,
                'adults' => 1,
                'children' => 0,
            ];

        return route('hotels.index') . '?' . http_build_query(array_filter($query, fn ($value) => $value !== '' && $value !== null));
    };
    $exploreRegions = collect([
        [
            'label' => 'Asia',
            'icon' => 'bi-globe-central-south-asia',
            'items' => [
                ['label' => 'Bali Hotels', 'value' => 'Bali', 'country' => 'Indonesia', 'country_code' => 'ID', 'location' => 'DPS'],
                ['label' => 'Bangkok Hotels', 'value' => 'Bangkok', 'country' => 'Thailand', 'country_code' => 'TH', 'location' => 'BKK'],
                ['label' => 'Tokyo Hotels', 'value' => 'Tokyo', 'country' => 'Japan', 'country_code' => 'JP', 'location' => 'TYO'],
                ['label' => 'Seoul Hotels', 'value' => 'Seoul', 'country' => 'South Korea', 'country_code' => 'KR', 'location' => 'SEL'],
                ['label' => 'Phuket Hotels', 'value' => 'Phuket', 'country' => 'Thailand', 'country_code' => 'TH', 'location' => 'HKT'],
                ['label' => 'Singapore', 'value' => 'Singapore', 'country' => 'Singapore', 'country_code' => 'SG', 'location' => 'SIN'],
            ],
        ],
        [
            'label' => 'Europe',
            'icon' => 'bi-bank2',
            'items' => [
                ['label' => 'London Hotels', 'value' => 'London', 'country' => 'United Kingdom', 'country_code' => 'GB', 'location' => 'LON'],
                ['label' => 'Paris Hotels', 'value' => 'Paris', 'country' => 'France', 'country_code' => 'FR', 'location' => 'PAR'],
                ['label' => 'Rome Hotels', 'value' => 'Rome', 'country' => 'Italy', 'country_code' => 'IT', 'location' => 'ROM'],
                ['label' => 'Berlin Hotels', 'value' => 'Berlin', 'country' => 'Germany', 'country_code' => 'DE', 'location' => 'BER'],
                ['label' => 'Spain Hotels', 'value' => 'Spain', 'country' => 'Spain', 'country_code' => 'ES', 'type' => 'country'],
            ],
        ],
        [
            'label' => 'Middle East',
            'icon' => 'bi-brightness-high',
            'items' => [
                ['label' => 'Dubai Hotels', 'value' => 'Dubai', 'country' => 'UAE', 'country_code' => 'AE', 'location' => 'DXB'],
                ['label' => 'Saudi Hotels', 'value' => 'Saudi Arabia', 'country' => 'Saudi Arabia', 'country_code' => 'SA', 'type' => 'country'],
                ['label' => 'Egypt Hotels', 'value' => 'Egypt', 'country' => 'Egypt', 'country_code' => 'EG', 'type' => 'country'],
            ],
        ],
        [
            'label' => 'Americas',
            'icon' => 'bi-globe-americas',
            'items' => [
                ['label' => 'USA Hotels', 'value' => 'United States', 'country' => 'United States', 'country_code' => 'US', 'type' => 'country'],
                ['label' => 'Canada Hotels', 'value' => 'Canada', 'country' => 'Canada', 'country_code' => 'CA', 'type' => 'country'],
                ['label' => 'Brazil Hotels', 'value' => 'Brazil', 'country' => 'Brazil', 'country_code' => 'BR', 'type' => 'country'],
                ['label' => 'Mexico Hotels', 'value' => 'Mexico', 'country' => 'Mexico', 'country_code' => 'MX', 'type' => 'country'],
            ],
        ],
        [
            'label' => 'Americas',
            'icon' => 'bi-globe-americas',
            'items' => [
                ['label' => 'USA Hotels', 'value' => 'United States', 'country' => 'United States', 'country_code' => 'US', 'type' => 'country'],
                ['label' => 'Canada Hotels', 'value' => 'Canada', 'country' => 'Canada', 'country_code' => 'CA', 'type' => 'country'],
                ['label' => 'Brazil Hotels', 'value' => 'Brazil', 'country' => 'Brazil', 'country_code' => 'BR', 'type' => 'country'],
            ],
        ],
        [
            'label' => 'Africa',
            'icon' => 'bi-globe2',
            'items' => [
                ['label' => 'Morocco Hotels', 'value' => 'Morocco', 'country' => 'Morocco', 'country_code' => 'MA', 'type' => 'country'],
                ['label' => 'South Africa Hotels', 'value' => 'South Africa', 'country' => 'South Africa', 'country_code' => 'ZA', 'type' => 'country'],
            ],
        ],
        [
            'label' => 'Oceania',
            'icon' => 'bi-water',
            'items' => [
                ['label' => 'Australia Hotels', 'value' => 'Australia', 'country' => 'Australia', 'country_code' => 'AU', 'type' => 'country'],
                ['label' => 'New Zealand Hotels', 'value' => 'New Zealand', 'country' => 'New Zealand', 'country_code' => 'NZ', 'type' => 'country'],
            ],
        ],
    ]);
    $heroQuickCities = $topCityRegions
        ->flatMap(fn (array $region) => data_get($region, 'items', []))
        ->take(6)
        ->values();
@endphp

@section('title', 'Travolyo - Where Your Journey Takes Off')

@push('styles')
<style>
.hero-section {
    background: #f7f8fb;
    min-height: auto;
    overflow: visible;
    padding: 26px 0 96px;
    position: relative;
    z-index: 12;
}
.hero-panel {
    background: url('{{ asset('assets/images/website/background-image.png') }}') center/cover no-repeat;
    border-radius: 28px;
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.24),
        0 22px 44px rgba(18, 38, 63, 0.08);
    min-height: 455px;
    overflow: visible;
    padding: 34px 0 86px;
    position: relative;
    z-index: 12;
}
.hero-panel::before {
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02) 38%, rgba(255, 255, 255, 0.10) 100%);
    border: 1px solid rgba(255, 255, 255, 0.42);
    border-radius: inherit;
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.10),
        inset 0 -12px 26px rgba(255, 255, 255, 0.08);
    content: "";
    inset: 0;
    pointer-events: none;
    position: absolute;
}
.hero-panel::after {
    background: none;
    content: "";
    inset: 0;
    pointer-events: none;
    position: absolute;
}
.hero-panel .hero-mountain {
    display: none;
}
.hero-panel .hero-mountain--left {
    background: linear-gradient(180deg, rgba(255,255,255,0.14), rgba(255,255,255,0.02));
    clip-path: polygon(0 100%, 14% 76%, 28% 66%, 42% 48%, 56% 57%, 72% 36%, 100% 100%);
    height: 228px;
    left: 0;
    width: 32%;
}
.hero-panel .hero-mountain--right {
    background: linear-gradient(180deg, rgba(255,255,255,0.14), rgba(255,255,255,0.02));
    clip-path: polygon(0 100%, 22% 58%, 40% 40%, 56% 54%, 76% 30%, 100% 100%);
    height: 214px;
    right: 0;
    width: 34%;
}
.hero-shell {
    position: relative;
    z-index: 1;
}
.hero-copy {
    margin: 0 auto 10px;
    max-width: 620px;
}
.hero-title {
    font-size: clamp(2.05rem, 3.2vw, 2.65rem);
    font-weight: 800;
    letter-spacing: -0.04em;
    margin-bottom: 10px;
    text-shadow: 0 10px 28px rgba(3, 45, 72, 0.18);
}
.hero-trustbar {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 12px 24px;
    justify-content: center;
    margin-bottom: 16px;
}
.hero-trustbar__item {
    align-items: center;
    color: rgba(255, 255, 255, 0.92);
    display: inline-flex;
    font-size: 0.83rem;
    font-weight: 600;
    gap: 8px;
}
.hero-trustbar__item i {
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 999px;
    display: inline-flex;
    font-size: .72rem;
    height: 20px;
    justify-content: center;
    width: 20px;
}
.hero-quick-links {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-top: 22px;
}
.hero-quick-links a {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 999px;
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 600;
    padding: 10px 16px;
    text-decoration: none;
    transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease;
}
.hero-quick-links a:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.34);
    transform: translateY(-1px);
}
.hero-quick-links .dot { display: none; }
.top-cities-directory-wrap {
    padding: 6px 0 34px;
    position: relative;
}
.top-cities-directory {
    background:
        radial-gradient(circle at 50% 100%, rgba(164, 211, 246, 0.30), rgba(164, 211, 246, 0) 34%),
        linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
    border: 1px solid #edf2f8;
    border-radius: 26px;
    box-shadow: 0 18px 44px rgba(18, 38, 63, .06);
    margin: 0 auto;
    max-width: 1320px;
    padding: 26px 28px 38px;
}
.top-cities-directory__heading {
    color: #374151;
    font-size: clamp(1.8rem, 2.6vw, 2.15rem);
    font-weight: 800;
    margin: 0 0 26px;
    text-align: center;
}
.top-cities-directory__rows {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.top-cities-directory__row {
    display: grid;
    gap: 14px;
    justify-content: center;
}
.top-cities-directory__row--wide {
    grid-template-columns: repeat(2, minmax(0, 330px));
}
.top-cities-directory__row--top .top-cities-region__grid {
    display: grid;
    gap: 8px;
    grid-template-columns: repeat(3, minmax(0, 1fr));
}
.top-cities-directory__row--top .top-cities-chip {
    width: 100%;
}
.top-cities-directory__row--middle {
    grid-template-columns: repeat(3, minmax(0, 245px));
}
.top-cities-directory__row--middle .top-cities-region__grid {
    display: grid;
    gap: 8px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}
.top-cities-region {
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid #eef3f9;
    box-sizing: border-box;
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(24, 55, 90, 0.08);
    padding: 16px 16px 14px;
    width: 100%;
}
.top-cities-region__title {
    align-items: center;
    color: #2f3f55;
    display: inline-flex;
    font-size: 1.1rem;
    font-weight: 700;
    gap: 9px;
    margin-bottom: 14px;
}
.top-cities-region__title i {
    align-items: center;
    background: linear-gradient(135deg, #d8efff 0%, #edf8ff 100%);
    border-radius: 999px;
    color: #28a6cb;
    display: inline-flex;
    font-size: 0.8rem;
    height: 22px;
    justify-content: center;
    width: 22px;
}
.top-cities-region__grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: flex-start;
}
.top-cities-chip {
    background: #f3f6fb;
    border: 1px solid #edf2f7;
    border-radius: 8px;
    color: #607085;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.84rem;
    font-weight: 600;
    line-height: 1;
    min-height: 34px;
    padding: 0 12px;
    white-space: nowrap;
    text-decoration: none;
    transition: transform .15s ease, border-color .15s ease, background .15s ease, color .15s ease;
}
.top-cities-chip:hover {
    background: #ffffff;
    border-color: #bfe0ef;
    color: #2293bb;
    transform: translateY(-1px);
}
.top-cities-placeholder {
    background: rgba(255, 255, 255, 0.88);
    border: 1px dashed #d8e3ee;
    border-radius: 18px;
    color: #5f6c7b;
    padding: 18px 20px;
}

@media (max-width: 991.98px) {
    .hero-section { padding-bottom: 82px; }
    .hero-panel { min-height: 430px; padding-bottom: 84px; }
    .hero-panel .hero-mountain { bottom: 64px; }
    .top-cities-directory-wrap { padding-top: 0; }
    .top-cities-directory {
        padding: 22px 18px 24px;
    }
    .top-cities-directory__row--wide,
    .top-cities-directory__row--middle {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .top-cities-directory__row--top .top-cities-region__grid {
        display: flex;
    }
    .top-cities-directory__row--middle .top-cities-region__grid {
        display: flex;
    }
    .top-cities-directory__row {
        justify-content: stretch;
    }
}

@media (max-width: 767.98px) {
    .hero-section { padding: 20px 0 62px; overflow: hidden; }
    .hero-panel { border-radius: 24px; min-height: auto; overflow: hidden; padding: 28px 0 30px; }
    .hero-panel .hero-mountain { display: none; }
    .hero-copy { max-width: 100%; margin-bottom: 8px; }
    .hero-trustbar { gap: 10px 14px; justify-content: center; margin-bottom: 16px; }
    .hero-trustbar__item { font-size: 0.78rem; gap: 6px; }
    .hero-trustbar__item i { height: 22px; width: 22px; }
    .hero-quick-links { gap: 8px; }
    .hero-quick-links a { font-size: 0.84rem; padding: 8px 12px; }
    .top-cities-directory {
        border-radius: 22px;
        padding: 20px 14px 18px;
    }
    .top-cities-directory__heading {
        font-size: 1.45rem;
        margin-bottom: 16px;
    }
    .top-cities-directory__row--wide,
    .top-cities-directory__row--middle {
        grid-template-columns: minmax(0, 1fr);
    }
    .top-cities-directory__row--top .top-cities-region__grid {
        display: flex;
    }
    .top-cities-directory__row--middle .top-cities-region__grid {
        display: flex;
    }
    .top-cities-region__title {
        font-size: 1rem;
        margin-bottom: 10px;
    }
    .top-cities-region__grid { gap: 8px; }
    .top-cities-chip {
        font-size: 0.8rem;
        min-height: 32px;
        min-width: 0;
        padding: 0 10px;
    }
}
</style>
@endpush

@section('content')

<section class="hero-section d-flex flex-column justify-content-center">
    <div class="container">
        <div class="hero-panel">
            <div class="text-center text-white hero-shell">
                <div class="hero-copy">
                    <h1 class="hero-title text-white">Your Trip Starts Here</h1>
                    <div class="hero-trustbar">
                        <span class="hero-trustbar__item"><i class="bi bi-check2"></i>Secure payment</span>
                        <span class="hero-trustbar__item"><i class="bi bi-headset"></i>Support in approx. 30s</span>
                    </div>
                </div>

                @include('website.partials._search-widget', [
                    'defaultHotelCheckIn' => $defaultHotelCheckIn,
                    'defaultHotelCheckOut' => $defaultHotelCheckOut,
                    'widgetVariant' => 'hero',
                ])
            </div>
        </div>
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

@if($exploreRegions->isNotEmpty())
<section class="top-cities-directory-wrap">
    <div class="container">
        <div class="top-cities-directory">
            <h2 class="top-cities-directory__heading">Explore Destinations</h2>
            @php
                $exploreTopRow = $exploreRegions->take(2);
                $exploreMiddleRow = $exploreRegions->slice(2, 3);
                $exploreBottomRow = $exploreRegions->slice(5, 2);
            @endphp
            <div class="top-cities-directory__rows">
                <div class="top-cities-directory__row top-cities-directory__row--wide top-cities-directory__row--top">
                    @foreach($exploreTopRow as $region)
                        <div class="top-cities-region">
                            <div class="top-cities-region__title">
                                <i class="bi {{ data_get($region, 'icon', 'bi-globe2') }}"></i>
                                <span>{{ data_get($region, 'label') }}</span>
                            </div>
                            <div class="top-cities-region__grid">
                                @foreach(data_get($region, 'items', []) as $entry)
                                    <a class="top-cities-chip" href="{{ $buildExploreHotelUrl($entry) }}">{{ data_get($entry, 'label') }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="top-cities-directory__row top-cities-directory__row--middle">
                    @foreach($exploreMiddleRow as $region)
                        <div class="top-cities-region">
                            <div class="top-cities-region__title">
                                <i class="bi {{ data_get($region, 'icon', 'bi-globe2') }}"></i>
                                <span>{{ data_get($region, 'label') }}</span>
                            </div>
                            <div class="top-cities-region__grid">
                                @foreach(data_get($region, 'items', []) as $entry)
                                    <a class="top-cities-chip" href="{{ $buildExploreHotelUrl($entry) }}">{{ data_get($entry, 'label') }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="top-cities-directory__row top-cities-directory__row--wide">
                    @foreach($exploreBottomRow as $region)
                        <div class="top-cities-region">
                            <div class="top-cities-region__title">
                                <i class="bi {{ data_get($region, 'icon', 'bi-globe2') }}"></i>
                                <span>{{ data_get($region, 'label') }}</span>
                            </div>
                            <div class="top-cities-region__grid">
                                @foreach(data_get($region, 'items', []) as $entry)
                                    <a class="top-cities-chip" href="{{ $buildExploreHotelUrl($entry) }}">{{ data_get($entry, 'label') }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@endsection
