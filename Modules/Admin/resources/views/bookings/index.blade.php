<x-admin::layouts.master>
    <x-slot name="title">Bookings</x-slot>

    {{-- ── Page Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">Bookings</h1>
            <p class="text-muted small mb-0">All flight and hotel bookings on the platform.</p>
        </div>
        <span class="badge rounded-pill fw-semibold px-3 py-2"
              style="background:rgba(14,165,233,0.12);color:#0369a1;font-size:12px;">
            {{ $counts['all'] }} Total
        </span>
    </div>

    {{-- ── Type Tabs ── --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        @foreach (['all' => 'All Bookings', 'flight' => 'Flights', 'hotel' => 'Hotels'] as $key => $label)
            @php
                $count = $counts[$key];
                $isActive = $type === $key;
            @endphp
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('type','page'), ['type' => $key])) }}"
               class="btn btn-sm rounded-3 fw-semibold"
               style="font-size:12px;
                      background:{{ $isActive ? 'rgba(14,165,233,0.15)' : 'rgba(0,0,0,0.04)' }};
                      color:{{ $isActive ? '#0369a1' : '#6b7280' }};
                      border:1px solid {{ $isActive ? 'rgba(14,165,233,0.3)' : 'transparent' }};">
                {{ $label }}
                <span class="ms-1 badge rounded-pill"
                      style="font-size:10px;
                             background:{{ $isActive ? 'rgba(14,165,233,0.2)' : 'rgba(0,0,0,0.07)' }};
                             color:{{ $isActive ? '#0369a1' : '#6b7280' }};">
                    {{ $count }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- ── Search + Status Filter ── --}}
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="d-flex gap-2 flex-wrap mb-4">
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="text" name="search" value="{{ $search }}"
               class="form-control form-control-sm rounded-3"
               style="max-width:260px;font-size:13px;" placeholder="Booking code or customer…">
        <select name="status" class="form-select form-select-sm rounded-3" style="max-width:160px;font-size:13px;">
            <option value="">All Statuses</option>
            @foreach (['draft','unpaid','confirmed','completed','cancelled','booking_failed'] as $st)
                <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $st)) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-dark rounded-3" style="font-size:13px;">Filter</button>
        @if($search || $status)
            <a href="{{ route('admin.bookings.index', ['type' => $type]) }}"
               class="btn btn-sm btn-outline-secondary rounded-3" style="font-size:13px;">Clear</a>
        @endif
    </form>

    {{-- ── Table ── --}}
    <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
        <div class="card-body p-0">
            @if($bookings->isEmpty())
                <div class="text-center py-5 text-muted">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-3 opacity-25">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="mb-0 small">No bookings found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead>
                            <tr class="text-uppercase text-muted border-bottom" style="font-size:10px;letter-spacing:.05em;">
                                <th class="fw-semibold ps-4 py-3">Booking</th>
                                <th class="fw-semibold py-3">Type</th>
                                <th class="fw-semibold py-3">Customer</th>
                                <th class="fw-semibold py-3">Vendor</th>
                                <th class="fw-semibold py-3">Date</th>
                                <th class="fw-semibold py-3">Amount</th>
                                <th class="fw-semibold py-3">Status</th>
                                <th class="fw-semibold py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                @php
                                    $isFlightBooking = $booking->object_model === 'flight';
                                    $isHotelBooking  = $booking->object_model === 'hotel';
                                    $statusColors = [
                                        'draft'          => ['bg' => 'rgba(107,114,128,0.1)',  'text' => '#374151'],
                                        'unpaid'         => ['bg' => 'rgba(234,179,8,0.12)',   'text' => '#a16207'],
                                        'confirmed'      => ['bg' => 'rgba(14,165,233,0.12)',  'text' => '#0369a1'],
                                        'completed'      => ['bg' => 'rgba(22,163,74,0.12)',   'text' => '#15803d'],
                                        'paid'           => ['bg' => 'rgba(22,163,74,0.12)',   'text' => '#15803d'],
                                        'cancelled'      => ['bg' => 'rgba(239,68,68,0.12)',   'text' => '#dc2626'],
                                        'booking_failed' => ['bg' => 'rgba(239,68,68,0.12)',   'text' => '#dc2626'],
                                    ];
                                    $sc = $statusColors[$booking->status] ?? ['bg' => 'rgba(0,0,0,0.06)', 'text' => '#6b7280'];
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <p class="fw-semibold text-dark mb-0">{{ $booking->code }}</p>
                                        <p class="text-muted mb-0" style="font-size:11px;">
                                            {{ $booking->created_at->format('d M Y') }}
                                        </p>
                                    </td>
                                    <td>
                                        @if($isFlightBooking)
                                            <span class="badge rounded-pill fw-semibold"
                                                  style="font-size:11px;background:rgba(99,102,241,0.12);color:#4f46e5;">
                                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="me-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                                Flight
                                            </span>
                                        @elseif($isHotelBooking)
                                            <span class="badge rounded-pill fw-semibold"
                                                  style="font-size:11px;background:rgba(245,158,11,0.12);color:#b45309;">
                                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="me-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                Hotel
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">{{ ucfirst($booking->object_model ?? '—') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->customer)
                                            <p class="fw-semibold text-dark mb-0">{{ $booking->customer->name }}</p>
                                            <p class="text-muted mb-0" style="font-size:11px;">{{ $booking->customer->email }}</p>
                                        @elseif($booking->first_name)
                                            <p class="fw-semibold text-dark mb-0">{{ $booking->first_name }} {{ $booking->last_name }}</p>
                                            <p class="text-muted mb-0" style="font-size:11px;">{{ $booking->email }}</p>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        {{ $booking->vendor?->name ?? '—' }}
                                    </td>
                                    <td class="text-muted">
                                        {{ $booking->start_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            {{ strtoupper($booking->currency ?? 'USD') }}
                                            {{ number_format($booking->total, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill fw-semibold"
                                              style="font-size:11px;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('bookings.show', $booking->code) }}"
                                           target="_blank"
                                           class="btn btn-sm fw-semibold"
                                           style="background:rgba(14,165,233,0.1);color:#0369a1;border:none;border-radius:7px;font-size:11px;padding:4px 12px;">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($bookings->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $bookings->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-admin::layouts.master>
