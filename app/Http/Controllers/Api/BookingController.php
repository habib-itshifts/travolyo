<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Modules\Hotel\Actions\SearchHotelAction;
use Modules\Hotel\DTOs\SearchHotelDto;

class BookingController extends Controller
{
    public function show(string $code): JsonResponse
    {
        $booking = Booking::where('code', $code)->first();

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $data = [
            'code'         => $booking->code,
            'status'       => $booking->status,
            'object_model' => $booking->object_model,
            'total'        => $booking->total,
            'pay_now'      => $booking->pay_now,
            'paid'         => $booking->paid,
            'currency'     => $booking->currency,
            'first_name'   => $booking->first_name,
            'last_name'    => $booking->last_name,
            'email'        => $booking->email,
            'phone'        => $booking->phone,
            'created_at'   => $booking->created_at?->toISOString(),
        ];

        // Include object-specific meta + recommended hotels for flight bookings
        if ($booking->object_model === 'flight') {
            $flight                = $booking->getJsonMeta('flight_details') ?: [];
            $data['flight']        = $flight;
            $data['passengers']    = $booking->getJsonMeta('flight_passengers');
            $data['recommended_hotels'] = $this->recommendedHotels($flight, $booking->currency ?? 'USD');
        }

        return response()->json([
            'success' => true,
            'booking' => $data,
        ]);
    }

    /**
     * Search up to 3 hotels at the flight arrival city for the 2 nights after landing.
     * Returns a flat array of hotel data suitable for mobile consumption.
     */
    private function recommendedHotels(array $flight, string $currency): array
    {
        $arrIata = strtoupper(trim((string) ($flight['arr_iata'] ?? '')));
        $city    = $this->resolveAirportCity($arrIata) ?: ($flight['arr_city'] ?? '');

        if ($city === '') {
            return [];
        }

        try {
            $checkIn  = Carbon::parse($flight['arr_date'])->format('Y-m-d');
            $checkOut = Carbon::parse($flight['arr_date'])->addDays(2)->format('Y-m-d');
        } catch (\Throwable) {
            return [];
        }

        $dto = SearchHotelDto::fromArray([
            'city'      => $city,
            'check_in'  => $checkIn,
            'check_out' => $checkOut,
            'adults'    => max(1, (int) ($flight['adults'] ?? 1)),
            'children'  => max(0, (int) ($flight['children'] ?? 0)),
            'rooms'     => max(1, (int) ceil((max(1, (int) ($flight['adults'] ?? 1)) + max(0, (int) ($flight['children'] ?? 0))) / 2)),
            'currency'  => $currency,
        ]);

        $offers = collect((new SearchHotelAction())->handle($dto))->take(3);

        return $offers->map(fn ($hotel) => [
            'offer_id'        => $hotel->offerId,
            'provider'        => $hotel->provider->value,
            'name'            => $hotel->name,
            'star_rating'     => $hotel->starRating,
            'city'            => $hotel->city,
            'country'         => $hotel->country,
            'address'         => $hotel->address,
            'description'     => $hotel->shortDescription ?: $hotel->description,
            'check_in_time'   => $hotel->checkInTime,
            'check_out_time'  => $hotel->checkOutTime,
            'image'           => $hotel->images[0] ?? null,
            'amenities'       => array_slice($hotel->amenityNames ?? [], 0, 5),
            'base_price'      => $hotel->baseLowestPrice,
            'base_currency'   => $hotel->baseCurrency,
            'converted_price' => $hotel->convertedLowestPrice,
            'converted_currency' => $hotel->convertedCurrency,
            'check_in'        => $checkIn,
            'check_out'       => $checkOut,
            'adults'          => max(1, (int) ($flight['adults'] ?? 1)),
            'children'        => max(0, (int) ($flight['children'] ?? 0)),
            // Direct link to rooms page for web; mobile uses offer_id + provider to call /api/hotels/rooms
            'rooms_url'       => route('hotels.rooms', array_filter([
                'offer_id'    => $hotel->offerId,
                'provider'    => $hotel->provider->value,
                'city'        => $hotel->city ?? '',
                'country'     => $hotel->country ?? '',
                'check_in'    => $checkIn,
                'check_out'   => $checkOut,
                'adults'      => max(1, (int) ($flight['adults'] ?? 1)),
                'children'    => max(0, (int) ($flight['children'] ?? 0)),
                'hotel_name'  => $hotel->name,
                'hotel_stars' => $hotel->starRating,
            ], fn ($v) => $v !== null && $v !== '')),
        ])->values()->all();
    }

    private function resolveAirportCity(string $iataCode): string
    {
        if ($iataCode === '') {
            return '';
        }

        $file = public_path('data/worldwide-airports.json');

        if (! is_file($file)) {
            return '';
        }

        $payload = json_decode((string) file_get_contents($file), true);

        return collect(is_array($payload) ? $payload : [])
            ->filter(fn (array $a) => ! empty($a['IATA_CODE']) && ! empty($a['CITY']))
            ->firstWhere(fn (array $a) => strtoupper((string) $a['IATA_CODE']) === $iataCode)['CITY'] ?? '';
    }
}
