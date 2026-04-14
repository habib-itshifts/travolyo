<x-admin::layouts.master>
    <x-slot name="title">CRM Dashboard</x-slot>

    @push('styles')
        <style>
            .crm-card { background:#fff; border:0; border-radius:1rem; box-shadow:0 8px 24px rgba(15,23,42,.08); }
            .crm-card__topbar { height:4px; border-radius:999px 999px 0 0; }
            .crm-stat { min-height:124px; }
            .crm-stat__icon { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
            .crm-table thead th { font-size:10px; letter-spacing:.05em; text-transform:uppercase; color:#94a3b8; border-bottom:1px solid #eef2f7; }
            .crm-pill { display:inline-flex; align-items:center; justify-content:center; min-width:92px; padding:.45rem .75rem; border-radius:.7rem; font-size:12px; font-weight:700; }
        </style>
    @endpush

    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">Platform CRM Overview</h1>
        <p class="text-muted small mb-0">Platform-wide financial snapshot - all vendors, all bookings</p>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-3 mb-4">
        <div class="col"><div class="crm-card crm-stat h-100"><div class="crm-card__topbar" style="background:#38bdf8;"></div><div class="p-3 d-flex gap-3"><div class="crm-stat__icon" style="background:#e0f2fe;color:#0284c7;">$</div><div><div class="text-uppercase fw-bold text-muted" style="font-size:10px;">Total Revenue</div><div class="fw-bold text-dark" style="font-size:1.4rem;">{{ $stats['currency'] }} {{ number_format($stats['total_revenue'], 0) }}</div></div></div></div></div>
        <div class="col"><div class="crm-card crm-stat h-100"><div class="crm-card__topbar" style="background:#10b981;"></div><div class="p-3 d-flex gap-3"><div class="crm-stat__icon" style="background:#d1fae5;color:#059669;">%</div><div><div class="text-uppercase fw-bold text-muted" style="font-size:10px;">Total Commission</div><div class="fw-bold text-dark" style="font-size:1.4rem;">{{ $stats['currency'] }} {{ number_format($stats['total_commission'], 0) }}</div></div></div></div></div>
        <div class="col"><div class="crm-card crm-stat h-100"><div class="crm-card__topbar" style="background:#8b5cf6;"></div><div class="p-3 d-flex gap-3"><div class="crm-stat__icon" style="background:#ede9fe;color:#7c3aed;">#</div><div><div class="text-uppercase fw-bold text-muted" style="font-size:10px;">Total Bookings</div><div class="fw-bold text-dark" style="font-size:1.4rem;">{{ number_format($stats['total_bookings']) }}</div></div></div></div></div>
        <div class="col"><div class="crm-card crm-stat h-100"><div class="crm-card__topbar" style="background:#14b8a6;"></div><div class="p-3 d-flex gap-3"><div class="crm-stat__icon" style="background:#ccfbf1;color:#0f766e;">A</div><div><div class="text-uppercase fw-bold text-muted" style="font-size:10px;">Active Agents</div><div class="fw-bold text-dark" style="font-size:1.4rem;">{{ number_format($stats['active_vendors']) }}</div><div class="text-muted" style="font-size:11px;">verified vendors</div></div></div></div></div>
        <div class="col"><div class="crm-card crm-stat h-100"><div class="crm-card__topbar" style="background:#ef4444;"></div><div class="p-3 d-flex gap-3"><div class="crm-stat__icon" style="background:#fee2e2;color:#dc2626;">×</div><div><div class="text-uppercase fw-bold text-muted" style="font-size:10px;">Cancelled Bookings</div><div class="fw-bold text-dark" style="font-size:1.4rem;">{{ number_format($stats['failed_bookings']) }}</div><div class="text-muted" style="font-size:11px;">failed/cancelled</div></div></div></div></div>
    </div>

    <div class="crm-card">
        <div class="p-4 border-bottom">
            <h3 class="fw-bold mb-1" style="font-size:1rem;">Revenue Trend</h3>
            <p class="text-muted mb-0" style="font-size:12px;">Last 6 months revenue vs commission</p>
        </div>
        <div class="p-4">
            <div style="height:280px;"><canvas id="crmTrendChart"></canvas></div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            new Chart(document.getElementById('crmTrendChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($monthlyTrendLabels),
                    datasets: [
                        { label: 'Revenue', data: @json($monthlyRevenue), backgroundColor: '#38bdf8', borderRadius: 6 },
                        { label: 'Commission', data: @json($monthlyCommission), backgroundColor: '#10b981', borderRadius: 6 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: '#eef2f7' } }
                    }
                }
            });
        </script>
    @endpush
</x-admin::layouts.master>
