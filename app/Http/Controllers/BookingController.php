<?php

namespace App\Http\Controllers;

use App\Enums\BookingObjectModelEnum;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function show(string $code): View
    {
        $booking = Booking::where('code', $code)->firstOrFail();

        // ── Flight ───────────────────────────────────────────────
        if ($booking->object_model === BookingObjectModelEnum::Flight->value) {
            $passengers = $booking->getJsonMeta('flight_passengers') ?: [];
            $orderRef   = $booking->getMeta('flight_pnr') ?: '';

            return view('flight::flights.confirmation', compact('booking', 'passengers', 'orderRef'));
        }

        // ── Hotel ────────────────────────────────────────────────
        if ($booking->object_model === BookingObjectModelEnum::Hotel->value) {
            $hotel   = $booking->getJsonMeta('hotel_details');
            $gateway = $booking->getMeta('payment_gateway', '');
            $nights  = (int) ($hotel['nights'] ?? 1);

            $bookingData = [
                'code'                  => $booking->code,
                'status'                => $booking->status,
                'payment_status'        => $booking->payment?->status ?? '',
                'hotel_name'            => $hotel['hotel_name'] ?? $hotel['name'] ?? '-',
                'hotel_address'         => $hotel['hotel_address'] ?? $hotel['address'] ?? '',
                'room_type'             => $hotel['room_type'] ?? $hotel['room_type_name'] ?? '-',
                'meal_basis'            => $hotel['meal_basis'] ?? $hotel['meal_basis_name'] ?? '',
                'check_in'              => isset($hotel['check_in'])  ? Carbon::parse($hotel['check_in'])  : null,
                'check_out'             => isset($hotel['check_out']) ? Carbon::parse($hotel['check_out']) : null,
                'adults'                => (int) ($hotel['adults']   ?? 1),
                'children'              => (int) ($hotel['children'] ?? 0),
                'rooms'                 => (int) ($hotel['rooms']    ?? 1),
                'nights'                => $nights,
                'is_b2b'                => ($hotel['provider'] ?? 'local') !== 'local',
                'supplier_status'       => $hotel['supplier_status']       ?? '',
                'supplier_reference'    => $hotel['supplier_reference']    ?? '',
                'supplier_booking_code' => $hotel['supplier_booking_code'] ?? '',
                'gateway'               => $gateway,
                'price_per_night'       => (float) ($hotel['unit_price'] ?? 0),
                'subtotal'              => (float) ($hotel['subtotal']   ?? (($hotel['unit_price'] ?? 0) * $nights)),
                'taxes'                 => (float) ($hotel['taxes']      ?? 0),
                'total'                 => (float) ($booking->total      ?? $hotel['total_price'] ?? 0),
                'currency'              => strtoupper($booking->currency ?? 'USD'),
                'extra_price_items'     => $hotel['extra_price_items'] ?? [],
            ];

            return view('bookings.confirmation', compact('booking', 'bookingData'));
        }

        // ── Fallback ─────────────────────────────────────────────
        return view('bookings.show', compact('booking'));
    }
}
