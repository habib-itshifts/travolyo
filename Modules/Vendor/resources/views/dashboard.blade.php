<x-vendor::layouts.master>
    <x-slot name="title">Dashboard</x-slot>

    {{-- ── Page Header ── --}}
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">Dashboard</h1>
        <p class="text-muted small mb-0">Welcome back, {{ auth()->user()->name }}. Here's your business overview.</p>
    </div>

    {{-- ── Welcome Banner ── --}}
    <div class="position-relative overflow-hidden rounded-4 px-4 py-4 mb-4 text-white"
         style="background: linear-gradient(135deg, #14532d 0%, var(--clr-primary) 60%, #4ade80 100%);">
        <div class="position-absolute rounded-circle" style="top:-24px;inset-inline-end:-24px;width:160px;height:160px;background:rgba(255,255,255,0.05);pointer-events:none;"></div>
        <div class="position-absolute rounded-circle" style="bottom:-32px;inset-inline-end:80px;width:112px;height:112px;background:rgba(255,255,255,0.05);pointer-events:none;"></div>

        <div class="position-relative">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span aria-hidden="true" style="font-size:1.4rem;">🏪</span>
                <h2 class="fw-bold mb-0" style="font-size:1.1rem;">{{ auth()->user()->name }}'s Store</h2>
            </div>
            <p class="text-white-50 small mb-3">Manage your listings, track bookings, and grow your revenue.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="#" class="btn btn-sm d-inline-flex align-items-center gap-1 text-white fw-semibold"
                   style="background:rgba(255,255,255,0.2);border:none;font-size:12px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Listing
                </a>
                <a href="#" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 fw-semibold"
                   style="color:#15803d;font-size:12px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View Reports
                </a>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-4 mb-4">

        {{-- Revenue --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(22,163,74,0.1);">
                            <svg width="20" height="20" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(22,163,74,0.1);color:#16a34a;font-size:11px;">+8%</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">$1,240</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">Total Revenue</p>
                </div>
            </div>
        </div>

        {{-- Listings --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(13,110,253,0.08);">
                            <svg width="20" height="20" style="color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(13,110,253,0.08);color:#3b82f6;font-size:11px;">12</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">12</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">Active Listings</p>
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
                        <span class="badge fw-semibold" style="background:rgba(22,163,74,0.1);color:#16a34a;font-size:11px;">+3</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">18</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">Total Bookings</p>
                </div>
            </div>
        </div>

        {{-- Rating --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(255,193,7,0.12);">
                            <svg width="20" height="20" style="color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(255,193,7,0.15);color:#d97706;font-size:11px;">★</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">4.8</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">Avg. Rating</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Chart + Recent Bookings ── --}}
    <div class="row g-4">

        {{-- Earnings chart --}}
        <div class="col-12 col-xl-7">
            <div class="card border rounded-4 shadow-sm h-100" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1" style="font-size:14px;">Earnings Overview</h3>
                            <p class="text-muted mb-0" style="font-size:12px;">Revenue from all your listings</p>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="font-size:12px;">
                            <svg width="14" height="14" style="color:var(--clr-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Last 7 days
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div style="height:240px;position:relative;">
                        <canvas id="vendorEarningsChart"></canvas>
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
                            <h3 class="fw-bold text-dark mb-1" style="font-size:14px;">Recent Bookings</h3>
                            <p class="text-muted mb-0" style="font-size:12px;">Latest orders on your listings</p>
                        </div>
                        <a href="#" class="fw-semibold text-decoration-none" style="color:var(--clr-primary);font-size:12px;">View all &rarr;</a>
                    </div>

                    @php
                    $bookings = [
                        ['id' => 18, 'item' => 'City Tour Package',  'total' => '$210', 'status' => 'PAID',      'date' => 'Mar 12'],
                        ['id' => 17, 'item' => 'Hotel Sunrise',      'total' => '$490', 'status' => 'COMPLETED', 'date' => 'Mar 11'],
                        ['id' => 15, 'item' => 'City Tour Package',  'total' => '$210', 'status' => 'PAID',      'date' => 'Mar 10'],
                        ['id' => 14, 'item' => 'Hotel Sunrise',      'total' => '$330', 'status' => 'COMPLETED', 'date' => 'Mar 09'],
                    ];
                    @endphp

                    @foreach ($bookings as $b)
                        @php $completed = $b['status'] === 'COMPLETED'; @endphp
                        <div class="d-flex align-items-center justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}"
                             style="border-color:#f9fafb!important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                                     style="width:36px;height:36px;background:{{ $completed ? 'rgba(22,163,74,0.1)' : 'rgba(13,110,253,0.08)' }};">
                                    <svg style="width:16px;height:16px;color:{{ $completed ? '#16a34a' : '#3b82f6' }};"
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
                                <span class="fw-semibold" style="font-size:10px;color:{{ $completed ? '#16a34a' : '#3b82f6' }};">
                                    ✓ {{ $completed ? 'Completed' : 'Paid' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        new Chart(document.getElementById('vendorEarningsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Mar 06','Mar 07','Mar 08','Mar 09','Mar 10','Mar 11','Mar 12'],
                datasets: [{
                    label: 'Revenue',
                    data: [0, 0, 120, 330, 210, 490, 210],
                    backgroundColor: 'rgba(22,163,74,0.65)',
                    borderRadius: 6,
                    barPercentage: 0.55,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11, family: 'Figtree' }, boxWidth: 10, padding: 16 } },
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10, family: 'Figtree' }, color: '#9ca3af' } },
                    y: { grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { font: { size: 10, family: 'Figtree' }, color: '#9ca3af', padding: 8 } },
                },
            },
        });
    </script>
    @endpush

</x-vendor::layouts.master>
