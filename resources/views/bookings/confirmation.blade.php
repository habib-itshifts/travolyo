<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking {{ $bookingData['code'] }} — {{ config('app.name', 'Travolyo') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Figtree', sans-serif; background: #f4f6fb; }

        .conf-hero {
            background: linear-gradient(135deg, #0f6fad 0%, #3ab5d4 60%, #2dd4bf 100%);
            padding: 2rem 0 2.5rem;
            color: #fff;
        }
        .conf-hero__title { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.3rem; }
        .conf-hero__sub   { opacity: .85; font-size: 0.9rem; margin: 0; }

        .conf-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
            overflow: hidden;
        }
        .conf-card__head {
            padding: 0.9rem 1.2rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .conf-card__body { padding: 1.1rem 1.2rem; }
        .conf-card__foot {
            padding: 0.9rem 1.2rem;
            border-top: 1px solid #f0f0f0;
        }

        .conf-status {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 999px;
            padding: 0.25rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .conf-status--ok   { background: #ecfdf3; color: #15803d; border: 1px solid #86efac; }
        .conf-status--warn { background: #fff7ed; color: #b45309; border: 1px solid #fdba74; }
        .conf-status--fail { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; }

        .conf-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.875rem;
            padding: 0.4rem 0;
            border-bottom: 1px dashed #f1f5f9;
        }
        .conf-row:last-child { border-bottom: 0; }
        .conf-row__label { color: #6b7280; }
        .conf-row__value { color: #111827; font-weight: 600; text-align: right; }

        .conf-total {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #e5e7eb;
            margin-top: 0.5rem;
            padding-top: 0.7rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
        }

        .activity-card {
            display: flex;
            gap: .65rem;
            padding: .7rem;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            transition: all .2s ease;
        }
        .activity-card + .activity-card { margin-top: .7rem; }
        .activity-card:hover {
            border-color: #9be7f4;
            box-shadow: 0 10px 22px rgba(58, 181, 212, .12);
            transform: translateY(-1px);
        }
        .activity-card__image {
            width: 72px;
            height: 72px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            background: #eef2ff;
        }
        .activity-card__title {
            font-size: .82rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.3;
            margin-bottom: .1rem;
        }
        .activity-card__meta {
            font-size: .72rem;
            color: #6b7280;
            margin-bottom: .25rem;
        }
        .activity-card__tag {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .18rem .45rem;
            border-radius: 999px;
            background: #ecfeff;
            color: #0f766e;
            font-size: .64rem;
            font-weight: 600;
            margin-right: .25rem;
            margin-bottom: .2rem;
        }
        .activity-card__bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .55rem;
            margin-top: .25rem;
        }
        .activity-card__price {
            font-size: .66rem;
            color: #6b7280;
        }
        .activity-card__price strong {
            display: block;
            color: #17b7cf;
            font-size: .88rem;
            line-height: 1.1;
        }
        .activity-card__cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
            padding: .38rem .65rem;
            border-radius: 999px;
            background: #dff9fd;
            color: #17b7cf;
            font-size: .7rem;
            font-weight: 700;
        }
        .activity-sidebar-btn {
            width: 100%;
            margin-top: .8rem;
            border-radius: 10px;
            font-weight: 600;
            padding: .55rem .85rem;
            font-size: .82rem;
        }
    </style>
</head>
<body>

{{-- ── Hero ──────────────────────────────────────────────────────────────── --}}
<div class="conf-hero">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <div class="conf-hero__title">
                <i class="bi bi-check2-circle me-2"></i>Booking Confirmed
            </div>
            <p class="conf-hero__sub">Your hotel reservation has been submitted successfully.</p>
        </div>
        <a href="{{ url('/') }}" class="text-white text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to Home
        </a>
    </div>
</div>

{{-- ── Content ───────────────────────────────────────────────────────────── --}}
<div class="container py-4">

    @php
        $activities = $activities ?? [];
        $activitySearchParams = $activitySearchParams ?? [];
        $activitySearchUrl = route('activities.index', $activitySearchParams);
        $activityLocation = trim((string) ($activitySearchParams['city'] ?? ''));
        $statusSlug = strtolower($bookingData['status'] ?? '');
        $statusOk   = in_array($statusSlug, ['paid', 'completed', 'confirmed'], true);
        $statusFail = in_array($statusSlug, ['cancelled', 'booking_failed'], true);
        $statusClass = $statusOk ? 'conf-status--ok' : ($statusFail ? 'conf-status--fail' : 'conf-status--warn');

        $payStatus = trim((string) $bookingData['payment_status']);
        if ($payStatus === '') {
            $payStatus = $statusOk ? 'PAID' : strtoupper($statusSlug);
        } else {
            $payStatus = strtoupper($payStatus);
        }
    @endphp

    <div class="row g-4">

        {{-- ── Left column: booking overview + supplier ──────────── --}}
        <div class="col-lg-8">

            {{-- Booking Overview --}}
            <div class="conf-card mb-3">
                <div class="conf-card__head">
                    Booking Overview
                    <span class="conf-status {{ $statusClass }}">
                        <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                        {{ strtoupper($bookingData['status']) }}
                    </span>
                </div>
                <div class="conf-card__body">
                    <div class="conf-row">
                        <span class="conf-row__label">Booking Code</span>
                        <span class="conf-row__value">{{ $bookingData['code'] }}</span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Hotel</span>
                        <span class="conf-row__value">{{ $bookingData['hotel_name'] }}</span>
                    </div>
                    @if ($bookingData['hotel_address'])
                    <div class="conf-row">
                        <span class="conf-row__label">Address</span>
                        <span class="conf-row__value">{{ $bookingData['hotel_address'] }}</span>
                    </div>
                    @endif
                    <div class="conf-row">
                        <span class="conf-row__label">Room</span>
                        <span class="conf-row__value">{{ $bookingData['room_type'] }}</span>
                    </div>
                    @if ($bookingData['meal_basis'])
                    <div class="conf-row">
                        <span class="conf-row__label">Meal Plan</span>
                        <span class="conf-row__value">{{ $bookingData['meal_basis'] }}</span>
                    </div>
                    @endif
                    <div class="conf-row">
                        <span class="conf-row__label">Check-in</span>
                        <span class="conf-row__value">
                            {{ $bookingData['check_in'] ? $bookingData['check_in']->format('D, d M Y') : '-' }}
                        </span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Check-out</span>
                        <span class="conf-row__value">
                            {{ $bookingData['check_out'] ? $bookingData['check_out']->format('D, d M Y') : '-' }}
                        </span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Guests</span>
                        <span class="conf-row__value">
                            {{ $bookingData['adults'] }} Adult{{ $bookingData['adults'] > 1 ? 's' : '' }}
                            @if ($bookingData['children'] > 0)
                                , {{ $bookingData['children'] }} Child
                            @endif
                        </span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Rooms / Nights</span>
                        <span class="conf-row__value">
                            {{ $bookingData['rooms'] }} Room,
                            {{ $bookingData['nights'] }} Night{{ $bookingData['nights'] > 1 ? 's' : '' }}
                        </span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Guest Name</span>
                        <span class="conf-row__value">{{ $booking->first_name }} {{ $booking->last_name }}</span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Email</span>
                        <span class="conf-row__value">{{ $booking->email }}</span>
                    </div>
                    @if ($booking->phone)
                    <div class="conf-row">
                        <span class="conf-row__label">Phone</span>
                        <span class="conf-row__value">{{ $booking->phone }}</span>
                    </div>
                    @endif
                    @if ($booking->customer_notes)
                    <div class="conf-row">
                        <span class="conf-row__label">Special Requests</span>
                        <span class="conf-row__value">{{ $booking->customer_notes }}</span>
                    </div>
                    @endif
                </div>
                <div class="conf-card__foot d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ url('/') }}" class="btn btn-sm d-inline-flex align-items-center gap-1"
                       style="background:#3ab5d4;color:#fff;border:none;border-radius:10px;font-weight:600;padding:.55rem 1rem;">
                        <i class="bi bi-search"></i> Book Another Hotel
                    </a>
                    <a href="{{ route('bookings.show', $bookingData['code']) }}"
                       class="btn btn-sm d-inline-flex align-items-center gap-1"
                       style="background:#fff;color:#3ab5d4;border:1px solid #3ab5d4;border-radius:10px;font-weight:600;padding:.55rem 1rem;">
                        <i class="bi bi-receipt"></i> View Invoice
                    </a>
                </div>
            </div>

            {{-- Supplier Confirmation (B2B only) --}}
            @if ($bookingData['is_b2b'])
            <div class="conf-card mb-3">
                <div class="conf-card__head">Supplier Confirmation</div>
                <div class="conf-card__body">
                    <div class="conf-row">
                        <span class="conf-row__label">Supplier Status</span>
                        <span class="conf-row__value">{{ $bookingData['supplier_status'] ?: '-' }}</span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Supplier Reference</span>
                        <span class="conf-row__value">{{ $bookingData['supplier_reference'] ?: '-' }}</span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Supplier Booking Code</span>
                        <span class="conf-row__value">{{ $bookingData['supplier_booking_code'] ?: '-' }}</span>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ── Right column: payment summary ─────────────────────── --}}
        <div class="col-lg-4">
            @if (!empty($activities))
            <div class="conf-card mb-3">
                <div class="conf-card__head">Recommended Activities</div>
                <div class="conf-card__body">
                    <div class="text-muted small mb-3">Top things to do in {{ $activityLocation ?: 'your destination' }}</div>
                    @foreach ($activities as $activity)
                        @php
                            $activityImage = $activity->imageUrl ?: asset('assets/images/favicon/favicon1.png');
                            $activityCheckoutUrl = route('activities.checkout', [
                                'activity' => $activity->slug ?: $activity->dbActivityId ?: $activity->offerId,
                                'city' => $activitySearchParams['city'] ?? '',
                                'date' => $activitySearchParams['activity_date'] ?? now()->toDateString(),
                                'participants' => $activitySearchParams['participants'] ?? 1,
                            ]);
                        @endphp
                        @php
                            $selectedCurrency = strtoupper((string) session('currency', $bookingData['currency'] ?? 'USD'));
                            $activityCurrency = strtoupper((string) (
                                is_object($activity)
                                    ? $selectedCurrency
                                    : data_get($activity, 'converted_currency', data_get($bookingData, 'currency', data_get($activity, 'base_currency', 'AED')))
                            ));
                            $activityPrice = (float) (
                                is_object($activity)
                                    ? ($activity->convertedPricePerPerson
                                        ?? $activity->basePricePerPerson
                                        ?? $activity->pricePerPerson
                                        ?? $activity->price_per_person
                                        ?? $activity->price
                                        ?? 0)
                                    : data_get($activity, 'converted_price_per_person', data_get($activity, 'base_price_per_person', data_get($activity, 'pricePerPerson', data_get($activity, 'price_per_person', data_get($activity, 'price', 0)))))
                            );
                        @endphp
                        @php
                            $activityBaseCurrency = strtoupper((string) (
                                is_object($activity)
                                    ? ($activity->baseCurrency
                                        ?? 'AED')
                                    : data_get($activity, 'base_currency', 'AED')
                            ));
                            $activityBasePrice = (float) (
                                is_object($activity)
                                    ? ($activity->basePricePerPerson
                                        ?? $activity->price_per_person
                                        ?? $activity->price
                                        ?? 0)
                                    : data_get($activity, 'base_price_per_person', data_get($activity, 'price_per_person', data_get($activity, 'price', 0)))
                            );
                        @endphp
                        <a href="{{ $activityCheckoutUrl }}" class="activity-card">
                            <img src="{{ $activityImage }}" alt="{{ $activity->title }}" class="activity-card__image" loading="lazy">
                            <div class="flex-grow-1">
                                <div class="activity-card__title">{{ $activity->title }}</div>
                                <div class="activity-card__meta">
                                    {{ $activity->city ?: ($activitySearchParams['city'] ?? '-') }}{{ $activity->country ? ', ' . $activity->country : '' }}
                                </div>
                                <div>
                                    @if ($activity->category)
                                        <span class="activity-card__tag">{{ $activity->category }}</span>
                                    @endif
                                    @if ($activity->duration)
                                        <span class="activity-card__tag"><i class="bi bi-clock"></i>{{ $activity->duration }}</span>
                                    @endif
                                </div>
                                <div class="activity-card__bottom">
                                    <div class="activity-card__price">
                                        from
                                        <strong>{{ $activityCurrency }} {{ number_format($activityPrice, 2) }} @if($activityBasePrice > 0) <span class="text-muted">({{ $activityBaseCurrency }} {{ number_format($activityBasePrice, 2) }})</span>@endif</strong>
                                    </div>
                                    <span class="activity-card__cta">Book Now</span>
                                </div>
                            </div>
                        </a>
                    @endforeach

                    <a href="{{ $activitySearchUrl }}"
                       class="btn btn-outline-info d-inline-flex align-items-center justify-content-center gap-2 activity-sidebar-btn">
                        <i class="bi bi-search"></i> Explore More Activities
                    </a>
                </div>
            </div>
            @endif

            <div class="conf-card">
                <div class="conf-card__head">Payment Summary</div>
                <div class="conf-card__body">
                    @if ($bookingData['gateway'])
                    <div class="conf-row">
                        <span class="conf-row__label">Payment Method</span>
                        <span class="conf-row__value">{{ strtoupper($bookingData['gateway']) }}</span>
                    </div>
                    @endif
                    <div class="conf-row">
                        <span class="conf-row__label">Payment Status</span>
                        <span class="conf-row__value">{{ $payStatus }}</span>
                    </div>
                    @if ($bookingData['price_per_night'] > 0)
                    <div class="conf-row">
                        <span class="conf-row__label">
                            {{ $bookingData['currency'] }} {{ number_format($bookingData['price_per_night'], 2) }}
                            × {{ $bookingData['nights'] }} night{{ $bookingData['nights'] > 1 ? 's' : '' }}
                        </span>
                        <span class="conf-row__value">
                            {{ $bookingData['currency'] }} {{ number_format($bookingData['subtotal'], 2) }}
                        </span>
                    </div>
                    @endif
                    @if ($bookingData['taxes'] > 0)
                    <div class="conf-row">
                        <span class="conf-row__label">Taxes &amp; fees</span>
                        <span class="conf-row__value">
                            {{ $bookingData['currency'] }} {{ number_format($bookingData['taxes'], 2) }}
                        </span>
                    </div>
                    @endif
                    @foreach ($bookingData['extra_price_items'] as $item)
                    <div class="conf-row">
                        <span class="conf-row__label">{{ $item['name'] ?? 'Add-on' }}</span>
                        <span class="conf-row__value">
                            {{ $bookingData['currency'] }} {{ number_format($item['amount'] ?? 0, 2) }}
                        </span>
                    </div>
                    @endforeach
                    <div class="conf-total">
                        <span>Total</span>
                        <span>{{ $bookingData['currency'] }} {{ number_format($bookingData['total'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
