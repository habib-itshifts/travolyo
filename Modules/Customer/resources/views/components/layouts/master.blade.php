@php
    $rtlLocales = ['ar', 'he', 'fa', 'ur'];
    $dir        = in_array(app()->getLocale(), $rtlLocales) ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'My Account' }} — {{ config('app.name', 'Travolyo') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('crm_theme/core.css') }}" rel="stylesheet">

    {{-- Customer colour tokens --}}
    <style>
        :root {
            --clr-primary:          #6366f1;
            --clr-primary-dark:     #4f46e5;
            --clr-primary-light:    rgba(99, 102, 241, 0.15);
            --clr-sidebar-bg:       #1a1535;
            --clr-sidebar-hover:    rgba(255, 255, 255, 0.06);
            --clr-sidebar-active:   rgba(99, 102, 241, 0.15);
            --clr-sidebar-text:     #8b8aab;
            --clr-sidebar-text-act: #6366f1;
            --clr-sidebar-section:  #4e4a72;
            --clr-sidebar-icon:     #3a3760;
            --clr-sidebar-disabled: #2a2850;
            --clr-page-bg:          #f5f4fb;
            --clr-topbar-bg:        #ffffff;
            --clr-badge-bg:         #6366f1;
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
            <a href="{{ route('customer.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                     style="width:28px;height:28px;background-color:var(--clr-primary);">
                    <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                    </svg>
                </div>
                <span class="text-white fw-bold" style="font-size:15px;letter-spacing:.02em;">{{ config('app.name', 'Travolyo') }}</span>
                <span class="badge-version">My Account</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-fill overflow-auto px-2 py-3 sidebar-scroll">
            @include('customer::components.layouts.partials.nav')
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
                    <p class="mb-0 text-truncate" style="color:var(--clr-sidebar-section);font-size:11px;">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-1 text-decoration-none border-0"
                            style="color:var(--clr-sidebar-section);"
                            title="Sign out">
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

            {{-- Start: page title slot --}}
            <div>
                @isset($header)
                    <h1 class="fw-semibold text-dark mb-0" style="font-size:16px;">{{ $header }}</h1>
                @endisset
            </div>

            {{-- End: notifications | user --}}
            <div class="d-flex align-items-center gap-3">

                {{-- Notifications --}}
                <button class="btn btn-sm btn-link text-secondary p-2 position-relative border-0" style="text-decoration:none;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="position-absolute bg-danger border border-white rounded-circle"
                          style="top:6px;inset-inline-end:6px;width:8px;height:8px;"></span>
                </button>

                <div class="vr" style="height:20px;opacity:.2;"></div>

                {{-- User --}}
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-2" style="cursor:pointer;" data-bs-toggle="dropdown" aria-expanded="false">
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
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 topbar-dropdown" style="min-width:180px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                Change Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
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
