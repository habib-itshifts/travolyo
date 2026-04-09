<?php

namespace Modules\Hotel\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Hotel\Actions\CheckoutHotelAction;
use Modules\Hotel\Actions\PrebookHotelAction;
use Modules\Hotel\Actions\SearchHotelAction;
use Modules\Hotel\DTOs\CheckoutHotelDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\Resources\HotelRoomOfferResource;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Http\Requests\CheckoutHotelRequest;
use Modules\Hotel\Http\Requests\PrebookHotelRequest;
use Modules\Hotel\Http\Requests\SearchHotelRequest;
use Modules\Hotel\Providers\HotelProviderInterface;
use Modules\Hotel\Services\HotelRoomService;
use Modules\Hotel\Resources\HotelOfferResource;
use Modules\Hotel\Resources\HotelOrderResource;

class HotelController extends Controller
{


    public function search(SearchHotelRequest $request): JsonResponse
    {
      
        try {
            $dto = SearchHotelDto::fromArray($request->validated());
             
            $perPage = $dto->perPage;
            $page    = $dto->page;
            $searchCache = Cache::store('file');

            $cacheKey = 'hotel_search_' . md5(json_encode([
                $dto->destination, $dto->checkIn, $dto->checkOut,
                $dto->adults, $dto->children, $dto->rooms,
                $dto->starRating, $dto->priceMin, $dto->priceMax,
                $dto->amenityIds, $dto->currency, $dto->sortBy,
                $dto->provider?->value,
            ]));

            if ($page === 1) {
                $offers = (new SearchHotelAction)->handle($dto);
                $this->storeSearchCache($searchCache, $cacheKey, $offers);
            } else {
                $offers = $searchCache->get($cacheKey, []);

                if (empty($offers)) {
                    $offers = (new SearchHotelAction)->handle($dto);
                    $this->storeSearchCache($searchCache, $cacheKey, $offers);
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
            'currency'  => 'nullable|string|size:3',
            'provider'  => 'required|string',
        ]);

        try {
            $rooms = app(HotelRoomService::class)->getRooms(
                provider: $request->input('provider'),
                offerId:  $request->input('offer_id'),
                cityCode: $request->input('city_code', ''),
                checkIn:  $request->input('check_in'),
                checkOut: $request->input('check_out'),
                adults:   (int) $request->input('adults', 1),
                children: (int) $request->input('children', 0),
                currency: (string) $request->input('currency', 'USD'),
            );

            return response()->json([
                'success' => true,
                'count'   => count($rooms),
                'data'    => HotelRoomOfferResource::collection($rooms),
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
            $room = collect($offer->rooms)->firstWhere('roomId', $validated['room_id']);
            if (! $room) {
                $room = collect($offer->rooms)->first();
            }

            if (! $room instanceof HotelRoomOfferDto) {
                throw HotelException::roomUnavailable();
            }

            $checkoutData = [
                'offer_id'    => $validated['offer_id'],
                'room_id'     => $room->roomId,
                'provider'    => $validated['provider'],
                'hotel_name'  => $offer->name ?: $validated['hotel_name'],
                'room_name'   => $room->name ?: $validated['room_name'],
                'city'        => $offer->city ?: $validated['city'],
                'country'     => $offer->country ?: $validated['country'],
                'check_in'    => $validated['check_in'],
                'check_out'   => $validated['check_out'],
                'adults'      => (int) $validated['adults'],
                'children'    => (int) ($validated['children'] ?? 0),
                'unit_price'  => $room->baseCurrentPrice,
                'total_price' => $room->baseTotalPrice,
                'currency'    => $room->baseCurrency ?: $offer->baseCurrency,
                'deal_id'     => $room->dealId,
            ];

            $token = Str::uuid()->toString();
            Cache::put('hotel_checkout_' . $token, $checkoutData, now()->addMinutes(30));

            return response()->json([
                'success'      => true,
                'total_price'  => $checkoutData['total_price'],
                'currency'     => $checkoutData['currency'],
                'checkout_url' => url('/hotels/checkout?token=' . $token),
                'checkout_token' => $token,
            ]);

        } catch (HotelException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 502);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
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
                //  customerId:      auth()->id(),
                customerId:      Auth::id(),
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
            $order = $this->resolveOrderProvider($orderId)->getOrder($orderId);

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
            $this->resolveOrderProvider($orderId)->cancelOrder($orderId);

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

    private function resolveOrderProvider(string $orderId): HotelProviderInterface
    {
        $source = Booking::query()
            ->where('code', $orderId)
            ->where('object_model', 'hotel')
            ->value('source');

        return app(HotelRoomService::class)->resolveProviderBySource($source);
    }

    private function storeSearchCache($store, string $cacheKey, array $offers): void
    {
        try {
            $store->put($cacheKey, $offers, now()->addMinutes(15));
        } catch (\Throwable $e) {
            Log::warning('Hotel search cache write skipped.', [
                'cache_key' => $cacheKey,
                'offers_count' => count($offers),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
