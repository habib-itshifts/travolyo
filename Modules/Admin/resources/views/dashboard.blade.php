<x-admin::layouts.master>
    <x-slot name="title">{{ __('admin.dashboard') }}</x-slot>

    {{-- ── Page Header ──────────────────────────────────────────────── --}}
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">{{ __('admin.dashboard') }}</h1>
        <p class="text-muted small mb-0">
            {{ __('admin.dashboard_subtitle', ['name' => auth()->user()->name]) }}
        </p>
    </div>

    {{-- ── Welcome Banner ───────────────────────────────────────────── --}}
    <div class="position-relative overflow-hidden rounded-4 px-4 py-4 mb-4 text-white"
         style="background: linear-gradient(135deg, #0f6fad 0%, var(--clr-primary) 60%, #2dd4bf 100%);">

        {{-- Decorative circles --}}
        <div class="position-absolute rounded-circle" style="top:-24px;inset-inline-end:-24px;width:160px;height:160px;background:rgba(255,255,255,0.05);pointer-events:none;"></div>
        <div class="position-absolute rounded-circle" style="bottom:-32px;inset-inline-end:80px;width:112px;height:112px;background:rgba(255,255,255,0.05);pointer-events:none;"></div>
        <div class="position-absolute rounded-circle" style="top:16px;inset-inline-end:144px;width:56px;height:56px;background:rgba(255,255,255,0.1);pointer-events:none;"></div>

        <div class="position-relative">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span aria-hidden="true" style="font-size:1.4rem;">👋</span>
                <h2 class="fw-bold mb-0" style="font-size:1.1rem;">
                    {{ __('admin.welcome_title', ['email' => auth()->user()->email]) }}
                </h2>
            </div>
            <p class="text-white-50 small mb-3">{{ __('admin.welcome_subtitle') }}</p>

            <div class="d-flex flex-wrap gap-2">
                <a href="#"
                   class="btn btn-sm d-inline-flex align-items-center gap-1 text-white fw-semibold"
                   style="background:rgba(255,255,255,0.2);border:none;font-size:12px;backdrop-filter:blur(4px);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    {{ __('admin.btn_view_reports') }}
                </a>
                <a href="#"
                   class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 fw-semibold"
                   style="color:#0f6fad;font-size:12px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('admin.btn_new_booking') }}
                </a>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ────────────────────────────────────────────────── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-4 mb-4">

        {{-- Revenue --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(13,110,253,0.08);">
                            <svg width="20" height="20" style="color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(25,135,84,0.1);color:#198754;font-size:11px;">+12%</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">$3,167</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">{{ __('admin.stat_revenue') }}</p>
                </div>
            </div>
        </div>

        {{-- Earning --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(25,135,84,0.08);">
                            <svg width="20" height="20" style="color:#198754;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(108,117,125,0.1);color:#6c757d;font-size:11px;">0%</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">$0</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">{{ __('admin.stat_earning') }}</p>
                </div>
            </div>
        </div>

        {{-- Bookings --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(111,66,193,0.08);">
                            <svg width="20" height="20" style="color:#6f42c1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(25,135,84,0.1);color:#198754;font-size:11px;">+4</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">4</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">{{ __('admin.stat_bookings') }}</p>
                </div>
            </div>
        </div>

        {{-- Services --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(255,193,7,0.12);">
                            <svg width="20" height="20" style="color:#fd7e14;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(25,135,84,0.1);color:#198754;font-size:11px;">70</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">70</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">{{ __('admin.stat_services') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Chart + Recent Bookings ───────────────────────────────────── --}}
    <div class="row g-4">

        {{-- Earnings chart --}}
        <div class="col-12 col-xl-7">
            <div class="card border rounded-4 shadow-sm h-100" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1" style="font-size:14px;">{{ __('admin.chart_title') }}</h3>
                            <p class="text-muted mb-0" style="font-size:12px;">{{ __('admin.chart_subtitle') }}</p>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="font-size:12px;">
                            <svg width="14" height="14" style="color:var(--clr-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ __('admin.last_7_days') }}</span>
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div style="height:240px;position:relative;">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent bookings --}}
        <div class="col-12 col-xl-5">
            <div class="card border rounded-4 shadow-sm h-100" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1" style="font-size:14px;">{{ __('admin.recent_bookings') }}</h3>
                            <p class="text-muted mb-0" style="font-size:12px;">{{ __('admin.recent_bookings_subtitle') }}</p>
                        </div>
                        <a href="#" class="fw-semibold text-decoration-none" style="color:var(--clr-primary);font-size:12px;">
                            {{ __('admin.view_all') }} &rarr;
                        </a>
                    </div>

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
                        <div class="d-flex align-items-center justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}"
                             style="border-color:#f9fafb!important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                                     style="width:36px;height:36px;background:{{ $completed ? 'rgba(20,184,166,0.1)' : 'rgba(13,110,253,0.08)' }};">
                                    <svg style="width:16px;height:16px;color:{{ $completed ? '#14b8a6' : '#3b82f6' }};"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="fw-semibold text-dark mb-0" style="font-size:12px;">{{ $b['item'] }}</p>
                                    <p class="text-muted mb-0" style="font-size:10px;">#{{ $b['id'] }} · {{ $b['date'] }}</p>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="fw-bold text-dark mb-0" style="font-size:12px;">{{ $b['total'] }}</p>
                                <span class="fw-semibold" style="font-size:10px;color:{{ $completed ? '#14b8a6' : '#3b82f6' }};">
                                    ✓ {{ $completed ? __('admin.status_completed') : __('admin.status_paid') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
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
