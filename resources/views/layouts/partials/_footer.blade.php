<footer class="site-footer">
    <div class="container">
        <div class="row g-4">

            {{-- Brand --}}
            <div class="col-12 col-md-4 mb-2">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/logo/travolyo-logo.svg') }}" alt="Travolyo" style="height:40px;">
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
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Press</a></li>
                </ul>
            </div>

            {{-- Support --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Support</h6>
                <ul class="footer-links">
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>

            {{-- Legal --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Legal</h6>
                <ul class="footer-links">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cookies Policy</a></li>
                </ul>
            </div>

            {{-- Explore --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Explore</h6>
                <ul class="footer-links">
                    <li><a href="{{ Route::has('hotels.index') ? route('hotels.index') : '#' }}">Hotels</a></li>
                    <li><a href="{{ Route::has('flights.index') ? route('flights.index') : '#' }}">Flights</a></li>
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
