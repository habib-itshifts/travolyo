<x-admin::layouts.master>
    <x-slot name="title">{{ __('admin.dashboard') }}</x-slot>

    {{-- ── Page Header ──────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-800">{{ __('admin.dashboard') }}</h1>
        <p class="text-sm text-gray-400 mt-0.5">
            {{ __('admin.dashboard_subtitle', ['name' => auth()->user()->name]) }}
        </p>
    </div>

    {{-- ── Welcome Banner ───────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl px-8 py-7 mb-6 text-white"
         style="background: linear-gradient(135deg, #0f6fad 0%, var(--clr-primary) 60%, #2dd4bf 100%);">

        {{-- Decorative circles (pure visual — safe to remove/restyle) --}}
        <div class="absolute -top-6 -end-6 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute -bottom-8 end-20 w-28 h-28 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute top-4 end-36 w-14 h-14 rounded-full bg-white/10 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-2xl" aria-hidden="true">👋</span>
                <h2 class="text-xl font-bold">
                    {{ __('admin.welcome_title', ['email' => auth()->user()->email]) }}
                </h2>
            </div>
            <p class="text-white/75 text-sm">{{ __('admin.welcome_subtitle') }}</p>

            <div class="flex flex-wrap gap-3 mt-4">
                <a href="#"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    {{ __('admin.btn_view_reports') }}
                </a>
                <a href="#"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white text-[#0f6fad] hover:bg-white/90 px-4 py-2 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('admin.btn_new_booking') }}
                </a>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

        {{-- Revenue --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100/80">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">+12%</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">$3,167</p>
            <p class="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wide">{{ __('admin.stat_revenue') }}</p>
        </div>

        {{-- Earning --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100/80">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">0%</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">$0</p>
            <p class="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wide">{{ __('admin.stat_earning') }}</p>
        </div>

        {{-- Bookings --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100/80">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">+4</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">4</p>
            <p class="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wide">{{ __('admin.stat_bookings') }}</p>
        </div>

        {{-- Services --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100/80">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">70</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">70</p>
            <p class="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wide">{{ __('admin.stat_services') }}</p>
        </div>
    </div>

    {{-- ── Chart + Recent Bookings ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

        {{-- Earnings chart --}}
        <div class="xl:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100/80 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">{{ __('admin.chart_title') }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('admin.chart_subtitle') }}</p>
                </div>
                <button class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5" style="color: var(--clr-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ __('admin.last_7_days') }}</span>
                    <svg class="w-3 h-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            <div class="relative h-60">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>

        {{-- Recent bookings --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100/80 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">{{ __('admin.recent_bookings') }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('admin.recent_bookings_subtitle') }}</p>
                </div>
                <a href="#" class="text-xs font-semibold hover:underline" style="color: var(--clr-primary);">
                    {{ __('admin.view_all') }} &rarr;
                </a>
            </div>

            <div class="space-y-1">
                @php
                $recentBookings = [
                    ['id' => 7, 'item' => 'Flight: DXB - LHR', 'total' => '$397',   'status' => 'COMPLETED', 'date' => 'Mar 10'],
                    ['id' => 5, 'item' => 'Hotel 1',            'total' => '$1,810', 'status' => 'PAID',      'date' => 'Mar 09'],
                    ['id' => 3, 'item' => '[Deleted]',          'total' => '$650',   'status' => 'PAID',      'date' => 'Mar 09'],
                    ['id' => 2, 'item' => 'Hotel 1',            'total' => '$310',   'status' => 'PAID',      'date' => 'Mar 09'],
                ];
                @endphp

                @foreach ($recentBookings as $b)
                    @php $completed = $b['status'] === 'COMPLETED'; @endphp
                    <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0
                                        {{ $completed ? 'bg-teal-50' : 'bg-blue-50' }}">
                                <svg class="w-4 h-4 {{ $completed ? 'text-teal-500' : 'text-blue-400' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-700">{{ $b['item'] }}</p>
                                <p class="text-[10px] text-gray-400">#{{ $b['id'] }} · {{ $b['date'] }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <p class="text-xs font-bold text-gray-800">{{ $b['total'] }}</p>
                            <span class="text-[10px] font-semibold {{ $completed ? 'text-teal-600' : 'text-blue-500' }}">
                                ✓ {{ $completed ? __('admin.status_completed') : __('admin.status_paid') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Chart script ─────────────────────────────────────────────── --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        new Chart(document.getElementById('earningsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Mar 04','Mar 05','Mar 06','Mar 07','Mar 08','Mar 09','Mar 10'],
                datasets: [
                    {
                        label: @json(__('admin.chart_revenue_label')),
                        data: [0, 0, 0, 0, 0, 2700, 380],
                        backgroundColor: 'rgba(58,181,212,0.7)',
                        borderRadius: 6,
                        barPercentage: 0.55,
                    },
                    {
                        label: @json(__('admin.chart_earning_label')),
                        data: [0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(45,212,191,0.5)',
                        borderRadius: 6,
                        barPercentage: 0.55,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11, family: 'Figtree' }, boxWidth: 10, padding: 16 },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 10, family: 'Figtree' }, color: '#9ca3af' },
                    },
                    y: {
                        grid: { color: '#f3f4f6' },
                        border: { display: false },
                        ticks: { font: { size: 10, family: 'Figtree' }, color: '#9ca3af', padding: 8 },
                    },
                },
            },
        });
    </script>
    @endpush

</x-admin::layouts.master>
