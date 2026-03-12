<?php

namespace Modules\Flight\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Flight\Actions\SearchFlightAction;
use Modules\Flight\DTOs\PrebookFlightDto;
use Modules\Flight\DTOs\SearchFlightDto;
use Modules\Flight\Enums\FlightProviderEnum;
use Modules\Flight\Exceptions\FlightException;
use Modules\Flight\Http\Requests\PrebookFlightRequest;
use Modules\Flight\Http\Requests\SearchFlightRequest;
use Modules\Flight\Providers\Duffel\DuffelProvider;
use Modules\Flight\Providers\TravolyoB2BXmlAgency\TravolyoB2BXmlAgencyProvider;
use Modules\Flight\Resources\FlightOfferResource;

class FlightController extends Controller
{
    public function search(SearchFlightRequest $request): JsonResponse
    {
        try {
            $dto = new SearchFlightDto(
                origin:        strtoupper($request->input('origin')),
                destination:   strtoupper($request->input('destination')),
                departureDate: $request->input('departure_date'),
                adults:        (int) $request->input('adults', 1),
                cabinClass:    $request->input('cabin_class', 'ECONOMY'),
                returnDate:    $request->input('return_date'),
                children:      (int) $request->input('children', 0),
                infants:       (int) $request->input('infants', 0),
            );

            $offers = (new SearchFlightAction)->handle($dto);

            return response()->json([
                'success' => true,
                'count'   => count($offers),
                'data'    => FlightOfferResource::collection(collect($offers)),
            ]);

        } catch (FlightException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }
    }

    public function prebook(PrebookFlightRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $providerEnum = FlightProviderEnum::from($validated['provider']);
            $provider     = match ($providerEnum) {
                FlightProviderEnum::Duffel               => new DuffelProvider(),
                FlightProviderEnum::TravolyoB2BXmlAgency => new TravolyoB2BXmlAgencyProvider(),
            };

            $dto   = new PrebookFlightDto(
                offerId:  $validated['offer_id'],
                provider: $providerEnum,
                adults:   (int) $validated['adults'],
                children: (int) ($validated['children'] ?? 0),
                infants:  (int) ($validated['infants'] ?? 0),
            );

            $offer = $provider->prebook($dto);

            $currency = strtoupper($offer->currency);
            $symbol   = match ($currency) {
                'EUR'   => '€',
                'GBP'   => '£',
                'AED'   => 'AED ',
                default => '$',
            };

            $checkoutData = [
                'offer_id'        => $validated['offer_id'],
                'provider'        => $validated['provider'],
                'dep_iata'        => strtoupper($validated['dep_iata']),
                'arr_iata'        => strtoupper($validated['arr_iata']),
                'dep_time'        => $validated['dep_time'],
                'dep_date'        => $validated['dep_date'],
                'arr_time'        => $validated['arr_time'],
                'arr_date'        => $validated['arr_date'],
                'duration'        => $validated['duration'] ?? '',
                'stops'           => (int) $validated['stops'],
                'price'           => $offer->totalAmount,
                'currency'        => $offer->currency,
                'currency_symbol' => $symbol,
                'airline_name'    => $offer->airlineName ?: ($validated['airline_name'] ?? ''),
                'airline_logo'    => $offer->airlineLogo ?: ($validated['airline_logo'] ?? ''),
                'cabin_class'     => $offer->cabinClass,
                'trip_type'       => $validated['trip_type'],
                'adults'          => (int) $validated['adults'],
                'children'        => (int) ($validated['children'] ?? 0),
                'infants'         => (int) ($validated['infants'] ?? 0),
            ];

            $token = Str::uuid()->toString();
            Cache::put('flight_checkout_' . $token, $checkoutData, now()->addMinutes(30));

            return response()->json([
                'success'      => true,
                'price'        => $offer->totalAmount,
                'currency'     => $offer->currency,
                'checkout_url' => url('/flights/checkout?token=' . $token),
            ]);

        } catch (FlightException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }
    }

    public function checkout(): JsonResponse
    {
        //
    }

    public function pay(): JsonResponse
    {
        //
    }

    public function order(string $orderId): JsonResponse
    {
        //
    }

    public function cancel(string $orderId): JsonResponse
    {
        //
    }
}
