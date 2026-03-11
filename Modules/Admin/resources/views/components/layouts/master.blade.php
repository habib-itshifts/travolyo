@php
    /*
     * Direction detection — add any RTL locale code here.
     * The $dir variable drives both the HTML dir attribute and
     * Tailwind's rtl: variants throughout the template.
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /*
        ╔═══════════════════════════════════════════════════════════════╗
        ║  DESIGN TOKENS                                                ║
        ║  To retheme the admin panel, ONLY edit this :root block.     ║
        ║  Do NOT touch the "STRUCTURAL" section below.                ║
        ╚═══════════════════════════════════════════════════════════════╝
        */
        :root {
            /* Brand colours */
            --clr-primary:          #3ab5d4;
            --clr-primary-dark:     #1a7a91;
            --clr-primary-light:    rgba(58, 181, 212, 0.15);

            /* Sidebar */
            --clr-sidebar-bg:       #0f1f38;
            --clr-sidebar-hover:    rgba(255, 255, 255, 0.06);
            --clr-sidebar-active:   rgba(58, 181, 212, 0.15);
            --clr-sidebar-text:     #7a90ab;
            --clr-sidebar-text-act: #3ab5d4;
            --clr-sidebar-section:  #4b6080;
            --clr-sidebar-icon:     #3a5070;
            --clr-sidebar-disabled: #2a3f58;

            /* Page */
            --clr-page-bg:          #f4f6fb;
            --clr-topbar-bg:        #ffffff;

            /* Cards */
            --radius-card:          1rem;          /* rounded-2xl */
            --radius-btn:           0.5rem;        /* rounded-lg */

            /* Badge / pill */
            --clr-badge-bg:         #2dd4bf;

            /* Sidebar sizing */
            --sidebar-width:        15rem;         /* w-60 */
            --topbar-height:        60px;
        }

        /*
        ╔═══════════════════════════════════════════════════════════════╗
        ║  STRUCTURAL — layout + component classes built on the tokens  ║
        ║  These reference CSS vars only — don't hardcode colours here. ║
        ╚═══════════════════════════════════════════════════════════════╝
        */

        /* Sidebar chrome */
        .sidebar-bg        { background-color: var(--clr-sidebar-bg); }
        .sidebar-hover:hover { background-color: var(--clr-sidebar-hover); }

        /*
         * Active nav item: accent border on the *start* side.
         * border-inline-start flips automatically in RTL — no extra code needed.
         */
        .sidebar-active    {
            background-color: var(--clr-sidebar-active);
            border-inline-start: 3px solid var(--clr-primary);
            color: var(--clr-sidebar-text-act);
        }
        .sidebar-inactive  { border-inline-start: 3px solid transparent; }

        /* Nav section label */
        .nav-section {
            color: var(--clr-sidebar-section);
            font-size: 0.68rem;
            letter-spacing: .1em;
        }

        /* Version badge */
        .badge-version {
            background: var(--clr-badge-bg);
            color: #fff;
            font-size: 0.58rem;
            padding: 2px 7px;
            border-radius: 20px;
        }

        /* Topbar bottom shadow */
        .topbar-shadow { box-shadow: 0 1px 0 0 #e5e7eb; }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased" style="background-color: var(--clr-page-bg);">

{{--
    Layout logic:
    - DOM order: [sidebar] [main content]
    - LTR (English): flex-row → sidebar LEFT, content RIGHT  ✓
    - RTL (Arabic):  dir="rtl" reverses flex-row → sidebar RIGHT, content LEFT  ✓
    - No extra Tailwind overrides needed — CSS direction handles it automatically.
--}}
<div class="min-h-screen flex">

    {{-- ── SIDEBAR (first in DOM = LEFT in LTR, RIGHT in RTL) ── --}}
    <aside class="sidebar-bg flex flex-col flex-shrink-0" style="width: var(--sidebar-width); min-height: 100vh;">

        {{-- Logo --}}
        <div class="flex items-center gap-2 px-5 py-[18px] border-b border-white/[0.07]">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background-color: var(--clr-primary);">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                    </svg>
                </div>
                <span class="text-white text-[15px] font-bold tracking-wide">{{ __('admin.app_name') }}</span>
                <span class="badge-version">v3.6.2</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-2.5 py-4 space-y-0.5">
            @include('admin::components.layouts.partials.sidebar')
        </nav>

        {{-- Bottom: user info + logout --}}
        <div class="px-3 py-4 border-t border-white/[0.07]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                     style="background: var(--clr-primary-light); border: 1px solid var(--clr-primary); color: var(--clr-primary);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs truncate" style="color: var(--clr-sidebar-section);">{{ __('admin.administrator') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1 transition hover:text-red-400"
                            style="color: var(--clr-sidebar-section);"
                            title="{{ __('admin.logout') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN AREA ── --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Topbar --}}
        <header class="topbar-shadow flex items-center justify-between px-6 gap-4 flex-shrink-0"
                style="height: var(--topbar-height); background-color: var(--clr-topbar-bg);">

            {{-- Start: View Site --}}
            <div class="flex items-center gap-3">
                <a href="/" target="_blank"
                   class="flex items-center gap-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 hover:border-gray-300 transition"
                   style="border-radius: var(--radius-btn);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ __('admin.view_site') }}
                </a>
            </div>

            {{-- End: currency | language | notifications | user --}}
            <div class="flex items-center gap-3">

                {{-- Currency dropdown — options from config/currency.php --}}
                @php
                    $currencies      = config('currency.supported');
                    $activeCurrency  = session('currency', config('currency.default'));
                    $currentCurrency = $currencies[$activeCurrency] ?? reset($currencies);
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 px-2 py-1.5 rounded-lg hover:bg-gray-50 transition">
                        <img src="https://flagcdn.com/w20/{{ $currentCurrency['flag'] }}.png"
                             alt="{{ $activeCurrency }}" class="w-4 h-auto rounded-sm">
                        <span class="font-medium">{{ $currentCurrency['symbol'] }}</span>
                        <span>{{ $currentCurrency['label'] }}</span>
                        <svg class="w-3 h-3 text-gray-400 transition-transform duration-200"
                             :class="{ 'rotate-180': open }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute end-0 top-full mt-1.5 w-44 bg-white border border-gray-200 rounded-xl shadow-lg z-50 overflow-hidden"
                         style="display:none;">
                        @foreach ($currencies as $code => $currency)
                            <a href="{{ route('currency.switch', $code) }}"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs hover:bg-gray-50 transition
                                      {{ $activeCurrency === $code ? 'text-[color:var(--clr-primary)] font-semibold bg-gray-50/60' : 'text-gray-600' }}">
                                <img src="https://flagcdn.com/w20/{{ $currency['flag'] }}.png"
                                     alt="{{ $code }}" class="w-4 h-auto rounded-sm flex-shrink-0">
                                <span class="font-semibold w-5">{{ $currency['symbol'] }}</span>
                                <span>{{ $currency['label'] }}</span>
                                <span class="text-gray-400 ms-auto text-[10px]">{{ $currency['name'] }}</span>
                                @if ($activeCurrency === $code)
                                    <svg class="w-3 h-3 text-[color:var(--clr-primary)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="w-px h-5 bg-gray-200"></div>

                {{-- Language dropdown — options from config/language.php --}}
                @php
                    $languages   = config('language.supported');
                    $currentLang = $languages[app()->getLocale()] ?? reset($languages);
                @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 px-2 py-1.5 rounded-lg hover:bg-gray-50 transition">
                        <img src="https://flagcdn.com/w20/{{ $currentLang['flag'] }}.png"
                             alt="{{ app()->getLocale() }}" class="w-4 h-auto rounded-sm">
                        <span>{{ $currentLang['label'] }}</span>
                        <svg class="w-3 h-3 text-gray-400 transition-transform duration-200"
                             :class="{ 'rotate-180': open }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute end-0 top-full mt-1.5 w-40 bg-white border border-gray-200 rounded-xl shadow-lg z-50 overflow-hidden"
                         style="display:none;">
                        @foreach ($languages as $code => $lang)
                            <a href="{{ route('locale.switch', $code) }}"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs hover:bg-gray-50 transition
                                      {{ app()->getLocale() === $code ? 'text-[color:var(--clr-primary)] font-semibold bg-gray-50/60' : 'text-gray-600' }}">
                                <img src="https://flagcdn.com/w20/{{ $lang['flag'] }}.png"
                                     alt="{{ $code }}" class="w-4 h-auto rounded-sm flex-shrink-0">
                                <span>{{ $lang['label'] }}</span>
                                @if (app()->getLocale() === $code)
                                    <svg class="w-3 h-3 ms-auto" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="w-px h-5 bg-gray-200"></div>

                {{-- Notifications --}}
                <button class="relative p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 end-1 w-2 h-2 rounded-full border border-white bg-red-500"></span>
                </button>

                <div class="w-px h-5 bg-gray-200"></div>

                {{-- User --}}
                <div class="flex items-center gap-2.5 cursor-pointer px-1 py-1 rounded-lg hover:bg-gray-50 transition">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm"
                         style="background: linear-gradient(135deg, var(--clr-primary), var(--clr-primary-dark));">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <p class="text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6" style="background-color: var(--clr-page-bg);">

            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

</div>

@stack('scripts')
</body>
</html>
