<x-admin::layouts.master>
    <x-slot name="title">{{ __('admin.dashboard') }}</x-slot>

    @push('styles')
        <style>
            .dash-hero { background: linear-gradient(135deg, #0f6fad 0%, var(--clr-primary) 60%, #2dd4bf 100%); }
            .dash-metric-card,
            .dash-breakdown-card { background: #fff; overflow: hidden; border: 0; border-radius: 1rem; box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08); }
            .dash-metric-card { min-height: 120px; }
            .dash-breakdown-card { min-height: 128px; }
            .dash-topbar { height: 4px; }
            .dash-metric-card__body { padding: .5rem; }
            .dash-breakdown-card__body { padding: .5rem; }
            .dash-metric-card__head,
            .dash-breakdown-card__head { display: flex; align-items: flex-start; justify-content: space-between; gap: .5rem; }
            .dash-metric-card__left,
            .dash-breakdown-card__left { display: flex; align-items: center; gap: .55rem; min-width: 0; }
            .dash-metric-card__icon,
            .dash-breakdown-card__icon { width: 34px; height: 34px; border-radius: .75rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
            .dash-metric-card__icon {
                width: 36px;
                height: 36px;
                border-radius: .75rem;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .dash-metric-card__label,
            .dash-breakdown-card__label { font-size: 9px; line-height: 1; letter-spacing: .08em; text-transform: uppercase; font-weight: 700; color: #6b7280; }
            .dash-metric-card__value,
            .dash-breakdown-card__value { font-size: 1rem; line-height: 1; font-weight: 800; color: #111827; white-space: nowrap; }
            .dash-metric-card__subtext,
            .dash-breakdown-card__footer { margin-top: .3rem; font-size: 11px; line-height: 1.15; color: #6b7280; }
            .dash-breakdown-card__progress { height: 4px; background: #eef2f7; }

            @media (max-width: 1199.98px) {
                .dash-metric-card { min-height: 108px; }
                .dash-breakdown-card { min-height: 116px; }
            }

            @media (max-width: 767.98px) {
                .dash-metric-card__value,
                .dash-breakdown-card__value { font-size: .94rem; }
                .dash-metric-card__icon,
                .dash-breakdown-card__icon { width: 30px; height: 30px; }
                .dash-metric-card__body,
                .dash-breakdown-card__body { padding: .45rem; }
            }

        </style>
    @endpush

    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">{{ __('admin.dashboard') }}</h1>
        <p class="text-muted small mb-0">
            {{ __('admin.dashboard_subtitle', ['name' => auth()->user()->name]) }}
        </p>
    </div>

    <div class="position-relative overflow-hidden rounded-4 px-4 py-4 mb-4 text-white dash-hero">
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
                <a href="{{ route('admin.bookings.index') }}"
                   class="btn btn-sm d-inline-flex align-items-center gap-1 text-white fw-semibold"
                   style="background:rgba(255,255,255,0.2);border:none;font-size:12px;backdrop-filter:blur(4px);">
                    {{ __('admin.btn_view_reports') }}
                </a>
                <a href="{{ route('admin.bookings.index') }}"
                   class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 fw-semibold"
                   style="color:#0f6fad;font-size:12px;">
                    {{ __('admin.btn_new_booking') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row dash-metric-grid row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-2 mb-4">
        <div class="col">
            <div class="dash-metric-card h-100">
                <div class="dash-topbar" style="background:#3b82f6;"></div>
                <div class="dash-metric-card__body">
                    <div class="dash-metric-card__head">
                        <div class="dash-metric-card__left">
                            <div class="dash-metric-card__icon" style="background:rgba(59,130,246,0.10);color:#3b82f6;">
                                <span style="font-size:16px;line-height:1;">▥</span>
                            </div>
                            <div>
                                <div class="dash-metric-card__label">Revenue</div>
                                <div class="dash-metric-card__value">{{ $stats['currency'] ?? 'USD' }} {{ number_format($stats['revenue'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="dash-metric-card__subtext">Completed bookings only</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="dash-metric-card h-100">
                <div class="dash-topbar" style="background:#10b981;"></div>
                <div class="dash-metric-card__body">
                    <div class="dash-metric-card__head">
                        <div class="dash-metric-card__left">
                            <div class="dash-metric-card__icon" style="background:rgba(16,185,129,0.10);color:#10b981;">
                                <span style="font-size:16px;line-height:1;">✦</span>
                            </div>
                            <div>
                                <div class="dash-metric-card__label">Profit</div>
                                <div class="dash-metric-card__value">{{ $stats['currency'] ?? 'USD' }} {{ number_format($stats['profit'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="dash-metric-card__subtext">Margin {{ number_format($stats['profit_margin'] ?? 0, 1) }}%</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="dash-metric-card h-100">
                <div class="dash-topbar" style="background:#8b5cf6;"></div>
                <div class="dash-metric-card__body">
                    <div class="dash-metric-card__head">
                        <div class="dash-metric-card__left">
                            <div class="dash-metric-card__icon" style="background:rgba(139,92,246,0.10);color:#8b5cf6;">
                                <span style="font-size:16px;line-height:1;">▣</span>
                            </div>
                            <div>
                                <div class="dash-metric-card__label">Bookings</div>
                                <div class="dash-metric-card__value">{{ number_format($stats['bookings'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="dash-metric-card__subtext">{{ number_format($stats['pending_bookings'] ?? 0) }} pending</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="dash-metric-card h-100">
                <div class="dash-topbar" style="background:#64748b;"></div>
                <div class="dash-metric-card__body">
                    <div class="dash-metric-card__head">
                        <div class="dash-metric-card__left">
                            <div class="dash-metric-card__icon" style="background:rgba(100,116,139,0.10);color:#64748b;">
                                <span style="font-size:16px;line-height:1;">×</span>
                            </div>
                            <div>
                                <div class="dash-metric-card__label">Cancelled</div>
                                <div class="dash-metric-card__value">{{ number_format($stats['cancelled_bookings'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="dash-metric-card__subtext">Bookings cancelled</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="dash-metric-card h-100">
                <div class="dash-topbar" style="background:#f59e0b;"></div>
                <div class="dash-metric-card__body">
                    <div class="dash-metric-card__head">
                        <div class="dash-metric-card__left">
                            <div class="dash-metric-card__icon" style="background:rgba(245,158,11,0.10);color:#f59e0b;">
                                <span style="font-size:16px;line-height:1;">⚡</span>
                            </div>
                            <div>
                                <div class="dash-metric-card__label">Pending</div>
                                <div class="dash-metric-card__value">{{ number_format($stats['pending_bookings'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="dash-metric-card__subtext">Awaiting confirmation</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="dash-metric-card h-100">
                <div class="dash-topbar" style="background:#ef4444;"></div>
                <div class="dash-metric-card__body">
                    <div class="dash-metric-card__head">
                        <div class="dash-metric-card__left">
                            <div class="dash-metric-card__icon" style="background:rgba(239,68,68,0.10);color:#ef4444;">
                                <span style="font-size:16px;line-height:1;">⌁</span>
                            </div>
                            <div>
                                <div class="dash-metric-card__label">Failed</div>
                                <div class="dash-metric-card__value">{{ number_format($stats['failed_payments'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="dash-metric-card h-100">
                <div class="dash-topbar" style="background:#1e293b;"></div>
                <div class="dash-metric-card__body">
                    <div class="dash-metric-card__head">
                        <div class="dash-metric-card__left">
                            <div class="dash-metric-card__icon" style="background:rgba(30,41,59,0.10);color:#1e293b;">
                                <span style="font-size:12px;line-height:1;">▲</span>
                            </div>
                            <div>
                                <div class="dash-metric-card__label">Active Vendors</div>
                                <div class="dash-metric-card__value">{{ number_format($stats['total_vendors'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="dash-metric-card__subtext">{{ number_format($stats['pending_vendors'] ?? 0) }} pending approval</div>
                </div>
            </div>
        </div>

    </div>

    <div class="mb-4">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div>
                <h3 class="fw-bold text-dark mb-1" style="font-size:1.05rem;">Revenue Breakdown</h3>
                <p class="text-muted mb-0" style="font-size:12px;">Excluding cancelled bookings · All converted to {{ $stats['currency'] ?? 'AED' }}</p>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3">
            @foreach (['hotel', 'activity', 'flight', 'profit'] as $key)
                @php
                    $item = $revenueBreakdown[$key] ?? null;
                    if (! $item) continue;
                    $topColor = $item['color'] ?? '#3b82f6';
                    $icon = $item['icon'] ?? 'profit';
                @endphp
                <div class="col">
                    <div class="dash-breakdown-card h-100">
                        <div class="dash-topbar" style="background:{{ $topColor }};"></div>
                        <div class="dash-breakdown-card__body">
                            <div class="d-flex align-items-start justify-content-between mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dash-breakdown-card__icon" style="background:{{ $topColor }}15;color:{{ $topColor }};">
                                        @if ($icon === 'hotel')
                                            <span style="font-size:15px;line-height:1;">🏨</span>
                                        @elseif ($icon === 'activity')
                                            <span style="font-size:15px;line-height:1;">🎢</span>
                                        @elseif ($icon === 'flight')
                                            <span style="font-size:15px;line-height:1;">✈</span>
                                        @else
                                            <span style="font-size:15px;line-height:1;">◆</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="dash-breakdown-card__label">{{ $item['label'] }}</div>
                                        <div class="dash-breakdown-card__value">{{ $stats['currency'] ?? 'AED' }} {{ number_format($item['amount'] ?? 0, 0) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-1">
                                <div class="progress dash-breakdown-card__progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ min(100, max(0, (float) ($item['share'] ?? 0))) }}%; background: {{ $topColor }};"></div>
                                </div>
                            </div>

                            <div class="dash-breakdown-card__footer">
                                @if ($key !== 'profit')
                                    <span>{{ number_format($item['count'] ?? 0) }} bookings · {{ number_format($item['share'] ?? 0, 1) }}% of total</span>
                                @else
                                    <span>{{ $item['meta'] ?? ('Margin ' . number_format($item['share'] ?? 0, 1) . '%') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 6-Month Trends Section -->
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">6-Month Trends</h3>
                <p class="text-muted mb-0" style="font-size: 11px;">{{ $monthlyTrendsLabels[0] ?? '' }} – {{ end($monthlyTrendsLabels) ?? '' }}</p>
            </div>
        </div>

        <div class="row g-4">
            {{-- Chart 1: Booking Volume --}}
            <div class="col-12 col-xl-6">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div>
                                <h4 class="fw-bold mb-0" style="font-size: 1rem;">Booking Volume</h4>
                                <p class="text-muted mb-0" style="font-size: 11px;">Hotel, activity & flight monthly counts</p>
                            </div>
                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-1" style="font-size: 10px;">Live</span>
                        </div>
                        <div style="height: 220px; position: relative;">
                            <canvas id="bookingVolumeChart"></canvas>
                        </div>
                        <div class="row g-2 mt-4">
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 9px;">Total Bookings</div>
                                    <div class="fw-bold text-dark" style="font-size: 1.1rem;">{{ number_format($stats['bookings'] ?? 0) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 9px;">Pending</div>
                                    <div class="fw-bold text-dark" style="font-size: 1.1rem;">{{ number_format($stats['pending_bookings'] ?? 0) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 9px;">Active Inventory</div>
                                    <div class="fw-bold text-dark" style="font-size: 1.1rem;">{{ number_format($summary['total_items'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart 2: Revenue Trend --}}
            <div class="col-12 col-xl-6">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div>
                                <h4 class="fw-bold mb-0" style="font-size: 1rem;">Revenue Trend</h4>
                                <p class="text-muted mb-0" style="font-size: 11px;">Combined hotel, activity & flight revenue ({{ $stats['currency'] }})</p>
                            </div>
                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-1" style="font-size: 10px;">Live</span>
                        </div>
                        <div style="height: 220px; position: relative;">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                        <div class="row g-2 mt-4">
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 9px;">Total Revenue</div>
                                    <div class="fw-bold text-dark" style="font-size: 1rem;">{{ $stats['currency'] }} {{ number_format($stats['revenue'] ?? 0, 0) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 9px;">Total Profit</div>
                                    <div class="fw-bold text-dark" style="font-size: 1rem;">{{ $stats['currency'] }} {{ number_format($stats['profit'] ?? 0, 0) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 9px;">Margin</div>
                                    <div class="fw-bold text-dark" style="font-size: 1.1rem;">{{ number_format($stats['profit_margin'] ?? 0, 1) }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings & Recent Bookings Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card border rounded-4 shadow-sm h-100" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1" style="font-size:14px;">{{ __('admin.chart_title') }}</h3>
                            <p class="text-muted mb-0" style="font-size:12px;">{{ __('admin.chart_subtitle') }}</p>
                        </div>
                        <span class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="font-size:12px;">
                            {{ __('admin.last_7_days') }}
                        </span>
                    </div>
                    <div style="height:240px;position:relative;">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card border rounded-4 shadow-sm h-100" style="border-color:rgba(0,0,0,0.06)!important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1" style="font-size:14px;">{{ __('admin.recent_bookings') }}</h3>
                            <p class="text-muted mb-0" style="font-size:12px;">{{ __('admin.recent_bookings_subtitle') }}</p>
                        </div>
                        <a href="{{ route('admin.bookings.index') }}" class="fw-semibold text-decoration-none" style="color:var(--clr-primary);font-size:12px;">
                            {{ __('admin.view_all') }} &rarr;
                        </a>
                    </div>

                    @forelse ($recentBookings as $booking)
                        <div class="d-flex align-items-center justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:#f9fafb!important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width:36px;height:36px;background:rgba(20,184,166,0.1);">
                                    <svg style="width:16px;height:16px;color:#14b8a6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="fw-semibold text-dark mb-0" style="font-size:12px;">{{ $booking['item'] }}</p>
                                    <p class="text-muted mb-0" style="font-size:10px;">#{{ $booking['id'] }} · {{ $booking['date'] }}</p>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="fw-bold text-dark mb-0" style="font-size:12px;">{{ $booking['currency'] ?? ($stats['currency'] ?? 'USD') }} {{ $booking['total'] }}</p>
                                @php
                                    $rawStatus = strtolower($booking['status']);
                                    $statusColor = match($rawStatus) {
                                        'completed', 'paid', 'confirmed' => '#14b8a6',
                                        'pending', 'processing', 'draft' => '#f59e0b',
                                        'cancelled', 'failed' => '#ef4444',
                                        default => '#6b7280'
                                    };
                                    // Draft ko bhi Pending dikhana hai
                                    $statusLabel = ($rawStatus == 'draft') ? 'Pending' : ucfirst($booking['status']);
                                @endphp
                                <span class="fw-semibold" style="font-size:10px; color: {{ $statusColor }};">
                                    {{ strtolower($booking['status']) == 'completed' ? '✓' : '●' }} {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No bookings found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Status & Payment Methods (At the very end) -->
    <div class="mb-5">
        <div class="row g-4">
            <div class="col-12 col-xl-7">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h3 class="fw-bold text-dark mb-0" style="font-size:1rem;">Booking Status Breakdown</h3>
                            <span class="badge rounded-pill px-3 py-2" style="background:#e0f2fe;color:#0369a1;font-size:11px;">{{ number_format($stats['bookings'] ?? 0) }} total</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr style="font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #f1f5f9;">
                                        <th class="border-0 ps-0 py-3">Service</th>
                                        <th class="border-0 text-center py-3">Pending</th>
                                        <th class="border-0 text-center py-3">Confirmed</th>
                                        <th class="border-0 text-center py-3">Cancelled</th>
                                        <th class="border-0 text-center pe-0 py-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bookingStatusBreakdown ?? [] as $row)
                                        <tr>
                                            <td class="ps-0 py-3 fw-semibold text-dark" style="font-size:13px;">
                                                <span class="me-2">{{ match ($row['icon']) {
                                                    'hotel' => '🏨',
                                                    'activity' => '🎢',
                                                    'flight' => '✈',
                                                    default => '•',
                                                } }}</span>
                                                {{ $row['label'] }}
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="badge rounded-pill px-2 py-1" style="background:#fef3c7;color:#92400e;font-size:11px;">{{ number_format($row['pending']) }}</span>
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="badge rounded-pill px-2 py-1" style="background:#d1fae5;color:#065f46;font-size:11px;">{{ number_format($row['confirmed']) }}</span>
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="badge rounded-pill px-2 py-1" style="background:#fee2e2;color:#991b1b;font-size:11px;">{{ number_format($row['cancelled']) }}</span>
                                            </td>
                                            <td class="text-center pe-0 py-3">
                                                <span class="badge rounded-pill px-2 py-1" style="background:#f1f5f9;color:#475569;font-size:11px;">{{ number_format($row['total']) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-muted py-4 text-center">No service data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h3 class="fw-bold text-dark mb-0" style="font-size:1rem;">Payment Methods</h3>
                            <span class="badge rounded-pill px-3 py-2" style="background:#f1f5f9;color:#475569;font-size:11px;">Gateways</span>
                        </div>

                        @foreach ($paymentMethods ?? [] as $method)
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle" style="width:8px;height:8px;background:{{ $method['color'] }};"></span>
                                        <div class="fw-semibold text-dark" style="font-size:13px;">{{ $method['label'] }}</div>
                                    </div>
                                    <div class="fw-bold text-dark" style="font-size:13px;">{{ number_format($method['share'] ?? 0, 1) }}%</div>
                                </div>
                                <div class="progress" style="height:6px;background:#f1f5f9;border-radius:10px;">
                                    <div class="progress-bar" role="progressbar" style="width:{{ $method['share'] }}%;background:{{ $method['color'] }};border-radius:10px;"></div>
                                </div>
                                <div class="mt-1 text-muted" style="font-size:11px;">{{ number_format($method['count']) }} transactions</div>
                            </div>
@endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            const dashboardCurrency = @json($stats['currency'] ?? 'USD');
            const trendsLabels = @json($monthlyTrendsLabels);

            // Chart 1: Booking Volume (Stacked Bar)
            new Chart(document.getElementById('bookingVolumeChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: trendsLabels,
                    datasets: [
                        { label: 'Hotels', data: @json($monthlyBookingVolume['hotel']), backgroundColor: '#0ea5e9', borderRadius: 4 },
                        { label: 'Activities', data: @json($monthlyBookingVolume['activity']), backgroundColor: '#334155', borderRadius: 4 },
                        { label: 'Flights', data: @json($monthlyBookingVolume['flight']), backgroundColor: '#f59e0b', borderRadius: 4 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 10 } } } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });

            // Chart 2: Revenue Trend (Grouped Bar)
            new Chart(document.getElementById('revenueTrendChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: trendsLabels,
                    datasets: [
                        { label: 'Hotel', data: @json($monthlyRevenueTrend['hotel']), backgroundColor: '#0ea5e9', borderRadius: 4 },
                        { label: 'Activity', data: @json($monthlyRevenueTrend['activity']), backgroundColor: '#14b8a6', borderRadius: 4 },
                        { label: 'Flight', data: @json($monthlyRevenueTrend['flight']), backgroundColor: '#f59e0b', borderRadius: 4 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 10 } } } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });

            // Chart 3: Earnings Chart (Last 7 Days)
            new Chart(document.getElementById('earningsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: @json(__('admin.chart_revenue_label')),
                            data: @json($chartRevenue),
                            backgroundColor: 'rgba(58,181,212,0.7)',
                            borderRadius: 6,
                            barPercentage: 0.55,
                        },
                        {
                            label: @json(__('admin.chart_earning_label')),
                            data: @json($chartEarnings),
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
                        tooltip: {
                            callbacks: {
                                label: (context) => `${dashboardCurrency} ${Number(context.parsed.y || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`,
                            },
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
