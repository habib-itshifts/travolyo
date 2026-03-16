@php
    /*
     * Direction detection — add any RTL locale code here.
     */
    $rtlLocales = ['ar', 'he', 'fa', 'ur'];
    $dir        = in_array(app()->getLocale(), $rtlLocales) ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('admin.dashboard') }} — {{ config('app.name', __('admin.app_name')) }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('crm_theme/core.css') }}" rel="stylesheet">

    {{-- Admin colour tokens --}}
    <style>
        :root {
            --clr-primary:          #3ab5d4;
            --clr-primary-dark:     #1a7a91;
            --clr-primary-light:    rgba(58, 181, 212, 0.15);
            --clr-sidebar-bg:       #0f1f38;
            --clr-sidebar-hover:    rgba(255, 255, 255, 0.06);
            --clr-sidebar-active:   rgba(58, 181, 212, 0.15);
            --clr-sidebar-text:     #7a90ab;
            --clr-sidebar-text-act: #3ab5d4;
            --clr-sidebar-section:  #4b6080;
            --clr-sidebar-icon:     #3a5070;
            --clr-sidebar-disabled: #2a3f58;
            --clr-page-bg:          #f4f6fb;
            --clr-topbar-bg:        #ffffff;
            --clr-badge-bg:         #2dd4bf;
            --radius-btn:           0.5rem;
            --sidebar-width:        15rem;
            --topbar-height:        60px;
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="d-flex" style="min-height:100vh;">

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar-bg d-flex flex-column flex-shrink-0" style="width: var(--sidebar-width); min-height: 100vh;">

        {{-- Logo --}}
        <div class="d-flex align-items-center gap-2 px-3 border-bottom border-white border-opacity-10" style="padding-top:18px;padding-bottom:18px;">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                     style="width:28px;height:28px;background-color:var(--clr-primary);">
                    <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                    </svg>
                </div>
                <span class="text-white fw-bold" style="font-size:15px;letter-spacing:.02em;">{{ __('admin.app_name') }}</span>
                <span class="badge-version">v3.6.2</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-fill overflow-auto px-2 py-3" style="scrollbar-width:none;">
            @include('admin::components.layouts.partials.sidebar')
        </nav>

        {{-- Bottom: user info + logout --}}
        <div class="px-3 py-3 border-top border-white border-opacity-10">
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold flex-shrink-0"
                     style="width:32px;height:32px;background:var(--clr-primary-light);border:1px solid var(--clr-primary);color:var(--clr-primary);font-size:12px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-fill" style="min-width:0;">
                    <p class="text-white fw-semibold mb-0 text-truncate" style="font-size:12px;">{{ auth()->user()->name }}</p>
                    <p class="mb-0 text-truncate" style="color:var(--clr-sidebar-section);font-size:11px;">{{ __('admin.administrator') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-1 text-decoration-none border-0"
                            style="color:var(--clr-sidebar-section);"
                            title="{{ __('admin.logout') }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN AREA ── --}}
    <div class="flex-fill d-flex flex-column overflow-hidden" style="min-width:0;">

        {{-- Topbar --}}
        <header class="topbar-shadow d-flex align-items-center justify-content-between px-4 gap-3 flex-shrink-0 bg-white"
                style="height: var(--topbar-height);">

            {{-- Start: View Site --}}
            <div class="d-flex align-items-center gap-2">
                <a href="/" target="_blank"
                   class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                   style="font-size:12px;border-radius:var(--radius-btn);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ __('admin.view_site') }}
                </a>
            </div>

            {{-- End: currency | language | notifications | user --}}
            <div class="d-flex align-items-center gap-3">

                {{-- Currency dropdown --}}
                @php
                    $currencies      = \App\Models\Currency::supported();
                    $activeCurrency  = session('currency', \App\Models\Currency::defaultCode());
                    $currentCurrency = $currencies[$activeCurrency] ?? reset($currencies);
                @endphp
                <div class="dropdown topbar-dropdown">
                    <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-1 border-0 text-secondary bg-transparent"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="font-size:12px;">
                        <img src="https://flagcdn.com/w20/{{ $currentCurrency['flag'] }}.png"
                             alt="{{ $activeCurrency }}" style="width:16px;" class="rounded-1">
                        <span class="fw-medium">{{ $currentCurrency['symbol'] }}</span>
                        <span>{{ $currentCurrency['label'] }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:180px;">
                        @foreach ($currencies as $code => $currency)
                            <li>
                                <a href="{{ route('currency.switch', $code) }}"
                                   class="dropdown-item d-flex align-items-center gap-2 {{ $activeCurrency === $code ? 'active' : '' }}"
                                   style="font-size:12px;">
                                    <img src="https://flagcdn.com/w20/{{ $currency['flag'] }}.png"
                                         alt="{{ $code }}" style="width:16px;" class="rounded-1 flex-shrink-0">
                                    <span class="fw-semibold" style="width:20px;">{{ $currency['symbol'] }}</span>
                                    <span>{{ $currency['label'] }}</span>
                                    <span class="text-muted ms-auto" style="font-size:10px;">{{ $currency['name'] }}</span>
                                    @if ($activeCurrency === $code)
                                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="color:var(--clr-primary);">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="vr" style="height:20px;opacity:.2;"></div>

                {{-- Language dropdown --}}
                @php
                    $languages   = config('language.supported');
                    $currentLang = $languages[app()->getLocale()] ?? reset($languages);
                @endphp
                <div class="dropdown topbar-dropdown">
                    <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-1 border-0 text-secondary bg-transparent"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="font-size:12px;">
                        <img src="https://flagcdn.com/w20/{{ $currentLang['flag'] }}.png"
                             alt="{{ app()->getLocale() }}" style="width:16px;" class="rounded-1">
                        <span>{{ $currentLang['label'] }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:160px;">
                        @foreach ($languages as $code => $lang)
                            <li>
                                <a href="{{ route('locale.switch', $code) }}"
                                   class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() === $code ? 'active' : '' }}"
                                   style="font-size:12px;">
                                    <img src="https://flagcdn.com/w20/{{ $lang['flag'] }}.png"
                                         alt="{{ $code }}" style="width:16px;" class="rounded-1 flex-shrink-0">
                                    <span>{{ $lang['label'] }}</span>
                                    @if (app()->getLocale() === $code)
                                        <svg class="ms-auto" width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="color:var(--clr-primary);">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="vr" style="height:20px;opacity:.2;"></div>

                {{-- Notifications --}}
                <button class="btn btn-sm btn-link text-secondary p-2 position-relative border-0"
                        style="text-decoration:none;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="position-absolute bg-danger border border-white rounded-circle"
                          style="top:6px;inset-inline-end:6px;width:8px;height:8px;"></span>
                </button>

                <div class="vr" style="height:20px;opacity:.2;"></div>

                {{-- User --}}
                <div class="d-flex align-items-center gap-2" style="cursor:pointer;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                         style="width:32px;height:32px;background:linear-gradient(135deg,var(--clr-primary),var(--clr-primary-dark));font-size:12px;flex-shrink:0;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="d-none d-sm-block lh-sm">
                        <p class="mb-0 fw-semibold text-dark" style="font-size:12px;">{{ auth()->user()->name }}</p>
                        <p class="mb-0 text-muted" style="font-size:10px;">{{ auth()->user()->email }}</p>
                    </div>
                    <svg width="14" height="14" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-fill overflow-auto p-4" style="background-color: var(--clr-page-bg);">

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                    <svg width="16" height="16" class="flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                    <svg width="16" height="16" class="flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
