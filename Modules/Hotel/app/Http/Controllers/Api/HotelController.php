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
use Modules\Hotel\Providers\TravolyoB2BBaseHotelProvider;
use Modules\Hotel\Providers\Hyperguest\HyperguestHotelProvider;
use Modules\Hotel\Resources\HotelOfferResource;
use Modules\Hotel\Resources\HotelOrderResource;
use Illuminate\Http\Request;

class HotelController extends Controller
{


    public function search(SearchHotelRequest $request): JsonResponse
    {
        try {
            $dto = SearchHotelDto::fromArray($request->validated());


            $perPage = $dto->perPage;
            $page    = $dto->page;

            // Build a cache key from the search params (excluding page/perPage)
            $cacheKey = 'hotel_search_' . md5(json_encode([
                $dto->destination, $dto->checkIn, $dto->checkOut,
                $dto->adults, $dto->children, $dto->rooms,
                $dto->starRating, $dto->priceMin, $dto->priceMax,
                $dto->amenityIds, $dto->currency,$dto->displayCurrency, $dto->sortBy,
            ]));


            // On page 1 always do a fresh search; cache results for subsequent pages
            if ($page === 1) {
                $offers = (new SearchHotelAction)->handle($dto);
                Cache::put($cacheKey, $offers, now()->addMinutes(15));
            } else {
                $offers = Cache::get($cacheKey, []);

                // If cache expired, re-fetch
                if (empty($offers)) {
                    $offers = (new SearchHotelAction)->handle($dto);
                    Cache::put($cacheKey, $offers, now()->addMinutes(15));
                }
            }

            $total  = count($offers);
            $sliced = array_slice($offers, ($page - 1) * $perPage, $perPage);

            return response()->json([
                'success'      => true,
                'count'        => count($sliced),
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => (int) ceil($total / $perPage),
                'data'         => HotelOfferResource::collection(collect($sliced)),
            ]);

        } catch (HotelException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 502);
        }
    }

    
    /**
     * Fetch available rooms for a specific B2B hotel.
     * Called via AJAX from the hotel listing page when the user clicks "View Deal".
     */
    public function rooms(Request $request): JsonResponse
    {
        $request->validate([
            'offer_id'  => 'required|string',
            'city_code' => 'nullable|string',
            'check_in'  => 'required|date_format:Y-m-d',
            'check_out' => 'required|date_format:Y-m-d|after:check_in',
            'adults'    => 'required|integer|min:1',
            'children'  => 'nullable|integer|min:0',
            'provider'  => 'required|string',
        ]);

        try {
            $providerEnum = HotelProviderEnum::from($request->input('provider'));

            $provider = match ($providerEnum) {
                HotelProviderEnum::Local       => new LocalHotelProvider(),
                HotelProviderEnum::TravolyoB2B => new TravolyoB2BBaseHotelProvider(),
                HotelProviderEnum::Hyperguest  => new HyperguestHotelProvider(),
            };

            $rooms = $provider->getRooms(
                offerId:  $request->input('offer_id'),
                cityCode: $request->input('city_code', ''),
                checkIn:  $request->input('check_in'),
                checkOut: $request->input('check_out'),
                adults:   (int) $request->input('adults', 1),
                children: (int) $request->input('children', 0),
            );

            return response()->json([
                'success' => true,
                'count'   => count($rooms),
                'data'    => collect($rooms)->map(fn ($r) => [
                    'id'                => $r->roomId,
                    'name'              => $r->name,
                    'room_type'         => $r->roomType,
                    'bed_configuration' => $r->bedConfiguration,
                    'max_adults'        => $r->maxAdults,
                    'max_children'      => $r->maxChildren,
                    'base_price'        => $r->basePrice,
                    'total_price'       => $r->totalPrice,
                    'nights'            => $r->nights,
                    'currency'          => $r->currency,
                    'is_available'      => $r->isAvailable,
                    'amenities'         => $r->amenityNames,
                    'size_sqm'          => $r->sizeSqm,
                    'description'       => $r->description,
                ])->values(),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not fetch rooms: ' . $e->getMessage(),
            ], 502);
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
                'deal_id'     => $room?->dealId,  // track which deal was applied (null = no deal)
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
