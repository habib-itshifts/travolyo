<x-customer::layouts.master>
    <x-slot name="title">My Bookings</x-slot>

    {{-- ── Page Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold text-dark mb-1" style="font-size:1.2rem;">My Bookings</h1>
            <p class="text-muted small mb-0">All your flight and hotel bookings.</p>
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
            <a href="{{ route('customer.bookings.index', array_merge(request()->except('type','page'), ['type' => $key])) }}"
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
    <form method="GET" action="{{ route('customer.bookings.index') }}" class="d-flex gap-2 flex-wrap mb-4">
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="text" name="search" value="{{ $search }}"
               class="form-control form-control-sm rounded-3"
               style="max-width:260px;font-size:13px;" placeholder="Search booking code…">
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
            <a href="{{ route('customer.bookings.index', ['type' => $type]) }}"
               class="btn btn-sm btn-outline-secondary rounded-3" style="font-size:13px;">Clear</a>
        @endif
    </form>

    {{-- ── Bookings List ── --}}
    @if($bookings->isEmpty())
        <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
            <div class="card-body text-center py-5 text-muted">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-3 opacity-25">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="mb-0 small">You have no bookings yet.</p>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
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

                <div class="card border rounded-4 shadow-sm" style="border-color:rgba(0,0,0,0.06)!important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-3">
                                {{-- Icon --}}
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:44px;height:44px;
                                            background:{{ $isFlightBooking ? 'rgba(99,102,241,0.1)' : ($isHotelBooking ? 'rgba(245,158,11,0.1)' : 'rgba(14,165,233,0.1)') }};">
                                    @if($isFlightBooking)
                                        <svg width="20" height="20" fill="none" stroke="#4f46e5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                    @elseif($isHotelBooking)
                                        <svg width="20" height="20" fill="none" stroke="#b45309" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    @else
                                        <svg width="20" height="20" fill="none" stroke="#0369a1" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <p class="fw-bold text-dark mb-0" style="font-size:14px;">{{ $booking->code }}</p>
                                        <span class="badge rounded-pill fw-semibold"
                                              style="font-size:10px;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 text-muted flex-wrap" style="font-size:12px;">
                                        <span>
                                            @if($isFlightBooking)
                                                ✈ Flight
                                            @elseif($isHotelBooking)
                                                🏨 Hotel
                                            @else
                                                {{ ucfirst($booking->object_model ?? 'Booking') }}
                                            @endif
                                        </span>
                                        @if($booking->start_date)
                                            <span>📅 {{ $booking->start_date->format('d M Y') }}</span>
                                        @endif
                                        @if($booking->total_guests)
                                            <span>👤 {{ $booking->total_guests }} {{ Str::plural('Guest', $booking->total_guests) }}</span>
                                        @endif
                                        <span>Booked {{ $booking->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="text-end">
                                    <p class="fw-bold text-dark mb-0" style="font-size:15px;">
                                        {{ strtoupper($booking->currency ?? 'USD') }}
                                        {{ number_format($booking->total, 2) }}
                                    </p>
                                    <p class="text-muted mb-0" style="font-size:11px;">
                                        {{ $booking->is_paid ? 'Paid' : 'Balance: ' . number_format(max(0, $booking->total - $booking->paid), 2) }}
                                    </p>
                                </div>
                                <a href="{{ route('bookings.show', $booking->code) }}"
                                   class="btn btn-sm fw-semibold"
                                   style="background:rgba(14,165,233,0.1);color:#0369a1;border:none;border-radius:8px;font-size:12px;padding:6px 14px;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($bookings->hasPages())
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif

</x-customer::layouts.master>
