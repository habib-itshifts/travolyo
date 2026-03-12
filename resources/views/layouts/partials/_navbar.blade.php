<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">

        {{-- Logo --}}
        <a class="navbar-brand" href="{{ url('/') }}">
            <img class="navbar-logo" src="{{ asset('assets/logo/travolyo-logo.svg') }}" alt="Travolyo">
        </a>

        {{-- Mobile Toggler --}}
        <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Nav Links --}}
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('hotels.*') ? 'nav-link--active' : '' }}"
                       href="{{ Route::has('hotels.index') ? route('hotels.index') : '#' }}">
                        Hotels
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('flights.*') ? 'nav-link--active' : '' }}"
                       href="{{ Route::has('flights.index') ? route('flights.index') : '#' }}">
                        Flights
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="#">Home &amp; Apts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="#">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('activities.*') ? 'nav-link--active' : '' }}"
                       href="{{ Route::has('activities.index') ? route('activities.index') : '#' }}">
                        Activities
                    </a>
                </li>
                <li class="nav-item dropdown nav-item-about">
                    <a class="nav-link px-3 dropdown-toggle {{ request()->routeIs('about') ? 'nav-link--active' : '' }}"
                       href="{{ Route::has('about') ? route('about') : '#' }}"
                       id="aboutDropdown"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        About Us
                    </a>
                    <ul class="dropdown-menu nav-about-dropdown shadow-sm border-0 rounded-0" aria-labelledby="aboutDropdown">
                        <li><a class="dropdown-item" href="#">Blogs</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('contact') ? 'nav-link--active' : '' }}"
                       href="{{ Route::has('contact') ? route('contact') : '#' }}">
                        Contact
                    </a>
                </li>
            </ul>

            {{-- Right Actions --}}
            <div class="d-flex align-items-center gap-3">
                {{-- PWA Install Button --}}
                <button id="pwa-install-btn"
                        class="btn btn-sm d-flex align-items-center gap-1 fw-500"
                        style="background:#17C3CE;color:#fff;border-radius:50px;padding:.35rem 1rem;font-size:.85rem;border:none;">
                    <i class="bi bi-download"></i>
                    <span>Install app</span>
                </button>

                {{-- Currency --}}
                <a href="#" class="text-dark text-decoration-none d-flex align-items-center gap-1 small fw-500">
                    <i class="bi bi-globe2"></i> USD
                </a>

                {{-- Auth --}}
                @auth
                    <div class="dropdown">
                        <a href="#" class="text-dark text-decoration-none d-flex align-items-center gap-1 small fw-500 dropdown-toggle"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                            @if(auth()->user()->hasRole('vendor'))
                            <li>
                                <a class="dropdown-item" href="{{ route('vendor.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>Vendor Dashboard
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasRole('admin'))
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-award me-2"></i>Admin Dashboard
                                </a>
                            </li>
                            @endif
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <button type="button" onclick="openAuthModal('signin')"
                            class="btn btn-link text-dark text-decoration-none d-flex align-items-center gap-1 small fw-500 p-0">
                        <i class="bi bi-person"></i> Sign in
                    </button>
                    <button type="button" onclick="openAuthModal('register')"
                            class="btn btn-sm fw-500"
                            style="background:#17C3CE;color:#fff;border-radius:50px;padding:.35rem 1rem;font-size:.85rem;border:none;">
                        Register
                    </button>
                @endauth
            </div>
        </div>

    </div>
</nav>
