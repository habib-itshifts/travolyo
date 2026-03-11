@extends('layouts.master')

@section('title', 'Travolyo – Where Your Journey Takes Off')

@section('content')

{{-- ═══════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════ --}}
<section class="hero-section d-flex flex-column justify-content-center">
    <div class="container text-center text-white">

        <h1 class="hero-title text-white mb-3">
            Explore the World, <br class="d-none d-md-block"> Your Way
        </h1>
        <p class="hero-subtitle text-white mb-5">
            Flights, hotels, activities &amp; more — all in one place.
        </p>

        {{-- Search Widget --}}
        @include('home.partials._search-widget')

        {{-- Quick Links --}}
        <div class="hero-quick-links mt-4">
            <a href="#">Dubai</a>
            <span class="dot"></span>
            <a href="#">London</a>
            <span class="dot"></span>
            <a href="#">Paris</a>
            <span class="dot"></span>
            <a href="#">New York</a>
            <span class="dot"></span>
            <a href="#">Bangkok</a>
            <span class="dot"></span>
            <a href="#">Tokyo</a>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════
     FEATURED HOTELS
════════════════════════════════════════════ --}}
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Featured Hotels</h2>
            <a href="{{ Route::has('hotels.index') ? route('hotels.index') : '#' }}" class="view-all-link">
                View all <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">

            {{-- Card 1 --}}
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

            {{-- Card 2 --}}
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

            {{-- Card 3 --}}
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

            {{-- Card 4 --}}
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

{{-- ═══════════════════════════════════════════
     PROMO BANNER
════════════════════════════════════════════ --}}
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

{{-- ═══════════════════════════════════════════
     POPULAR DESTINATIONS
════════════════════════════════════════════ --}}
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

{{-- ═══════════════════════════════════════════
     WHY TRAVOLYO
════════════════════════════════════════════ --}}
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
