@extends('layouts.master')

@section('canonical', 'https://www.travolyo.com/')

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Travolyo",
  "url": "https://www.travolyo.com",
  "logo": "https://www.travolyo.com/assets/logo/travolyo-logo.svg",
  "description": "Travolyo helps users book flights, hotels, and travel activities worldwide.",
  "foundingDate": "2025",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+97142296659",
    "contactType": "customer service",
    "email": "support@travolyo.com",
    "areaServed": "Worldwide",
    "availableLanguage": ["English"]
  },
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Glass Building - Office 201, Al Maktoum Rd, next to Khalidiya Palace Hotel",
    "addressLocality": "Al Muraqqabat",
    "addressRegion": "Dubai",
    "addressCountry": "United Arab Emirates"
  }
}
</script>
@endpush

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
    $uaeDestinationCards = collect($uaeDestinationCards ?? []);
    $accommodationPromotions = [
        ['image' => asset('assets/images/website/accomodaion-pomotion/image-1.png'), 'alt' => 'Luxury hotel resort'],
        ['image' => asset('assets/images/website/accomodaion-pomotion/image-2.png'), 'alt' => 'Resort pool and stay offer'],
        ['image' => asset('assets/images/website/accomodaion-pomotion/image-3.png'), 'alt' => 'City hotel promotion'],
    ];
    $flightActivityPromotions = [
        ['image' => asset('assets/images/website/accomodaion-pomotion/image-4.png'), 'alt' => 'Airplane travel promotion'],
        ['image' => asset('assets/images/website/accomodaion-pomotion/image-5.png'), 'alt' => 'Adventure activities promotion'],
    ];
    $inspireTripCards = [
        ['title' => 'Anywhere', 'badge' => null, 'image' => asset('assets/images/website/inspired-trip/inpired-trip-image-1.avif'), 'active' => false],
        ['title' => 'Doha', 'badge' => 'Short haul', 'image' => asset('assets/images/website/inspired-trip/inpired-trip-image-2.avif'), 'active' => true],
        ['title' => 'Tbilisi', 'badge' => 'Medium haul', 'image' => asset('assets/images/website/inspired-trip/inpired-trip-image-3.avif'), 'active' => false],
        ['title' => 'Accra', 'badge' => 'Long haul', 'image' => asset('assets/images/website/inspired-trip/inpired-trip-image-4.avif'), 'active' => false],
    ];
    $inspireDohaSights = [
        ['title' => 'Museum of Islamic Art', 'location' => 'Doha', 'score' => '7.7', 'rating' => '4.5', 'reviews' => '186', 'image' => asset('assets/images/website/inspired-trip/inpired-trip-image-5.avif')],
        ['title' => 'National Museum of Qatar', 'location' => 'Doha', 'score' => '7.3', 'rating' => '4.6', 'reviews' => '91', 'image' => asset('assets/images/website/inspired-trip/inpired-trip-image-6.avif')],
        ['title' => 'Souq Waqif', 'location' => 'Doha', 'score' => '6.7', 'rating' => '4.5', 'reviews' => '31', 'image' => asset('assets/images/website/inspired-trip/inpired-trip-image-7.avif')],
        ['title' => 'Katara Cultural Village', 'location' => 'Doha', 'score' => '6.3', 'rating' => '3.9', 'reviews' => '66', 'image' => asset('assets/images/website/inspired-trip/inpired-trip-image-8.avif')],
    ];
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

.uae-destination-section {
    padding: 18px 0 12px;
}

.uae-destination-panel {
    background:
        radial-gradient(circle at 0 50%, rgba(59, 130, 246, 0.12), rgba(59, 130, 246, 0) 28%),
        radial-gradient(circle at 100% 50%, rgba(56, 189, 248, 0.16), rgba(56, 189, 248, 0) 30%),
        #ffffff;
    border-radius: 34px;
    box-shadow: 0 26px 60px rgba(15, 54, 88, 0.09);
    min-height: 600px;
    padding: 26px 36px 30px;
    position: relative;
}

.uae-destination-header {
    align-items: center;
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 18px;
    text-align: center;
}

.uae-destination-pin {
    align-items: center;
    display: inline-flex;
    justify-content: center;
}

.uae-destination-pin img {
    filter: drop-shadow(0 10px 16px rgba(37, 99, 235, 0.18));
    height: 48px;
    width: 48px;
}

.uae-destination-title {
    color: #27496d;
    font-size: clamp(1.9rem, 2.7vw, 3rem);
    font-weight: 400;
    letter-spacing: -0.035em;
    line-height: 1.08;
    margin: 0;
}

.uae-destination-slider {
    margin: 0 auto;
    max-width: 1110px;
    padding: 0 18px;
    position: relative;
}

.uae-destination-viewport {
    overflow-x: auto;
    padding: 4px 0 10px;
    scroll-behavior: smooth;
    scrollbar-width: none;
}

.uae-destination-viewport::-webkit-scrollbar {
    display: none;
}

.uae-destination-track {
    display: flex;
    gap: 14px;
    margin: 0;
    min-width: 100%;
    width: max-content;
}

.uae-destination-track.row {
    flex-wrap: nowrap;
    margin-left: 0;
    margin-right: 0;
}

.uae-destination-track > [class*='col-'] {
    flex: 0 0 192px;
    max-width: 192px;
    padding-left: 0;
    padding-right: 0;
}

.uae-destination-card {
    background: linear-gradient(180deg, #ffffff 0%, #f3f8ff 100%);
    border-radius: 18px;
    box-shadow: 0 16px 34px rgba(25, 74, 116, 0.11);
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 380px;
    overflow: hidden;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    width: 100%;
}

.uae-destination-card:hover {
    box-shadow: 0 24px 42px rgba(25, 74, 116, 0.18);
    transform: translateY(-4px);
}

.uae-destination-card__image {
    aspect-ratio: 4 / 5;
    border-radius: 18px 18px 14px 14px;
    flex: 0 0 auto;
    overflow: hidden;
    height: auto;
    margin: 0;
}

.uae-destination-card__image img {
    display: block;
    height: 100%;
    object-fit: cover;
    object-position: center center;
    width: 100%;
}

.uae-destination-card__body {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    justify-content: flex-start;
    min-height: 120px;
    padding: 16px 13px 20px;
}

.uae-destination-card__title {
    color: #27496d;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: -0.03em;
    line-height: 1.08;
    margin: 0 0 6px;
    min-height: 2.25em;
}

.uae-destination-card__meta {
    color: #7c91a8;
    font-size: 0.76rem;
    line-height: 1.35;
    margin: 0;
}

.uae-destination-arrow {
    align-items: center;
    background: linear-gradient(135deg, #2dd4f1 0%, #1d9bf0 100%);
    border: 0;
    border-radius: 999px;
    box-shadow: 0 16px 32px rgba(29, 155, 240, 0.28);
    color: #ffffff;
    display: inline-flex;
    height: 46px;
    justify-content: center;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 46px;
    z-index: 2;
}

.uae-destination-arrow--left {
    left: 2px;
}

.uae-destination-arrow--right {
    right: 2px;
}

.uae-destination-arrow:disabled {
    box-shadow: none;
    cursor: default;
    opacity: 0.38;
}

.uae-destination-arrow.is-hidden {
    opacity: 0;
    pointer-events: none;
}

.uae-destination-arrow i {
    font-size: 1rem;
}

.promotion-static-section {
    padding: 18px 0 28px;
}

.promotion-static-panel {
    background: linear-gradient(180deg, #fbfdff 0%, #eef6ff 100%);
    border: 1px solid #e3edf8;
    border-radius: 34px;
    box-shadow: 0 26px 60px rgba(15, 54, 88, 0.09);
    min-height: 500px;
    overflow: hidden;
    padding: 30px 32px 36px;
    position: relative;
}

.promotion-static-panel::before {
    content: "";
    display: none;
    pointer-events: none;
    position: absolute;
}

.promotion-static-cloud {
    height: auto;
    opacity: 0.82;
    pointer-events: none;
    position: absolute;
    width: auto;
    z-index: 1;
}

.promotion-static-cloud--top {
    left: 26px;
    top: 10px;
    width: 200px;
}

.promotion-static-cloud--middle {
    left: 50%;
    top: 52%;
    transform: translate(-50%, -50%);
    width: 256px;
}

.promotion-static-cloud--bottom {
    bottom: 12px;
    left: 20px;
    width: 210px;
}

.promotion-static-content {
    position: relative;
    z-index: 2;
}

.promotion-static-block + .promotion-static-block {
    margin-top: 26px;
}

.promotion-static-head {
    align-items: center;
    display: flex;
    justify-content: space-between;
    margin-bottom: 14px;
}

.promotion-static-title {
    color: #1f3658;
    font-size: clamp(1.55rem, 2.1vw, 2.15rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
    margin: 0;
}

.promotion-static-view {
    align-items: center;
    color: #2b65da;
    display: inline-flex;
    font-size: 1rem;
    font-weight: 600;
    gap: 6px;
    text-decoration: none;
}

.promotion-static-view:hover {
    color: #1749b4;
}

.promotion-static-row {
    padding-right: 0;
    position: relative;
}

.promotion-static-grid {
    display: grid;
    gap: 14px;
}

.promotion-static-grid--three {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.promotion-static-grid--two {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.promotion-static-card {
    border-radius: 16px;
    box-shadow: 0 14px 30px rgba(17, 53, 90, 0.14);
    display: block;
    margin: 0;
    overflow: hidden;
    aspect-ratio: 16 / 9;
}

.promotion-static-card img {
    display: block;
    height: 100%;
    /* object-fit: cover; */
    object-position: center center;
    width: 100%;
}

.promotion-static-grid--three .promotion-static-card {
    min-height: 146px;
}

.promotion-static-grid--two .promotion-static-card {
    min-height: 146px;
}

.promotion-static-arrow {
    align-items: center;
    background: linear-gradient(135deg, #2dd4f1 0%, #1d9bf0 100%);
    border: 0;
    border-radius: 999px;
    box-shadow: 0 14px 28px rgba(29, 155, 240, 0.26);
    color: #ffffff;
    display: inline-flex;
    height: 50px;
    justify-content: center;
    position: absolute;
    right: -20px;
    top: auto;
    bottom: 12px;
    transform: none;
    width: 50px;
    z-index: 3;
}

.promotion-static-arrow i {
    font-size: 1rem;
}

.inspire-static-section {
    padding: 6px 0 20px;
}

.inspire-static-panel {
    background: linear-gradient(180deg, #fbfdff 0%, #eef6ff 100%);
    border: 1px solid #e9f1fb;
    border-radius: 28px;
    box-shadow: 0 20px 48px rgba(20, 54, 88, 0.07);
    overflow: hidden;
    padding: 24px 20px 24px;
    position: relative;
}

.inspire-static-cloud {
    height: auto;
    opacity: 0.82;
    pointer-events: none;
    position: absolute;
    width: auto;
    z-index: 1;
}

.inspire-static-cloud--top {
    left: 20px;
    top: 8px;
    width: 176px;
}

.inspire-static-cloud--middle {
    left: 50%;
    top: 56%;
    transform: translate(-50%, -50%);
    width: 238px;
}

.inspire-static-cloud--bottom {
    bottom: 10px;
    left: 18px;
    width: 182px;
}

.inspire-static-content {
    position: relative;
    z-index: 2;
}

.inspire-static-heading {
    color: #253f63;
    font-size: clamp(1.6rem, 2.3vw, 2.2rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    margin: 0 0 16px;
}

.inspire-trip-row {
    padding-right: 34px;
    position: relative;
}

.inspire-trip-grid {
    display: grid;
    gap: 8px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin-bottom: 20px;
}

.inspire-trip-card {
    border-radius: 12px;
    box-shadow: 0 12px 26px rgba(21, 57, 90, 0.14);
    display: block;
    aspect-ratio: 1.9 / 1;
    min-height: 0;
    overflow: hidden;
    position: relative;
    text-decoration: none;
}

.inspire-trip-card img {
    display: block;
    height: 100%;
    object-fit: cover;
    width: 100%;
}

.inspire-trip-card::after {
    background: linear-gradient(180deg, rgba(13, 28, 52, 0) 30%, rgba(13, 28, 52, 0.72) 100%);
    content: "";
    inset: 0;
    position: absolute;
}

.inspire-trip-card__badge {
    background: rgba(255, 255, 255, 0.88);
    border-radius: 6px;
    color: #3f4e61;
    font-size: 0.65rem;
    font-weight: 600;
    left: 8px;
    line-height: 1;
    padding: 4px 7px;
    position: absolute;
    top: 8px;
    z-index: 2;
}

.inspire-trip-card__title {
    bottom: 8px;
    color: #ffffff;
    font-size: 1rem;
    font-weight: 700;
    left: 8px;
    letter-spacing: -0.02em;
    line-height: 1;
    margin: 0;
    position: absolute;
    z-index: 2;
}

.inspire-trip-card.is-active {
    border: 2px solid #5f88e8;
    box-shadow: 0 14px 30px rgba(47, 91, 197, 0.2);
}

.inspire-trip-card.is-active::before {
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 10px solid #5f88e8;
    bottom: -10px;
    content: "";
    left: 50%;
    position: absolute;
    transform: translateX(-50%);
    z-index: 3;
}

.inspire-trip-arrow {
    align-items: center;
    background: #ffffff;
    border: 1px solid rgba(36, 126, 237, 0.2);
    border-radius: 999px;
    box-shadow: 0 10px 22px rgba(33, 118, 222, 0.22);
    color: #1d9bf0;
    display: inline-flex;
    height: 42px;
    justify-content: center;
    position: absolute;
    right: -2px;
    top: 50%;
    transform: translateY(-50%);
    width: 42px;
}

.inspire-trip-arrow i {
    font-size: 1rem;
}

.inspire-sights-row {
    padding-right: 34px;
    position: relative;
}

.inspire-sights-arrow {
    align-items: center;
    background: #ffffff;
    border: 1px solid rgba(36, 126, 237, 0.2);
    border-radius: 999px;
    box-shadow: 0 10px 22px rgba(33, 118, 222, 0.22);
    color: #1d9bf0;
    display: inline-flex;
    height: 42px;
    justify-content: center;
    position: absolute;
    right: -2px;
    top: 50%;
    transform: translateY(-50%);
    width: 42px;
}

.inspire-sights-arrow i {
    font-size: 1rem;
}

.inspire-sights-head {
    align-items: center;
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
}

.inspire-sights-title {
    color: #223a5d;
    font-size: clamp(1.2rem, 1.9vw, 1.8rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    margin: 0;
}

.inspire-sights-more {
    align-items: center;
    color: #2e65d8;
    display: inline-flex;
    font-size: 0.95rem;
    font-weight: 600;
    gap: 6px;
    text-decoration: none;
}

.inspire-sights-grid {
    display: grid;
    gap: 8px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.inspire-sight-card {
    border-radius: 12px;
    box-shadow: 0 14px 28px rgba(21, 57, 90, 0.16);
    color: #ffffff;
    aspect-ratio: 0.72 / 1;
    min-height: 0;
    overflow: hidden;
    position: relative;
}

.inspire-sight-card img {
    display: block;
    height: 100%;
    object-fit: cover;
    width: 100%;
}

.inspire-sight-card::after {
    background: linear-gradient(180deg, rgba(8, 17, 34, 0) 36%, rgba(8, 17, 34, 0.82) 100%);
    content: "";
    inset: 0;
    position: absolute;
}

.inspire-sight-tag {
    background: rgba(245, 248, 255, 0.94);
    border-radius: 6px;
    color: #2b3850;
    font-size: 0.66rem;
    font-weight: 700;
    left: 8px;
    line-height: 1;
    padding: 4px 7px;
    position: absolute;
    top: 8px;
    z-index: 2;
}

.inspire-sight-heart {
    align-items: center;
    background: rgba(245, 248, 255, 0.94);
    border-radius: 999px;
    color: #6d7b91;
    display: inline-flex;
    height: 26px;
    justify-content: center;
    position: absolute;
    right: 8px;
    top: 8px;
    width: 26px;
    z-index: 2;
}

.inspire-sight-body {
    bottom: 10px;
    left: 8px;
    position: absolute;
    right: 8px;
    z-index: 2;
}

.inspire-sight-location {
    align-items: center;
    display: inline-flex;
    font-size: 0.72rem;
    gap: 5px;
    margin-bottom: 4px;
}

.inspire-sight-score {
    background: rgba(255, 240, 240, 0.94);
    border-radius: 4px;
    color: #a83b3b;
    font-size: 0.65rem;
    font-weight: 700;
    line-height: 1;
    padding: 3px 4px;
}

.inspire-sight-name {
    color: #ffffff;
    font-size: 0.92rem;
    font-weight: 700;
    line-height: 1.1;
    margin: 0 0 4px;
}

.inspire-sight-meta {
    color: rgba(236, 243, 255, 0.92);
    font-size: 0.72rem;
    margin: 0 0 4px;
}

.inspire-sight-stars {
    color: #ffd84a;
    display: inline-flex;
    font-size: 0.72rem;
    gap: 2px;
}

@media (min-width: 992px) and (max-width: 1399.98px) {
    .uae-destination-slider {
        max-width: 1088px;
        padding: 0 34px;
    }
    .uae-destination-track {
        gap: 10px;
    }
    .uae-destination-track > [class*='col-'] {
        flex: 0 0 182px;
        max-width: 182px;
    }
}

@media (max-width: 1199.98px) {
    .uae-destination-slider {
        max-width: 1038px;
    }
    .uae-destination-track {
        gap: 10px;
    }
    .uae-destination-track > [class*='col-'] {
        flex: 0 0 180px;
        max-width: 180px;
    }
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
    .uae-destination-panel {
        padding: 24px 22px 24px;
    }
    .uae-destination-title {
        font-size: 2.15rem;
    }
    .uae-destination-slider {
        max-width: 720px;
        padding: 0 24px;
    }
    .uae-destination-track > [class*='col-'] {
        flex: 0 0 206px;
        max-width: 206px;
    }
    .promotion-static-panel {
        min-height: 0;
        padding: 26px 22px 30px;
    }
    .promotion-static-cloud--top {
        left: 18px;
        top: 8px;
        width: 158px;
    }
    .promotion-static-cloud--middle {
        width: 210px;
    }
    .promotion-static-cloud--bottom {
        left: 14px;
        width: 168px;
    }
    .promotion-static-title {
        font-size: 1.5rem;
    }
    .promotion-static-row {
        padding-right: 0;
    }
    .promotion-static-grid {
        gap: 12px;
    }
    .promotion-static-grid--three {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .promotion-static-grid--three .promotion-static-card,
    .promotion-static-grid--two .promotion-static-card {
        min-height: 146px;
    }
    .promotion-static-arrow {
        height: 44px;
        right: -16px;
        top: auto;
        bottom: 10px;
        transform: none;
        width: 44px;
    }
    .inspire-static-panel {
        padding: 20px 18px 22px;
    }
    .inspire-static-cloud--top {
        left: 14px;
        top: 6px;
        width: 142px;
    }
    .inspire-static-cloud--middle {
        width: 192px;
    }
    .inspire-static-cloud--bottom {
        left: 12px;
        width: 148px;
    }
    .inspire-trip-row {
        padding-right: 40px;
    }
    .inspire-trip-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .inspire-trip-card {
        aspect-ratio: 1.75 / 1;
        min-height: 0;
    }
    .inspire-trip-card__title {
        font-size: 0.94rem;
    }
    .inspire-trip-arrow,
    .inspire-sights-arrow {
        height: 40px;
        width: 40px;
    }
    .inspire-sights-row {
        padding-right: 40px;
    }
    .inspire-sights-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .inspire-sight-card {
        aspect-ratio: 0.75 / 1;
        min-height: 0;
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
    .uae-destination-section {
        padding-top: 6px;
    }
    .uae-destination-panel {
        border-radius: 26px;
        padding: 22px 16px 20px;
    }
    .uae-destination-header {
        gap: 8px;
        margin-bottom: 16px;
    }
    .uae-destination-pin img {
        height: 40px;
        width: 40px;
    }
    .uae-destination-title {
        font-size: 1.55rem;
        line-height: 1.12;
    }
    .uae-destination-slider {
        padding: 0 20px;
    }
    .uae-destination-track > [class*='col-'] {
        flex: 0 0 232px;
        max-width: 232px;
    }
    .uae-destination-card__title {
        font-size: 0.96rem;
    }
    .uae-destination-card__meta {
        font-size: 0.7rem;
    }
    .uae-destination-card__body {
        min-height: 102px;
        padding: 14px 10px 16px;
    }
    .uae-destination-arrow {
        height: 38px;
        width: 38px;
    }
    .uae-destination-arrow--left {
        left: -2px;
    }
    .uae-destination-arrow--right {
        right: -2px;
    }
    .promotion-static-panel {
        border-radius: 26px;
        padding: 20px 14px 20px;
    }
    .promotion-static-cloud--top {
        left: 14px;
        top: 8px;
        width: 120px;
    }
    .promotion-static-cloud--middle {
        top: 52%;
        width: 170px;
    }
    .promotion-static-cloud--bottom {
        left: 10px;
        width: 124px;
    }
    .promotion-static-block + .promotion-static-block {
        margin-top: 16px;
    }
    .promotion-static-title {
        font-size: 1.22rem;
    }
    .promotion-static-view {
        font-size: 0.82rem;
    }
    .promotion-static-row {
        padding-right: 0;
    }
    .promotion-static-grid {
        gap: 10px;
    }
    .promotion-static-grid--three,
    .promotion-static-grid--two {
        grid-template-columns: minmax(0, 1fr);
    }
    .promotion-static-grid--three .promotion-static-card,
    .promotion-static-grid--two .promotion-static-card {
        min-height: 132px;
    }
    .promotion-static-arrow {
        height: 36px;
        right: -12px;
        top: auto;
        bottom: 8px;
        transform: none;
        width: 36px;
    }
    .inspire-static-section {
        padding: 6px 0 14px;
    }
    .inspire-static-panel {
        border-radius: 22px;
        padding: 16px 12px 18px;
    }
    .inspire-static-cloud--top {
        left: 10px;
        top: 6px;
        width: 112px;
    }
    .inspire-static-cloud--middle {
        width: 148px;
    }
    .inspire-static-cloud--bottom {
        left: 8px;
        width: 114px;
    }
    .inspire-static-heading {
        font-size: 1.3rem;
        margin-bottom: 12px;
    }
    .inspire-trip-row {
        padding-right: 30px;
    }
    .inspire-trip-grid {
        gap: 8px;
        margin-bottom: 14px;
    }
    .inspire-trip-card {
        border-radius: 10px;
        aspect-ratio: 1.7 / 1;
        min-height: 0;
    }
    .inspire-trip-card__title {
        bottom: 8px;
        font-size: 0.88rem;
        left: 8px;
    }
    .inspire-trip-card__badge {
        font-size: 0.58rem;
        left: 6px;
        top: 6px;
    }
    .inspire-trip-arrow,
    .inspire-sights-arrow {
        height: 34px;
        width: 34px;
    }
    .inspire-sights-row {
        padding-right: 30px;
    }
    .inspire-sights-head {
        margin-bottom: 10px;
    }
    .inspire-sights-title {
        font-size: 1.05rem;
    }
    .inspire-sights-more {
        font-size: 0.82rem;
    }
    .inspire-sights-grid {
        gap: 8px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .inspire-sight-card {
        border-radius: 10px;
        aspect-ratio: 0.76 / 1;
        min-height: 0;
    }
    .inspire-sight-name {
        font-size: 0.8rem;
    }
    .inspire-sight-meta {
        font-size: 0.64rem;
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
@php
    $destinationCountries = collect($uaeDestinationCards ?? [])
        ->pluck('country')
        ->filter()
        ->map(fn ($country) => trim((string) $country))
        ->unique()
        ->implode(', ');
@endphp

<section class="uae-destination-section">
    <div class="container">
        <div class="uae-destination-panel">
            <div class="uae-destination-header">
                <span class="uae-destination-pin">
                    <img src="{{ asset('assets/images/website/top-destination/location.png') }}" alt="Location">
                </span>
                <h2 class="uae-destination-title">Top Destinations in the {{ $destinationCountries }}</h2>
            </div>

            <div class="uae-destination-slider">
                <button class="uae-destination-arrow uae-destination-arrow--left" id="uaeDestinationArrowPrev" type="button" aria-label="Previous destination">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="uae-destination-viewport" id="uaeDestinationViewport">
                    <div class="uae-destination-track row" id="uaeDestinationTrack">
                        @foreach($uaeDestinationCards as $entry)
                            <div class="col-10 col-sm-6 col-md-4 col-lg-auto">
                                <a class="uae-destination-card" href="{{ $buildExploreHotelUrl([
                                    'value' => $entry['city'],
                                    'country' => $entry['country'],
                                    'country_code' => $entry['country_code'],
                                    'location' => $entry['location'],
                                ]) }}">
                                    <figure class="uae-destination-card__image">
                                        <img src="{{ $entry['image'] }}" alt="{{ $entry['image_alt'] }}">
                                    </figure>
                                    <div class="uae-destination-card__body">
                                        <h3 class="uae-destination-card__title">{{ $entry['city'] }}</h3>
                                        <p class="uae-destination-card__meta">{{ $entry['accommodations'] }}</p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button class="uae-destination-arrow uae-destination-arrow--right" id="uaeDestinationArrowNext" type="button" aria-label="Next destination">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<section class="promotion-static-section">
    <div class="container">
        <div class="promotion-static-panel">
            <img class="promotion-static-cloud promotion-static-cloud--top" src="{{ asset('assets/images/website/accomodaion-pomotion/cloud-1.png') }}" alt="">
            <img class="promotion-static-cloud promotion-static-cloud--middle" src="{{ asset('assets/images/website/accomodaion-pomotion/cloud-2.png') }}" alt="">
            <img class="promotion-static-cloud promotion-static-cloud--bottom" src="{{ asset('assets/images/website/accomodaion-pomotion/cloud-1.png') }}" alt="">

            <div class="promotion-static-content">
                <div class="promotion-static-block">
                    <div class="promotion-static-head">
                        <h3 class="promotion-static-title">Accommodation Promotions</h3>
                        <a class="promotion-static-view" href="javascript:void(0)">View all <i class="bi bi-chevron-right"></i></a>
                    </div>

                    <div class="promotion-static-row">
                        <div class="promotion-static-grid promotion-static-grid--three">
                            @foreach($accommodationPromotions as $promotion)
                                <figure class="promotion-static-card">
                                    <img src="{{ $promotion['image'] }}" alt="{{ $promotion['alt'] }}">
                                </figure>
                            @endforeach
                        </div>

                        <button class="promotion-static-arrow" type="button" aria-label="Next accommodation promotions">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <div class="promotion-static-block">
                    <div class="promotion-static-head">
                        <h3 class="promotion-static-title">Flights & Activities Promotions</h3>
                        <a class="promotion-static-view" href="javascript:void(0)">View all <i class="bi bi-chevron-right"></i></a>
                    </div>

                    <div class="promotion-static-row">
                        <div class="promotion-static-grid promotion-static-grid--two">
                            @foreach($flightActivityPromotions as $promotion)
                                <figure class="promotion-static-card">
                                    <img src="{{ $promotion['image'] }}" alt="{{ $promotion['alt'] }}">
                                </figure>
                            @endforeach
                        </div>

                        <button class="promotion-static-arrow" type="button" aria-label="Next flight and activity promotions">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="inspire-static-section">
    <div class="container">
        <div class="inspire-static-panel">
            <img class="inspire-static-cloud inspire-static-cloud--top" src="{{ asset('assets/images/website/accomodaion-pomotion/cloud-1.png') }}" alt="">
            <img class="inspire-static-cloud inspire-static-cloud--middle" src="{{ asset('assets/images/website/accomodaion-pomotion/cloud-2.png') }}" alt="">
            <img class="inspire-static-cloud inspire-static-cloud--bottom" src="{{ asset('assets/images/website/accomodaion-pomotion/cloud-1.png') }}" alt="">

            <div class="inspire-static-content">
            <h3 class="inspire-static-heading">Get inspired for your next trip</h3>

            <div class="inspire-trip-row">
                <div class="inspire-trip-grid">
                    @foreach($inspireTripCards as $trip)
                        <a class="inspire-trip-card {{ $trip['active'] ? 'is-active' : '' }}" href="javascript:void(0)">
                            <img src="{{ $trip['image'] }}" alt="{{ $trip['title'] }}">
                            @if($trip['badge'])
                                <span class="inspire-trip-card__badge">{{ $trip['badge'] }}</span>
                            @endif
                            <h4 class="inspire-trip-card__title">{{ $trip['title'] }}</h4>
                        </a>
                    @endforeach
                </div>

                <button class="inspire-trip-arrow" type="button" aria-label="Next trip ideas">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="inspire-sights-head">
                <h4 class="inspire-sights-title">Top sights you can't miss in Doha</h4>
                <a class="inspire-sights-more" href="javascript:void(0)">More <i class="bi bi-chevron-right"></i></a>
            </div>

            <div class="inspire-sights-row">
                <div class="inspire-sights-grid">
                    @foreach($inspireDohaSights as $sight)
                        <article class="inspire-sight-card">
                            <img src="{{ $sight['image'] }}" alt="{{ $sight['title'] }}">
                            <span class="inspire-sight-tag">{ Trip,Best }</span>
                            <span class="inspire-sight-heart"><i class="bi bi-heart"></i></span>
                            <div class="inspire-sight-body">
                                <div class="inspire-sight-location">
                                    <span>{{ $sight['location'] }}</span>
                                    <span class="inspire-sight-score"><i class="bi bi-geo-alt-fill"></i> {{ $sight['score'] }}</span>
                                </div>
                                <h5 class="inspire-sight-name">{{ $sight['title'] }}</h5>
                                <p class="inspire-sight-meta">{{ $sight['rating'] }} /5 · {{ $sight['reviews'] }} reviews</p>
                                <div class="inspire-sight-stars" aria-hidden="true">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <button class="inspire-sights-arrow" type="button" aria-label="Next sights">
                    <i class="bi bi-chevron-right"></i>
                </button>
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

@push('scripts')
<script>
(function () {
    const viewport = document.getElementById('uaeDestinationViewport');
    const track = document.getElementById('uaeDestinationTrack');
    const prev = document.getElementById('uaeDestinationArrowPrev');
    const next = document.getElementById('uaeDestinationArrowNext');

    if (!viewport || !track || !prev || !next) return;

    const getScrollStep = () => {
        const firstCard = track.querySelector('[class*="col-"]');
        if (!firstCard) return viewport.clientWidth;

        const gap = parseFloat(window.getComputedStyle(track).gap || '0');
        return firstCard.getBoundingClientRect().width + gap;
    };

    const updateArrows = () => {
        const maxScrollLeft = viewport.scrollWidth - viewport.clientWidth;
        const hasOverflow = maxScrollLeft > 8;

        prev.classList.toggle('is-hidden', !hasOverflow);
        next.classList.toggle('is-hidden', !hasOverflow);

        prev.disabled = !hasOverflow || viewport.scrollLeft <= 8;
        next.disabled = !hasOverflow || viewport.scrollLeft >= (maxScrollLeft - 8);
    };

    prev.addEventListener('click', () => {
        viewport.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
    });

    next.addEventListener('click', () => {
        viewport.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
    });

    viewport.addEventListener('scroll', updateArrows, { passive: true });
    window.addEventListener('resize', updateArrows);

    updateArrows();
})();
</script>
@endpush
