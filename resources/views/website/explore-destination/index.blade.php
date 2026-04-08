@extends('layouts.master')

@section('title', 'Explore Hotels by Destination - Travolyo')

@php
    $selectedDestination = $selectedDestination ?? data_get($tab, 'label', 'Dubai');
    $featuredHotels = collect($featuredHotels ?? []);
    $hotelCount = $featuredHotels->count();
    $selectedTabKey = data_get($tab, 'key', '');
@endphp

@push('styles')
<style>
.dest-page { background: #f3f7fb; padding: 22px 0 34px; }
.dest-hero {
    background: linear-gradient(135deg, #0b5d81 0%, #0ca4c6 100%);
    border-radius: 24px;
    color: #fff;
    padding: 34px 28px 38px;
    box-shadow: 0 18px 42px rgba(13, 77, 111, .18);
    margin-bottom: 18px;
}
.dest-hero__eyebrow {
    color: rgba(255,255,255,.68);
    font-size: .76rem;
    font-weight: 800;
    letter-spacing: .16em;
    margin-bottom: 12px;
}
.dest-hero__title {
    font-size: clamp(2rem, 3vw, 2.7rem);
    font-weight: 800;
    letter-spacing: -.04em;
    line-height: 1.05;
    margin: 0 0 12px;
}
.dest-hero__text {
    color: rgba(255,255,255,.9);
    font-size: .98rem;
    line-height: 1.5;
    margin: 0;
    max-width: 720px;
}
.destinations-section {
    margin-top: 28px;
}
.destinations-shell, .hotel-section {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 14px 36px rgba(16, 52, 84, .08);
    padding: 28px 22px 24px;
}
.hotel-section { margin-top: 12px; }
.destinations-title, .hotel-section__title {
    color: #0b4d76;
    font-size: clamp(1.55rem, 2.4vw, 2.2rem);
    font-weight: 800;
    letter-spacing: -.03em;
    margin: 0 0 8px;
}
.destinations-text, .hotel-section__sub {
    color: #5e7591;
    margin: 0 0 22px;
}
.destinations-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
}
.destinations-tab {
    border: 2px solid #d6e0ee;
    border-radius: 999px;
    color: #385471;
    display: inline-flex;
    font-weight: 700;
    padding: 12px 18px;
    text-decoration: none;
    transition: .2s ease;
}
.destinations-tab:hover,
.destinations-tab.is-active {
    background: #17a9c8;
    border-color: #17a9c8;
    color: #fff;
}
.destinations-grid {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(6, minmax(0, 1fr));
}
.destination-card {
    border-radius: 18px;
    color: #fff;
    display: block;
    min-height: 190px;
    overflow: hidden;
    position: relative;
    text-decoration: none;
}
.destination-card__img {
    inset: 0;
    object-fit: cover;
    position: absolute;
    width: 100%;
    height: 100%;
}
.destination-card::after {
    background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,.62) 100%);
    content: '';
    inset: 0;
    position: absolute;
}
.destination-card__body {
    bottom: 0;
    left: 0;
    padding: 14px;
    position: absolute;
    right: 0;
    z-index: 1;
}
.destination-card__name {
    font-size: 1rem;
    font-weight: 800;
    line-height: 1.1;
    margin: 0 0 4px;
}
.destination-card__meta {
    font-size: .84rem;
    opacity: .9;
    margin: 0;
}
.hotel-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(5, minmax(0, 1fr));
}
.hotel-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 10px 22px rgba(15, 54, 88, .10);
    color: inherit;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    text-decoration: none;
}
.hotel-card__img {
    align-items: center;
    background: #eef3ff;
    display: flex;
    justify-content: center;
    aspect-ratio: 1 / 1;
    overflow: hidden;
}
.hotel-card__img img {
    display: block;
    height: 100%;
    object-fit: cover;
    width: 100%;
}
.hotel-card__placeholder {
    align-items: center;
    color: #90a4bf;
    display: flex;
    font-size: 2.1rem;
    height: 100%;
    justify-content: center;
    width: 100%;
}
.hotel-card__body { padding: 10px 10px 12px; }
.hotel-card__stars {
    color: #f59e0b;
    font-size: .72rem;
    letter-spacing: 1px;
    line-height: 1;
    margin-bottom: 6px;
}
.hotel-card__name {
    color: #123c61;
    font-size: .9rem;
    font-weight: 800;
    line-height: 1.15;
    margin: 0 0 4px;
}
.hotel-card__meta {
    color: #6f87a2;
    font-size: .71rem;
    line-height: 1.25;
    margin: 0;
}
@media (max-width: 1199.98px) {
    .hotel-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .destinations-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 767.98px) {
    .dest-hero { padding: 24px 18px 26px; }
    .hotel-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .destinations-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
</style>
@endpush

@section('content')
<div class="dest-page">
    <div class="container">
        <div class="dest-hero">
            <div class="dest-hero__eyebrow">WORLD-CLASS HOTELS</div>
            <h1 class="dest-hero__title">Find the Perfect Stay for Every Trip</h1>
            <p class="dest-hero__text">Search thousands of hotels across 190+ countries. Compare live rates, read reviews, and book with confidence.</p>
        </div>

        <div id="explore-content">
            <div class="destinations-section" id="destinations-section">
                <div class="destinations-shell">
                    <h2 class="destinations-title">Explore Hotels by Destination</h2>
                    <p class="destinations-text">Discover the world's most popular travel destinations - from urban city breaks to tropical beach escapes.</p>

                    <div class="destinations-tabs">
                        @foreach($tabs as $destinationTab)
                            <a
                                class="destinations-tab {{ data_get($destinationTab, 'key') === $selectedTabKey ? 'is-active' : '' }}"
                                data-explore-tab
                                href="{{ route('explore-destination-by-hotel', [
                                    'tab' => data_get($destinationTab, 'key'),
                                    'destination' => data_get($destinationTab, 'label'),
                                    'city' => data_get($destinationTab, 'label'),
                                ]) }}"
                            >
                                {{ data_get($destinationTab, 'label') }}
                            </a>
                        @endforeach
                    </div>

                    @php
                        $destinationCards = collect(data_get($tab, 'items', []));
                    @endphp

                    <div class="destinations-grid">
                        @foreach($destinationCards as $destinationCard)
                            @php
                                $destinationImage = data_get($destinationCard, 'image');
                                $cardDestination = data_get($destinationCard, 'destination', data_get($destinationCard, 'label'));
                                $cardCity = data_get($destinationCard, 'city', $cardDestination);
                            @endphp
                            <a
                                class="destination-card"
                                href="{{ route('explore-destination-by-hotel', [
                                    'tab' => $selectedTabKey,
                                    'destination' => $cardDestination,
                                    'city' => $cardCity,
                                ]) }}"
                            >
                                @if($destinationImage)
                                    <img class="destination-card__img" src="{{ $destinationImage }}" alt="{{ $cardDestination }}" loading="lazy">
                                @endif
                                <div class="destination-card__body">
                                    <h3 class="destination-card__name">{{ $cardDestination }}</h3>
                                    <p class="destination-card__meta">{{ data_get($destinationCard, 'meta') ?? '' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="hotel-section" id="hotel-section">
                <h2 class="hotel-section__title">Hotels in {{ $selectedDestination }}</h2>
                <p class="hotel-section__sub">{{ $hotelCount }} options found in {{ $selectedDestination }}</p>

                <div class="hotel-grid">
                    @forelse($featuredHotels as $hotel)
                        @php
                            $hotelImage = data_get($hotel, 'images.0') ?: data_get($hotel, 'images.1');
                        @endphp
                        <a class="hotel-card" href="{{ route('hotels.rooms', [
                            'offer_id' => data_get($hotel, 'id'),
                            'provider' => data_get($hotel, 'provider'),
                            'city' => data_get($hotel, 'city'),
                            'country' => data_get($hotel, 'country'),
                            'check_in' => $defaultHotelCheckIn,
                            'check_out' => $defaultHotelCheckOut,
                            'adults' => 1,
                            'children' => 0,
                            'hotel_name' => data_get($hotel, 'name'),
                            'hotel_stars' => data_get($hotel, 'star_rating', 0),
                        ]) }}">
                            <span class="hotel-card__img">
                                @if($hotelImage)
                                    <img src="{{ $hotelImage }}" alt="{{ data_get($hotel, 'name') }}" loading="lazy">
                                @else
                                    <span class="hotel-card__placeholder" aria-hidden="true">
                                        <i class="bi bi-building"></i>
                                    </span>
                                @endif
                            </span>
                            <span class="hotel-card__body">
                                <div class="hotel-card__stars">
                                    @php($stars = (int) data_get($hotel, 'star_rating', 0))
                                    {!! str_repeat('★', max(0, min(5, $stars))) !!}
                                    {!! str_repeat('☆', max(0, 5 - max(0, min(5, $stars)))) !!}
                                </div>
                                <h3 class="hotel-card__name">{{ data_get($hotel, 'name') }}</h3>
                                <p class="hotel-card__meta">
                                    {{ trim((string) data_get($hotel, 'address', '')) !== '' ? data_get($hotel, 'address') : data_get($hotel, 'city') }}
                                    {{ data_get($hotel, 'country') ? ', ' . data_get($hotel, 'country') : '' }}
                                </p>
                            </span>
                        </a>
                    @empty
                        <div class="alert alert-light border mb-0">No hotels found for this destination.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const page = document.querySelector('.dest-page');
    if (!page) return;

    const loadUrl = async (url, push = true) => {
        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) return;

        const html = await response.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const nextDestinations = doc.getElementById('destinations-section');
        const currentDestinations = document.getElementById('destinations-section');

        if (nextDestinations && currentDestinations) {
            currentDestinations.outerHTML = nextDestinations.outerHTML;
        }

        if (push) {
            window.history.pushState({}, '', url);
        }
    };

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-explore-tab]');
        if (!link) return;
        event.preventDefault();
        loadUrl(link.href, true);
    });

    window.addEventListener('popstate', () => {
        loadUrl(window.location.href, false);
    });
})();
</script>
@endpush
