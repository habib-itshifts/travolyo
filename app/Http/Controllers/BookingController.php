<?php

namespace App\Http\Controllers;

use App\Enums\BookingObjectModelEnum;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Modules\Hotel\Actions\SearchHotelAction;
use Modules\Hotel\DTOs\SearchHotelDto;

class BookingController extends Controller
{
    public function show(string $code)
    {
        $booking = Booking::with(['customer', 'vendor', 'author', 'payment'])
            ->where('code', $code)
            ->firstOrFail();

        $data = $booking->getJsonMeta('flight_details');

        $dto = SearchHotelDto::fromArray([
            'city' => $this->resolveAirportCity((string) ($data['arr_iata'] ?? '')) ?: 'Dubai',
            'check_in' => Carbon::parse($data['arr_date'])->format('Y-m-d'),
            'check_out' => Carbon::parse($data['arr_date'])->addDays(2)->format('Y-m-d'),
            'adults' => (int) ($data['adults'] ?? 1),
            'children' => (int) ($data['children'] ?? 0),
            'rooms' => $this->resolveRooms((array) $data),
        ]);

        $offers = (new SearchHotelAction())->handle($dto);

        $financials = $this->financialBreakdown($booking);

        // Flight
        if ($booking->object_model === BookingObjectModelEnum::Flight->value) {
            $passengers = $booking->getJsonMeta('flight_passengers') ?: [];
            $orderRef = $booking->getMeta('flight_pnr') ?: '';

            return view('flight::flights.confirmation', compact(
                'booking',
                'passengers',
                'orderRef',
                'financials',
                'offers'
            ));
        }

        // Hotel
        if ($booking->object_model === BookingObjectModelEnum::Hotel->value) {
            $hotel = $booking->getJsonMeta('hotel_details');
            $gateway = $booking->getMeta('payment_gateway', '');

            $nights = (isset($hotel['check_in'], $hotel['check_out']))
                ? max(1, (int) Carbon::parse($hotel['check_in'])->diffInDays($hotel['check_out']))
                : 1;

            $bookingData = [
                'code' => $booking->code,
                'status' => $booking->status,
                'payment_status' => $booking->payment?->status ?? '',
                'hotel_name' => $hotel['hotel_name'] ?? $hotel['name'] ?? '-',
                'hotel_address' => $hotel['hotel_address'] ?? $hotel['address'] ?? '',
                'room_type' => $hotel['room_name'] ?? $hotel['room_type'] ?? $hotel['room_type_name'] ?? '-',
                'meal_basis' => $hotel['meal_basis'] ?? $hotel['meal_basis_name'] ?? '',
                'check_in' => isset($hotel['check_in']) ? Carbon::parse($hotel['check_in']) : null,
                'check_out' => isset($hotel['check_out']) ? Carbon::parse($hotel['check_out']) : null,
                'adults' => (int) ($hotel['adults'] ?? 1),
                'children' => (int) ($hotel['children'] ?? 0),
                'rooms' => (int) ($hotel['rooms'] ?? 1),
                'nights' => $nights,
                'is_b2b' => ($hotel['provider'] ?? 'local') !== 'local',
                'supplier_status' => $hotel['supplier_status'] ?? '',
                'supplier_reference' => $hotel['supplier_reference'] ?? '',
                'supplier_booking_code' => $hotel['supplier_booking_code'] ?? '',
                'gateway' => $gateway,
                'price_per_night' => (float) ($hotel['unit_price'] ?? 0),
                'subtotal' => (float) ($hotel['subtotal'] ?? (($hotel['unit_price'] ?? 0) * $nights)),
                'taxes' => (float) ($hotel['taxes'] ?? 0),
                'total' => (float) ($booking->total ?? $hotel['total_price'] ?? 0),
                'currency' => strtoupper($booking->currency ?? 'USD'),
                'extra_price_items' => $hotel['extra_price_items'] ?? [],
            ];

            return view('bookings.confirmation', compact('booking', 'bookingData', 'financials'));
        }

        // Activity
        if ($booking->object_model === BookingObjectModelEnum::Activity->value) {
            return redirect()->route('activities.booking.detail', ['code' => $booking->code]);
        }

        // Fallback
        return view('bookings.show', compact('booking', 'financials'));
    }

    private function financialBreakdown(Booking $booking): array
    {
        return [
            'currency' => strtoupper($booking->currency ?? 'USD'),
            'total_before_discount' => $booking->total_before_discount,
            'coupon_amount' => $booking->coupon_amount,
            'total_before_fees' => $booking->total_before_fees,
            'buyer_fees' => $booking->buyer_fees,
            'total' => $booking->total,
            'paid' => $booking->paid,
            'balance_due' => $booking->balance_due,
            'commission_type' => $booking->commission_type,
            'commission_rate' => $booking->commission,
            'commission_amount' => $booking->commission_amount,
            'vendor_service_fee' => $booking->vendor_service_fee,
            'vendor_amount' => $booking->vendor_amount,
            'platform_earnings' => $booking->platform_earnings,
            'vendor_paid_at' => $booking->vendor_paid_at,
            'refund_status' => $booking->refund_status,
            'refund_amount' => $booking->refund_amount,
        ];
    }

    private function resolveAirportCity(string $iataCode): string
    {
        $iataCode = strtoupper(trim($iataCode));

        if ($iataCode === '') {
            return '';
        }

        static $airportCities = null;

        if ($airportCities === null) {
            $file = public_path('data/worldwide-airports.json');

            if (! is_file($file)) {
                $airportCities = [];
            } else {
                $payload = json_decode((string) file_get_contents($file), true);

                $airportCities = collect(is_array($payload) ? $payload : [])
                    ->filter(fn (array $airport) => ! empty($airport['IATA_CODE']) && ! empty($airport['CITY']))
                    ->mapWithKeys(fn (array $airport) => [
                        strtoupper((string) $airport['IATA_CODE']) => trim((string) $airport['CITY']),
                    ])
                    ->all();
            }
        }

        return $airportCities[$iataCode] ?? '';
    }

    private function resolveRooms(array $flightData): int
    {
        if (! empty($flightData['rooms'])) {
            return max(1, (int) $flightData['rooms']);
        }

        $guests = max(1, (int) ($flightData['adults'] ?? 1) + (int) ($flightData['children'] ?? 0));

        return (int) ceil($guests / 2);
    }
}
