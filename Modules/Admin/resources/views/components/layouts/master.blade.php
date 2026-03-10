<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | {{ config('app.name', 'Travolyo') }} Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col flex-shrink-0">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-700">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-white">
                Travolyo <span class="text-indigo-400 text-sm font-medium ml-1">Admin</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @include('admin::components.layouts.partials.sidebar')
        </nav>

        {{-- User --}}
        <div class="px-4 py-4 border-t border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="text-xs text-gray-400 hover:text-white transition">Sign out</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="h-16 bg-white shadow flex items-center justify-between px-6">
            <h1 class="text-lg font-semibold text-gray-800">
                {{ $header ?? '' }}
            </h1>
            <div class="flex items-center gap-4">
                {{ $topbar ?? '' }}
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">

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
    </div>
</div>

@stack('scripts')
</body>
</html>
