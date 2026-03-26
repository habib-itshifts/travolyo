<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="display-currency" content="{{ session('currency', config('currency.default')) }}" />

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#17C3CE" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="Travolyo" />
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/favicon1.png') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon/favicon1.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('assets/images/favicon/favicon1.png') }}" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@yield('title', 'Travolyo') – Where Your Journey Takes Off</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    {{-- Google Fonts: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    {{-- Travolyo Custom CSS --}}
    <link href="{{ asset('assets/css/custom/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/custom/flight.css') }}" rel="stylesheet" />

    {{-- Page-specific styles --}}
    @stack('styles')

    <style>
        :root {
            --site-font-family: "Inter", sans-serif;
        }

        html,
        body {
            font-family: var(--site-font-family);
        }

        body,
        body h1,
        body h2,
        body h3,
        body h4,
        body h5,
        body h6,
        body p,
        body span,
        body a,
        body li,
        body ul,
        body ol,
        body small,
        body strong,
        body em,
        body label,
        body div,
        body section,
        body article,
        body aside,
        body header,
        body footer,
        body main,
        body nav,
        body button,
        body input,
        body select,
        body textarea,
        body table,
        body thead,
        body tbody,
        body tr,
        body td,
        body th,
        .navbar,
        .site-footer,
        .dropdown-menu,
        .dropdown-item,
        .modal-content,
        .modal-title,
        .form-control,
        .form-select,
        .btn,
        [class$="-page"],
        [class*="-page "],
        [class*="title"],
        [class*="subtitle"],
        [class*="heading"],
        [class*="label"],
        [class*="text"],
        [class*="content"] {
            font-family: var(--site-font-family) !important;
        }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    @include('layouts.partials._navbar')

    {{-- PAGE CONTENT --}}
    @yield('content')

    {{-- FOOTER --}}
    @include('layouts.partials._footer')

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- Travolyo Core JS --}}
    <script src="{{ asset('assets/js/custom/main.js') }}"></script>
    {{-- Location AJAX (country / city / airport pickers) --}}
    <script src="{{ asset('assets/js/custom/location.js') }}"></script>

    {{-- Auth Modal --}}
    @guest
        @include('layouts.partials._auth_modal')
    @endguest

    {{-- Page-specific scripts --}}
    @stack('scripts')

    {{-- Floating WhatsApp Button --}}
    <a class="floating-whatsapp" href="https://wa.me/1234567890" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.325-.336-.445-.342-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.13 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
        </svg>
    </a>
    <style>
        .floating-whatsapp {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 56px;
            height: 56px;
            background-color: #25D366;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            z-index: 9999;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
        }
        .floating-whatsapp:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            color: #fff;
        }
    </style>

    {{-- PWA: Service Worker + Install Prompt --}}
    <script>
    (function () {
        var deferredPrompt = null;
        var installBtn = document.getElementById('pwa-install-btn');

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function (err) {
                    console.warn('SW registration failed:', err);
                });
            });
        }

        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
        });

        if (installBtn) {
            installBtn.addEventListener('click', function () {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function () { deferredPrompt = null; });
                } else if (window.matchMedia('(display-mode: standalone)').matches) {
                    showPwaToast('Travolyo is already installed on your device.');
                } else {
                    showPwaToast('To install: open your browser menu and choose "Install app" or "Add to Home Screen".');
                }
            });
        }

        window.addEventListener('appinstalled', function () {
            if (installBtn) installBtn.style.display = 'none';
            deferredPrompt = null;
        });

        function showPwaToast(msg) {
            var existing = document.getElementById('pwa-toast');
            if (existing) existing.remove();
            var t = document.createElement('div');
            t.id = 'pwa-toast';
            t.textContent = msg;
            t.style.cssText = 'position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);background:#333;color:#fff;padding:.65rem 1.25rem;border-radius:8px;font-size:.85rem;z-index:9999;max-width:90%;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,.25);';
            document.body.appendChild(t);
            setTimeout(function () { t.remove(); }, 4000);
        }
    })();
    </script>

</body>
</html>
