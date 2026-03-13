<?php

namespace Modules\Hotel\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Hotel\Actions\CheckoutHotelAction;
use Modules\Hotel\Actions\PrebookHotelAction;
use Modules\Hotel\Actions\SearchHotelAction;
use Modules\Hotel\DTOs\CheckoutHotelDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Http\Requests\CheckoutHotelRequest;
use Modules\Hotel\Http\Requests\PrebookHotelRequest;
use Modules\Hotel\Http\Requests\SearchHotelRequest;
use Modules\Hotel\Providers\Local\LocalHotelProvider;
use Modules\Hotel\Providers\TravolyoB2BLocal\TravolyoB2BLocalHotelProvider;
use Modules\Hotel\Providers\TravolyoB2BNetStreaming\TravolyoB2BNetStreamingHotelProvider;
use Modules\Hotel\Resources\HotelOfferResource;
use Modules\Hotel\Resources\HotelOrderResource;

class HotelController extends Controller
{
    public function search(SearchHotelRequest $request): JsonResponse
    {
        try {
            $dto = SearchHotelDto::fromArray($request->validated());

            $offers = (new SearchHotelAction)->handle($dto);

            return response()->json([
                'success' => true,
                'count'   => count($offers),
                'data'    => HotelOfferResource::collection(collect($offers)),
            ]);

        } catch (HotelException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 502);
        }
    }

    public function prebook(PrebookHotelRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $providerEnum = HotelProviderEnum::from($validated['provider']);

            $dto = new PrebookHotelDto(
                offerId:  $validated['offer_id'],
                roomId:   $validated['room_id'],
                provider: $providerEnum,
                checkIn:  $validated['check_in'],
                checkOut: $validated['check_out'],
                adults:   (int) $validated['adults'],
                children: (int) ($validated['children'] ?? 0),
                currency: $validated['currency'] ?? 'USD',
            );

            $offer = (new PrebookHotelAction)->handle($dto);

            // Find the requested room from the offer
            $room = collect($offer->rooms)->firstWhere('roomId', $validated['room_id']);

            $checkoutData = [
                'offer_id'    => $validated['offer_id'],
                'room_id'     => $validated['room_id'],
                'provider'    => $validated['provider'],
                'hotel_name'  => $validated['hotel_name'],
                'room_name'   => $validated['room_name'],
                'city'        => $validated['city'],
                'country'     => $validated['country'],
                'check_in'    => $validated['check_in'],
                'check_out'   => $validated['check_out'],
                'adults'      => (int) $validated['adults'],
                'children'    => (int) ($validated['children'] ?? 0),
                'unit_price'  => $room?->basePrice ?? $offer->lowestPrice,
                'total_price' => $room?->totalPrice ?? $offer->lowestPrice,
                'currency'    => $offer->currency,
            ];

            $token = Str::uuid()->toString();
            Cache::put('hotel_checkout_' . $token, $checkoutData, now()->addMinutes(30));

            return response()->json([
                'success'      => true,
                'total_price'  => $checkoutData['total_price'],
                'currency'     => $checkoutData['currency'],
                'checkout_url' => url('/hotels/checkout?token=' . $token),
            ]);

        } catch (HotelException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 502);
        }
    }

    public function checkout(CheckoutHotelRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $dto = new CheckoutHotelDto(
                checkoutToken:   $validated['checkout_token'],
                firstName:       $validated['first_name'],
                lastName:        $validated['last_name'],
                email:           $validated['email'],
                phone:           $validated['phone'],
                paymentGateway:  $validated['payment_gateway'],
                specialRequests: $validated['special_requests'] ?? null,
                extraServices:   $validated['extra_services'] ?? [],
                customerId:      auth()->id(),
            );

            $result = (new CheckoutHotelAction)->handle($dto);

            return response()->json([
                'success' => true,
                'url'     => $result['url'],
            ]);

        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Could not initiate payment. Please try again.',
            ], 500);
        }
    }

    public function order(string $orderId): JsonResponse
    {
        try {
            // Determine provider from booking meta or default to local
            $provider = new LocalHotelProvider();
            $order    = $provider->getOrder($orderId);

            return response()->json([
                'success' => true,
                'data'    => new HotelOrderResource($order),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function cancel(string $orderId): JsonResponse
    {
        try {
            $provider = new LocalHotelProvider();
            $provider->cancelOrder($orderId);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
