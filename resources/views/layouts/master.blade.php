<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#17C3CE" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="Travolyo" />
    <link rel="apple-touch-icon" href="{{ asset('assets/logo/travolyo-logo.svg') }}" />

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
