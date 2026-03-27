@php
    $currencies = \App\Models\Currency::supported();
    $activeCurrencyCode = session('currency', \App\Models\Currency::defaultCode());
    $activeCurrency = $currencies[$activeCurrencyCode] ?? reset($currencies);

    $languages = config('language.supported', []);
    $activeLanguageCode = app()->getLocale();
    $activeLanguage = $languages[$activeLanguageCode] ?? reset($languages);

    $listYourPlaceUrl = auth()->check()
        ? (auth()->user()->hasRole('vendor') ? route('vendor.dashboard') : (Route::has('contact') ? route('contact') : '#'))
        : '#';
@endphp

<style>
.travolyo-topbar {
    backdrop-filter: blur(16px);
    background: rgba(255, 255, 255, 0.94);
    border-bottom: 1px solid rgba(17, 110, 161, 0.08);
    box-shadow: 0 8px 20px rgba(18, 38, 63, 0.05);
    padding: 12px 0;
}
.travolyo-topbar .container { max-width: 1320px; }
.travolyo-brand { align-items: center; display: inline-flex; gap: 8px; text-decoration: none; }
.travolyo-brand img { height: 54px; width: auto; }
.travolyo-nav { align-items: flex-end; gap: 24px; }
.travolyo-nav__item {
    align-items: flex-start;
    display: inline-flex;
    flex-direction: column;
    gap: 5px;
    justify-content: flex-end;
}
.travolyo-nav__label-row {
    align-items: center;
    display: flex;
    min-height: 16px;
}
.travolyo-nav__link {
    align-items: center;
    border-radius: 999px;
    color: #18415f;
    display: inline-flex;
    font-size: 0.8rem;
    font-weight: 700;
    gap: 6px;
    line-height: 1;
    min-height: 22px;
    padding: 0;
    text-decoration: none;
    transition: background 0.18s ease, color 0.18s ease, transform 0.18s ease;
    white-space: nowrap;
}
.travolyo-nav__link:hover { color: #0f88ca; transform: translateY(-1px); }
.travolyo-nav__link.is-active { color: #0f88ca; }
.travolyo-nav__badge {
    background: #18d4e6;
    border-radius: 999px;
    color: #ffffff;
    display: inline-flex;
    font-size: 0.58rem;
    font-weight: 800;
    line-height: 1;
    padding: 4px 8px;
    text-transform: uppercase;
}
.travolyo-nav__promo { font-weight: 700; }
.travolyo-nav__ghost {
    align-items: center;
    color: #244863;
    display: inline-flex;
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1;
    min-height: 22px;
    padding: 0;
}
.travolyo-actions { align-items: center; gap: 8px; }
.travolyo-btn {
    align-items: center;
    border-radius: 999px;
    display: inline-flex;
    font-size: 0.82rem;
    font-weight: 700;
    gap: 8px;
    justify-content: center;
    min-height: 36px;
    padding: 0 16px;
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, color 0.18s ease;
    white-space: nowrap;
}
.travolyo-btn:hover { transform: translateY(-1px); }
.travolyo-btn--outline {
    background: #ffffff;
    border: 1px solid #c9dce8;
    color: #18d4e6;
}
.travolyo-btn--primary {
    background: #ffffff;
    border: 1px solid #7dd2eb;
    color: #18d4e6;
}
.travolyo-btn--plain {
    background: transparent;
    border: 0;
    color: #0f88ca;
    padding: 0 6px;
}
.travolyo-currency {
    align-items: center;
    background: #ffffff;
    border: 1px solid #d4e2ec;
    border-radius: 999px;
    display: inline-flex;
    gap: 6px;
    min-height: 36px;
    padding: 0 12px;
}
.travolyo-currency img,
.travolyo-lang {
    border-radius: 50%;
    height: 16px;
    object-fit: cover;
    width: 16px;
}
.travolyo-more {
    align-items: center;
    display: inline-flex;
    justify-content: center;
    min-height: 36px;
    padding: 0;
    width: 36px;
}
.travolyo-more.dropdown-toggle::after,
.travolyo-currency.dropdown-toggle::after,
.travolyo-btn--plain.dropdown-toggle::after { display: none; }
.travolyo-more-menu {
    border: 1px solid #dbe7ef;
    border-radius: 18px;
    box-shadow: 0 20px 38px rgba(18, 38, 63, 0.14);
    min-width: 230px;
    padding: 10px;
}
.travolyo-more-menu .dropdown-item {
    border-radius: 12px;
    font-size: 0.92rem;
    font-weight: 600;
    padding: 10px 12px;
}
.travolyo-more-menu .dropdown-item:hover,
.travolyo-more-menu .dropdown-item.active { background: #eef8fd; color: #0f88ca; }
.travolyo-divider { border-top: 1px solid #ebf2f7; margin: 8px 0; }
@media (max-width: 1199.98px) {
    .travolyo-brand img { height: 48px; }
    .travolyo-nav { gap: 18px; }
    .travolyo-nav__link,
    .travolyo-nav__ghost { font-size: 0.74rem; padding: 0 8px; }
}
@media (max-width: 991.98px) {
    .travolyo-topbar { padding: 10px 0; }
    .travolyo-nav,
    .travolyo-actions { align-items: stretch; flex-direction: column; }
    .travolyo-nav__item { width: 100%; }
    .travolyo-nav__label-row { min-height: 0; }
    .travolyo-nav__link,
    .travolyo-nav__ghost,
    .travolyo-nav__promo,
    .travolyo-btn,
    .travolyo-currency { justify-content: flex-start; width: 100%; }
    .travolyo-brand { align-items: center; }
    .travolyo-brand img { height: 42px; }
}
</style>

<nav class="navbar navbar-expand-lg travolyo-topbar sticky-top">
    <div class="container">
        <a class="travolyo-brand me-3" href="{{ url('/') }}">
            <img src="{{ asset('assets/images/logo/travolyo-logo.svg') }}" alt="Travolyo">
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <div class="travolyo-nav d-flex flex-lg-row flex-column mx-lg-auto mt-3 mt-lg-0">
                <span class="travolyo-nav__item">
                    <span class="travolyo-nav__label-row">
                        <span class="travolyo-nav__badge">Bundle and save!</span>
                    </span>
                    <a href="{{ Route::has('website') ? route('website') : url('/') }}" class="travolyo-nav__link travolyo-nav__promo">
                        Flight + Hotel
                    </a>
                </span>
                <span class="travolyo-nav__item">
                    <span class="travolyo-nav__label-row">
                        <span class="travolyo-nav__badge">New!</span>
                    </span>
                    <a class="travolyo-nav__link {{ request()->routeIs('hotels.*') ? 'is-active' : '' }}" href="{{ Route::has('hotels.index') ? route('hotels.index') : '#' }}">
                        Hotels &amp; Homes
                    </a>
                </span>
                <span class="travolyo-nav__ghost">Transport</span>
                <a class="travolyo-nav__link {{ request()->routeIs('activities.*') ? 'is-active' : '' }}" href="{{ Route::has('activities.index') ? route('activities.index') : '#' }}">
                    Activities
                </a>
                <span class="travolyo-nav__ghost">Coupons</span>
            </div>

            <div class="travolyo-actions d-flex ms-lg-auto mt-3 mt-lg-0">
                <div class="dropdown">
                    <button class="travolyo-btn travolyo-btn--outline travolyo-more dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end travolyo-more-menu border-0">
                        <a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ Route::has('about') ? route('about') : '#' }}">About us</a>
                        <a class="dropdown-item {{ request()->routeIs('blogs.*') ? 'active' : '' }}" href="{{ Route::has('blogs.index') ? route('blogs.index') : '#' }}">Blogs</a>
                        <a class="dropdown-item {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ Route::has('contact') ? route('contact') : '#' }}">Contact us</a>
                        <div class="travolyo-divider"></div>
                        @if (!empty($activeLanguage))
                            <div class="px-2 pb-1 text-muted small text-uppercase fw-semibold">Language</div>
                            @foreach ($languages as $code => $language)
                                <a class="dropdown-item d-flex align-items-center gap-2 {{ $activeLanguageCode === $code ? 'active' : '' }}" href="{{ route('locale.switch', $code) }}">
                                    <img src="https://flagcdn.com/w20/{{ $language['flag'] }}.png" alt="{{ $code }}" class="travolyo-lang">
                                    <span>{{ $language['label'] }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                @if(auth()->check())
                    <a href="{{ $listYourPlaceUrl }}" class="travolyo-btn travolyo-btn--outline">List your place</a>
                @else
                    <button type="button" class="travolyo-btn travolyo-btn--outline" onclick="openAuthModal('register')">List your place</button>
                @endif

                @if (!empty($activeCurrency))
                    <div class="dropdown">
                        <button class="travolyo-currency btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://flagcdn.com/w20/{{ $activeCurrency['flag'] }}.png" alt="{{ $activeCurrencyCode }}">
                            <span>{{ $activeCurrencyCode }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end travolyo-more-menu border-0">
                            @foreach ($currencies as $code => $currency)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 {{ $activeCurrencyCode === $code ? 'active' : '' }}" href="{{ route('currency.switch', $code) }}">
                                        <img src="https://flagcdn.com/w20/{{ $currency['flag'] }}.png" alt="{{ $code }}">
                                        <span>{{ $currency['symbol'] }}</span>
                                        @if (strtoupper(trim((string) $currency['symbol'])) !== strtoupper(trim((string) $code)))
                                            <span>{{ $code }}</span>
                                        @endif
                                        <span class="ms-auto text-muted small">{{ $currency['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @auth
                    <div class="dropdown">
                        <button class="travolyo-btn travolyo-btn--plain dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span>{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end travolyo-more-menu border-0">
                            @if(auth()->user()->hasRole('customer'))
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">My dashboard</a></li>
                            @endif
                            @if(auth()->user()->hasRole('vendor'))
                                <li><a class="dropdown-item" href="{{ route('vendor.dashboard') }}">Vendor dashboard</a></li>
                            @endif
                            @if(auth()->user()->hasRole('admin'))
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin dashboard</a></li>
                            @endif
                            <li><div class="travolyo-divider"></div></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <button type="button" onclick="openAuthModal('signin')" class="travolyo-btn travolyo-btn--plain">Sign in</button>
                    <button type="button" onclick="openAuthModal('register')" class="travolyo-btn travolyo-btn--primary">Create account</button>
                @endauth
            </div>
        </div>
    </div>
</nav>
