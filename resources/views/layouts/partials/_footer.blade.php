@php
    $footerCompanyLinks = [
        ['label' => __('website.footer_link_about'),   'href' => Route::has('about') ? route('about') : '#'],
        ['label' => __('website.footer_link_contact'), 'href' => Route::has('contact') ? route('contact') : '#'],
        ['label' => __('website.footer_link_blog'),    'href' => Route::has('blogs.index') ? route('blogs.index') : '#'],
        ['label' => __('website.footer_link_privacy'), 'href' => Route::has('privacy') ? route('privacy') : '#'],
        ['label' => __('website.footer_link_terms'),   'href' => Route::has('terms') ? route('terms') : '#'],
    ];

    $footerDestinationLinks = [
        ['label' => __('website.footer_dest_dubai'),    'href' => Route::has('hotels.index') ? route('hotels.index', ['city' => 'Dubai']) : '#'],
        ['label' => __('website.footer_dest_maldives'), 'href' => Route::has('hotels.index') ? route('hotels.index', ['city' => 'Maldives']) : '#'],
        ['label' => __('website.footer_dest_bali'),     'href' => Route::has('hotels.index') ? route('hotels.index', ['city' => 'Bali']) : '#'],
        ['label' => __('website.footer_dest_japan'),    'href' => Route::has('hotels.index') ? route('hotels.index', ['city' => 'Japan']) : '#'],
        ['label' => __('website.footer_dest_coaching'), 'href' => Route::has('contact') ? route('contact') : '#'],
    ];

    $footerSocialLinks = [
        ['label' => 'Instagram', 'href' => 'https://www.instagram.com/travolyo_official/'],
        ['label' => 'X', 'href' => 'https://x.com/travolyo'],
        ['label' => 'Facebook', 'href' => 'https://www.facebook.com/profile.php?id=61586651742003'],
    ];
@endphp

<style>
.site-footer {
    padding: 36px 16px 28px;
}

.site-footer__card {
    background-color: #0f77a8;
    background-image: url("{{ asset('assets/images/website/footer-background.png') }}");
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    border-radius: 22px;
    color: #ffffff;
    margin: 0 auto;
    max-width: 1320px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 24px 50px rgba(8, 65, 96, 0.16);
}

.site-footer__card::after {
    display: none;
}

.site-footer__inner {
    padding: 46px 46px 34px;
    position: relative;
    z-index: 1;
}

.site-footer__grid {
    display: grid;
    gap: 34px;
    grid-template-columns: minmax(300px, 1.4fr) repeat(3, minmax(130px, 0.7fr));
    align-items: start;
}

.site-footer__lead {
    max-width: 360px;
}

.site-footer__title {
    color: #ffffff;
    font-size: clamp(1.8rem, 3vw, 2.35rem);
    font-weight: 600;
    letter-spacing: -0.03em;
    line-height: 1.16;
    margin: 0 0 28px;
}

.site-footer__subscribe {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.26);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 999px;
    gap: 10px;
    max-width: 340px;
    padding: 6px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.site-footer__input {
    background: transparent;
    border: 0;
    box-shadow: none;
    color: #ffffff;
    flex: 1 1 auto;
    font-size: 0.96rem;
    min-height: 48px;
    padding: 0 14px;
}

.site-footer__input::placeholder {
    color: rgba(255, 255, 255, 0.78);
}

.site-footer__input:focus {
    background: transparent;
    color: #ffffff;
    box-shadow: none;
}

.site-footer__button {
    background: linear-gradient(135deg, #f8b45d 0%, #f39140 100%);
    border: 0;
    border-radius: 999px;
    color: #ffffff;
    flex: 0 0 auto;
    font-size: 0.92rem;
    font-weight: 600;
    min-height: 48px;
    padding: 0 24px;
}

.site-footer__button:hover {
    background: linear-gradient(135deg, #f6a84b 0%, #ef8330 100%);
    color: #ffffff;
}

.site-footer__brand {
    flex: 0 0 auto;
    margin-top: 34px;
    width: clamp(260px, 28vw, 430px);
}

.site-footer__brand a {
    display: block;
    width: 100%;
}

.site-footer__logo {
    display: block;
    width: 80%;
    height: auto;
    filter: brightness(0) invert(1);
}

.site-footer__menu-title {
    color: #ffffff;
    font-size: 1.55rem;
    font-weight: 700;
    margin: 0 0 18px;
}

.site-footer__menu {
    list-style: none;
    margin: 0;
    padding: 0;
}

.site-footer__menu li + li {
    margin-top: 10px;
}

.site-footer__menu a {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1rem;
    text-decoration: none;
    transition: color 0.18s ease, opacity 0.18s ease;
}

.site-footer__menu a:hover {
    color: #ffffff;
    opacity: 0.82;
}

.site-footer__bottom {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-top: 34px;
}

.site-footer__copyright {
    color: rgba(255, 255, 255, 0.86);
    flex: 1 1 auto;
    font-size: 0.95rem;
    margin: 0;
    text-align: right;
}

@media (max-width: 1199.98px) {
    .site-footer__grid {
        grid-template-columns: minmax(280px, 1.2fr) repeat(3, minmax(120px, 0.7fr));
        gap: 28px;
    }

    .site-footer__inner {
        padding: 40px 32px 30px;
    }
}

@media (max-width: 991.98px) {
    .site-footer__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .site-footer__lead {
        grid-column: 1 / -1;
        max-width: 100%;
    }

    .site-footer__title {
        max-width: 520px;
    }

    .site-footer__bottom {
        align-items: flex-start;
        flex-direction: column;
    }

    .site-footer__copyright {
        text-align: left;
    }
}

@media (max-width: 767.98px) {
    .site-footer {
        padding: 28px 12px 22px;
    }

    .site-footer__inner {
        padding: 32px 20px 26px;
    }

    .site-footer__grid {
        gap: 24px;
        grid-template-columns: minmax(0, 1fr);
    }

    .site-footer__title {
        font-size: 1.9rem;
        margin-bottom: 22px;
    }

    .site-footer__subscribe {
        flex-direction: column;
        align-items: stretch;
        border-radius: 22px;
        max-width: 100%;
        padding: 10px;
    }

    .site-footer__button {
        width: 100%;
    }

    .site-footer__brand {
        margin-top: 24px;
        width: clamp(220px, 62vw, 340px);
    }

    .site-footer__logo {
        width: 100%;
    }

    .site-footer__menu-title {
        font-size: 1.25rem;
        margin-bottom: 12px;
    }

    .site-footer__bottom {
        margin-top: 24px;
    }
}
</style>

<footer class="site-footer">
    <div class="site-footer__card">
        <div class="site-footer__inner">
            <div class="site-footer__grid">
                <div class="site-footer__lead">
                    <h2 class="site-footer__title">{{ __('website.footer_tagline') }}</h2>

                    <form class="site-footer__subscribe" action="#" method="GET" onsubmit="return false;">
                        <input
                            type="email"
                            class="form-control site-footer__input"
                            placeholder="{{ __('website.footer_email_placeholder') }}"
                            aria-label="{{ __('website.footer_email_aria') }}"
                        >
                        <button type="button" class="btn site-footer__button">{{ __('website.footer_subscribe') }}</button>
                    </form>
                </div>

                <div class="site-footer__menu-col">
                    <h3 class="site-footer__menu-title">{{ __('website.footer_col_company') }}</h3>
                    <ul class="site-footer__menu">
                        @foreach ($footerCompanyLinks as $item)
                            <li><a href="{{ $item['href'] }}">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="site-footer__menu-col">
                    <h3 class="site-footer__menu-title">{{ __('website.footer_col_destinations') }}</h3>
                    <ul class="site-footer__menu">
                        @foreach ($footerDestinationLinks as $item)
                            <li><a href="{{ $item['href'] }}">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="site-footer__menu-col">
                    <h3 class="site-footer__menu-title">{{ __('website.footer_col_social') }}</h3>
                    <ul class="site-footer__menu">
                        @foreach ($footerSocialLinks as $item)
                            <li><a href="{{ $item['href'] }}" target="_blank" rel="noopener">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="site-footer__bottom">
                <div class="site-footer__brand">
                    <a href="{{ url('/') }}" aria-label="{{ __('website.footer_home_aria') }}">
                        <img
                            class="site-footer__logo"
                            src="{{ asset('assets/images/logo/travolyo-logo.svg') }}"
                            alt="Travolyo"
                        >
                    </a>
                </div>

                <p class="site-footer__copyright">{{ __('website.footer_copyright', ['year' => date('Y')]) }}</p>
            </div>
        </div>
    </div>
</footer>
