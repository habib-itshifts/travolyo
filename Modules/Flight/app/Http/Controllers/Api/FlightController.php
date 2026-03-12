<?php

namespace Modules\Flight\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Flight\Actions\SearchFlightAction;
use Modules\Flight\DTOs\SearchFlightDto;
use Modules\Flight\Exceptions\FlightException;
use Modules\Flight\Http\Requests\SearchFlightRequest;
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

    public function prebook()//: JsonResponse
    {
        //
    }

    public function checkout()//: JsonResponse
    {
        //
    }

    public function pay()//: JsonResponse
    {
        //
    }

    public function order(string $orderId)//: JsonResponse
    {
        //
    }

    public function cancel(string $orderId)//: JsonResponse
    {
        //
    }
}
