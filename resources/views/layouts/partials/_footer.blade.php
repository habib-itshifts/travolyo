<footer class="site-footer">
    <div class="container">
        <div class="row g-4">

            {{-- Brand --}}
            <div class="col-12 col-md-4 mb-2">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/images/favicon/favicon1.png') }}" alt="Travolyo" style="height:40px;">
                </a>
                <p class="text-muted small mt-3" style="max-width:280px;line-height:1.6;">
                    Your one-stop travel platform for flights, hotels, and experiences. Plan smarter. Travel better.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="https://www.facebook.com/profile.php?id=61586651742003" target="_blank" rel="noopener" class="text-muted fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="https://x.com/travolyo" target="_blank" rel="noopener" class="text-muted fs-5"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://www.instagram.com/travolyo_official/" target="_blank" rel="noopener" class="text-muted fs-5"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

            {{-- Company --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Company</h6>
                <ul class="footer-links">
                    <li><a href="{{ Route::has('about') ? route('about') : '#' }}">About Us</a></li>
                    <li><a href="{{ Route::has('careers') ? route('careers') : '#' }}">Careers</a></li>
                    <li><a href="{{ Route::has('press') ? route('press') : '#' }}">Press</a></li>
                </ul>
            </div>

            {{-- Support --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Support</h6>
                <ul class="footer-links">
                    <li><a href="{{ Route::has('help') ? route('help') : '#' }}">Help Center</a></li>
                    <li><a href="{{ Route::has('contact') ? route('contact') : '#' }}">Contact Us</a></li>
                    <li><a href="{{ Route::has('faqs') ? route('faqs') : '#' }}">FAQs</a></li>
                </ul>
            </div>

            {{-- Legal --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Legal</h6>
                <ul class="footer-links">
                    <li><a href="{{ Route::has('privacy') ? route('privacy') : '#' }}">Privacy Policy</a></li>
                    <li><a href="{{ Route::has('terms') ? route('terms') : '#' }}">Terms of Service</a></li>
                    <li><a href="{{ Route::has('cookies') ? route('cookies') : '#' }}">Cookies Policy</a></li>
                </ul>
            </div>

            {{-- Explore --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Explore</h6>
                <ul class="footer-links">
                    <li><a href="{{ Route::has('hotels.index') ? route('hotels.index') : '#' }}">Hotels</a></li>
                    <li><a href="{{ url('/flights') }}">Flights</a></li>
                    <li><a href="{{ Route::has('activities.index') ? route('activities.index') : '#' }}">Activities</a></li>
                </ul>
            </div>

        </div>

        <hr class="footer-divider" />

        <p class="text-center text-muted small mb-0">
            &copy; {{ date('Y') }} Travolyo. All rights reserved.
        </p>
    </div>
</footer>
