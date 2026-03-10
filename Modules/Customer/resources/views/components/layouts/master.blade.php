<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'My Account' }} | {{ config('app.name', 'Travolyo') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- Top Navigation --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <a href="{{ route('customer.dashboard') }}" class="text-xl font-bold text-gray-900">
                Travolyo
            </a>

            <nav class="hidden md:flex items-center gap-6">
                @include('customer::components.layouts.partials.nav')
            </nav>

            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-800 transition">Sign out</button>
                </form>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @isset($header)
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">{{ $header }}</h1>
            </div>
        @endisset

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="mt-12 border-t border-gray-200 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Travolyo') }}. All rights reserved.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
