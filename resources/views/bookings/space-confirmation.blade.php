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
            <p class="conf-hero__sub">Your space reservation has been submitted successfully.</p>
        </div>
        <a href="{{ url('/') }}" class="text-white text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to Home
        </a>
    </div>
</div>

{{-- ── Content ───────────────────────────────────────────────────────────── --}}
<div class="container py-4">

    @php
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

        {{-- ── Left column: booking overview ──────────── --}}
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
                        <span class="conf-row__label">Property</span>
                        <span class="conf-row__value">{{ $bookingData['space_name'] }}</span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Type</span>
                        <span class="conf-row__value">{{ ucfirst($bookingData['space_type']) }}</span>
                    </div>
                    @if ($bookingData['address'])
                    <div class="conf-row">
                        <span class="conf-row__label">Address</span>
                        <span class="conf-row__value">{{ $bookingData['address'] }}</span>
                    </div>
                    @endif
                    @if ($bookingData['city'])
                    <div class="conf-row">
                        <span class="conf-row__label">Location</span>
                        <span class="conf-row__value">{{ $bookingData['city'] }}{{ $bookingData['country'] ? ', ' . $bookingData['country'] : '' }}</span>
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
                        <span class="conf-row__value">{{ $bookingData['guests'] }} Guest{{ $bookingData['guests'] > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="conf-row">
                        <span class="conf-row__label">Duration</span>
                        <span class="conf-row__value">{{ $bookingData['nights'] }} Night{{ $bookingData['nights'] > 1 ? 's' : '' }}</span>
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
                    <a href="{{ route('homes.index') }}" class="btn btn-sm d-inline-flex align-items-center gap-1"
                       style="background:#3ab5d4;color:#fff;border:none;border-radius:10px;font-weight:600;padding:.55rem 1rem;">
                        <i class="bi bi-search"></i> Book Another Home
                    </a>
                    <a href="{{ route('bookings.show', $bookingData['code']) }}"
                       class="btn btn-sm d-inline-flex align-items-center gap-1"
                       style="background:#fff;color:#3ab5d4;border:1px solid #3ab5d4;border-radius:10px;font-weight:600;padding:.55rem 1rem;">
                        <i class="bi bi-receipt"></i> View Invoice
                    </a>
                </div>
            </div>

        </div>

        {{-- ── Right column: payment summary ─────────────────────── --}}
        <div class="col-lg-4">

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
                            &times; {{ $bookingData['nights'] }} night{{ $bookingData['nights'] > 1 ? 's' : '' }}
                        </span>
                        <span class="conf-row__value">
                            {{ $bookingData['currency'] }} {{ number_format($bookingData['price_per_night'] * $bookingData['nights'], 2) }}
                        </span>
                    </div>
                    @endif
                    @if ($bookingData['cleaning_fee'] > 0)
                    <div class="conf-row">
                        <span class="conf-row__label">Cleaning fee</span>
                        <span class="conf-row__value">
                            {{ $bookingData['currency'] }} {{ number_format($bookingData['cleaning_fee'], 2) }}
                        </span>
                    </div>
                    @endif
                    @if ($bookingData['service_fee'] > 0)
                    <div class="conf-row">
                        <span class="conf-row__label">Service fee</span>
                        <span class="conf-row__value">
                            {{ $bookingData['currency'] }} {{ number_format($bookingData['service_fee'], 2) }}
                        </span>
                    </div>
                    @endif
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
