<x-customer::layouts.master>
    <x-slot name="title">My Account</x-slot>
    <x-slot name="header">My Account</x-slot>

    {{-- ── Page Header ── --}}
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">My Account</h1>
        <p class="text-muted small mb-0">Welcome back, {{ auth()->user()->name }}. Here's a summary of your trips.</p>
    </div>

    {{-- ── Welcome Banner ── --}}
    <div class="position-relative overflow-hidden rounded-4 px-4 py-4 mb-4 text-white"
         style="background: linear-gradient(135deg, #312e81 0%, var(--clr-primary) 60%, #a78bfa 100%);">
        <div class="position-absolute rounded-circle" style="top:-24px;inset-inline-end:-24px;width:160px;height:160px;background:rgba(255,255,255,0.05);pointer-events:none;"></div>
        <div class="position-absolute rounded-circle" style="bottom:-32px;inset-inline-end:80px;width:112px;height:112px;background:rgba(255,255,255,0.05);pointer-events:none;"></div>

        <div class="position-relative">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span aria-hidden="true" style="font-size:1.4rem;">✈️</span>
                <h2 class="fw-bold mb-0" style="font-size:1.1rem;">Ready for your next adventure?</h2>
            </div>
            <p class="text-white-50 small mb-3">Track your bookings, save your wishlist, and manage your account.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="#" class="btn btn-sm d-inline-flex align-items-center gap-1 text-white fw-semibold"
                   style="background:rgba(255,255,255,0.2);border:none;font-size:12px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    My Bookings
                </a>
                <a href="#" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 fw-semibold"
                   style="color:#4f46e5;font-size:12px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Explore
                </a>

                {{-- Become a Vendor CTA --}}
                @if(!auth()->user()->is_vendor_verified)
                    <a href="#" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1 fw-semibold"
                       style="font-size:12px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Become a Vendor
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-4 mb-4">

        {{-- Total Bookings --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(99,102,241,0.1);">
                            <svg width="20" height="20" style="color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(99,102,241,0.1);color:#6366f1;font-size:11px;">4</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">4</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">Total Bookings</p>
                </div>
            </div>
        </div>

        {{-- Upcoming Trips --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(14,165,233,0.1);">
                            <svg width="20" height="20" style="color:#0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(14,165,233,0.1);color:#0ea5e9;font-size:11px;">1</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">1</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">Upcoming Trips</p>
                </div>
            </div>
        </div>

        {{-- Wishlist --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(244,63,94,0.08);">
                            <svg width="20" height="20" style="color:#f43f5e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(244,63,94,0.08);color:#f43f5e;font-size:11px;">5</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">5</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">Wishlist Items</p>
                </div>
            </div>
        </div>

        {{-- Total Spent --}}
        <div class="col">
            <div class="card border h-100 rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:44px;height:44px;background:rgba(255,193,7,0.12);">
                            <svg width="20" height="20" style="color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="badge fw-semibold" style="background:rgba(255,193,7,0.15);color:#d97706;font-size:11px;">↑</span>
                    </div>
                    <p class="fw-bold text-dark mb-1" style="font-size:1.5rem;">$1,240</p>
                    <p class="text-uppercase text-muted fw-medium mb-0" style="font-size:10px;letter-spacing:.05em;">Total Spent</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recent Bookings ── --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1" style="font-size:14px;">My Recent Bookings</h3>
                            <p class="text-muted mb-0" style="font-size:12px;">Your latest travel activity</p>
                        </div>
                        <a href="#" class="fw-semibold text-decoration-none" style="color:var(--clr-primary);font-size:12px;">View all &rarr;</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead>
                                <tr class="text-uppercase text-muted" style="font-size:10px;letter-spacing:.05em;">
                                    <th class="border-0 fw-semibold pb-3">#</th>
                                    <th class="border-0 fw-semibold pb-3">Item</th>
                                    <th class="border-0 fw-semibold pb-3">Date</th>
                                    <th class="border-0 fw-semibold pb-3">Amount</th>
                                    <th class="border-0 fw-semibold pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $bookings = [
                                    ['id' => 7, 'item' => 'Flight: DXB - LHR', 'date' => 'Mar 10', 'total' => '$397',   'status' => 'COMPLETED'],
                                    ['id' => 5, 'item' => 'Hotel Sunrise',      'date' => 'Mar 09', 'total' => '$490',   'status' => 'PAID'],
                                    ['id' => 3, 'item' => 'City Tour Package',  'date' => 'Mar 08', 'total' => '$210',   'status' => 'PAID'],
                                    ['id' => 2, 'item' => 'Hotel Grand',        'date' => 'Mar 07', 'total' => '$143',   'status' => 'COMPLETED'],
                                ];
                                @endphp
                                @foreach ($bookings as $b)
                                    @php $completed = $b['status'] === 'COMPLETED'; @endphp
                                    <tr>
                                        <td class="text-muted">#{{ $b['id'] }}</td>
                                        <td class="fw-semibold text-dark">{{ $b['item'] }}</td>
                                        <td class="text-muted">{{ $b['date'] }}</td>
                                        <td class="fw-bold text-dark">{{ $b['total'] }}</td>
                                        <td>
                                            <span class="badge rounded-pill fw-semibold"
                                                  style="font-size:11px;background:{{ $completed ? 'rgba(20,184,166,0.1)' : 'rgba(99,102,241,0.1)' }};color:{{ $completed ? '#14b8a6' : '#6366f1' }};">
                                                ✓ {{ $completed ? 'Completed' : 'Paid' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-customer::layouts.master>
