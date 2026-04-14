<?php

namespace Modules\Space\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Space\Actions\BookSpaceAction;
use Modules\Space\Actions\CheckoutSpaceAction;
use Modules\Space\Actions\GetSpaceOrderAction;
use Modules\Space\Actions\SearchSpaceAction;
use Modules\Space\DTOs\BookSpaceDto;
use Modules\Space\DTOs\CheckoutSpaceDto;
use Modules\Space\DTOs\SearchSpaceDto;
use Modules\Space\Exceptions\SpaceException;
use Modules\Space\Http\Requests\BookSpaceRequest;
use Modules\Space\Http\Requests\CheckoutSpaceRequest;
use Modules\Space\Http\Requests\SearchSpaceRequest;
use Modules\Space\Models\Space;
use Modules\Space\Models\SpaceAvailability;
use Modules\Space\Resources\SpaceOfferResource;
use Modules\Space\Resources\SpaceOrderResource;

class SpaceController extends Controller
{
    public function search(SearchSpaceRequest $request): JsonResponse
    {
        try {
            $dto = SearchSpaceDto::fromArray($request->validated());

            $perPage = $dto->perPage;
            $page    = $dto->page;
            $searchCache = Cache::store('file');

            $cacheKey = 'space_search_' . md5(json_encode([
                $dto->destination, $dto->checkIn, $dto->checkOut,
                $dto->guests, $dto->type, $dto->priceMin, $dto->priceMax,
                $dto->bedrooms, $dto->bathrooms, $dto->amenities,
                $dto->currency, $dto->sortBy,
            ]));

            if ($page === 1) {
                $offers = (new SearchSpaceAction)->handle($dto);
                $this->storeSearchCache($searchCache, $cacheKey, $offers);
            } else {
                $offers = $searchCache->get($cacheKey, []);

                if (empty($offers)) {
                    $offers = (new SearchSpaceAction)->handle($dto);
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
                'data'         => SpaceOfferResource::collection(collect($sliced)),
            ]);

        } catch (SpaceException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 502);
        }
    }

    public function show(int $id): JsonResponse
    {
        $space = Space::with('amenities')->active()->find($id);

        if (! $space) {
            return response()->json([
                'success' => false,
                'message' => 'Space not found.',
            ], 404);
        }

        $images = [];
        if ($space->featured_image_url) {
            $images[] = $space->featured_image_url;
        }
        $images = array_merge($images, $space->gallery_urls ?? []);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $space->id,
                'name'                => $space->name,
                'slug'                => $space->slug,
                'type'                => $space->type,
                'description'         => $space->description,
                'short_description'   => $space->short_description,
                'max_guests'          => $space->max_guests,
                'bedrooms'            => $space->bedrooms,
                'bathrooms'           => $space->bathrooms,
                'beds'                => $space->beds,
                'city'                => $space->city,
                'country'             => $space->country,
                'address'             => $space->address,
                'latitude'            => $space->latitude,
                'longitude'           => $space->longitude,
                'check_in_time'       => $space->check_in_time,
                'check_out_time'      => $space->check_out_time,
                'min_stay_nights'     => $space->min_stay_nights,
                'max_stay_nights'     => $space->max_stay_nights,
                'cancellation_policy' => $space->cancellation_policy,
                'house_rules'         => $space->house_rules,
                'price_per_night'     => $space->price_per_night,
                'sale_price'          => $space->sale_price,
                'cleaning_fee'        => $space->cleaning_fee,
                'service_fee'         => $space->service_fee,
                'currency'            => $space->currency,
                'images'              => $images,
                'amenities'           => $space->amenities->pluck('name'),
            ],
        ]);
    }

    public function availability(int $id): JsonResponse
    {
        $space = Space::active()->find($id);

        if (! $space) {
            return response()->json([
                'success' => false,
                'message' => 'Space not found.',
            ], 404);
        }

        $availabilities = $space->availabilities()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get()
            ->map(fn ($a) => [
                'date'           => $a->date->toDateString(),
                'is_available'   => $a->is_available,
                'price_override' => $a->price_override,
            ]);

        // Also get booked dates
        $bookedRanges = $space->spaceBookings()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_out', '>=', now()->toDateString())
            ->get(['check_in', 'check_out']);

        return response()->json([
            'success'        => true,
            'availabilities'  => $availabilities,
            'booked_ranges'  => $bookedRanges,
        ]);
    }

    public function book(BookSpaceRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $adults   = max(1, (int) ($validated['adults'] ?? 1));
            $children = max(0, (int) ($validated['children'] ?? 0));
            $infants  = max(0, (int) ($validated['infants'] ?? 0));
            $guests   = max(1, $adults + $children + $infants);

            $dto = new BookSpaceDto(
                spaceId:  (int) $validated['space_id'],
                checkIn:  $validated['check_in'],
                checkOut: $validated['check_out'],
                adults:   $adults,
                children: $children,
                infants:  $infants,
                guests:   $guests,
                currency: $validated['currency'] ?? 'USD',
            );

            $offer = (new BookSpaceAction)->handle($dto);

            $checkoutData = [
                'space_id'        => $offer->id,
                'space_name'      => $offer->name,
                'space_type'      => $offer->type,
                'city'            => $offer->city,
                'country'         => $offer->country,
                'address'         => $offer->address,
                'check_in'        => $validated['check_in'],
                'check_out'       => $validated['check_out'],
                'adults'          => $adults,
                'children'        => $children,
                'infants'         => $infants,
                'guests'          => $guests,
                'price_per_night' => $offer->basePricePerNight,
                'cleaning_fee'    => $offer->cleaningFee,
                'service_fee'     => $offer->serviceFee,
                'total_price'     => $offer->baseTotalPrice,
                'currency'        => $offer->baseCurrency,
            ];

            $token = Str::uuid()->toString();
            Cache::put('space_checkout_' . $token, $checkoutData, now()->addMinutes(30));

            $baseCurrency = $checkoutData['currency'];
            $userCurrency = currency()->getUserCurrency();
            $convertedPrice = ($userCurrency !== $baseCurrency)
                ? currency($checkoutData['total_price'], $baseCurrency, $userCurrency, false)
                : $checkoutData['total_price'];

            return response()->json([
                'success'             => true,
                'total_price'         => $checkoutData['total_price'],
                'currency'            => $baseCurrency,
                'converted_price'     => $convertedPrice,
                'converted_currency'  => $userCurrency,
                'checkout_url'        => route('homes.checkout', ['token' => $token]),
                'checkout_token'      => $token,
            ]);

        } catch (SpaceException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }
    }

    public function checkout(CheckoutSpaceRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $dto = new CheckoutSpaceDto(
                checkoutToken:   $validated['checkout_token'],
                firstName:       $validated['first_name'],
                lastName:        $validated['last_name'],
                email:           $validated['email'],
                phone:           $validated['phone'],
                paymentGateway:  $validated['payment_gateway'],
                specialRequests: $validated['special_requests'] ?? null,
                extraServices:   $validated['extra_services'] ?? [],
                customerId:      Auth::id(),
            );

            $result = (new CheckoutSpaceAction)->handle($dto);

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
            $order = (new GetSpaceOrderAction)->handle($orderId);

            return response()->json([
                'success' => true,
                'data'    => new SpaceOrderResource($order),
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
            $booking = Booking::where('code', $orderId)
                ->where('object_model', 'space')
                ->firstOrFail();

            $booking->update(['status' => 'cancelled']);

            // Cancel associated space booking
            $booking->spaceBookings?->each(fn ($sb) => $sb->update(['status' => 'cancelled']));

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

    private function storeSearchCache($store, string $cacheKey, array $offers): void
    {
        try {
            $store->put($cacheKey, $offers, now()->addMinutes(15));
        } catch (\Throwable $e) {
            Log::warning('Space search cache write skipped.', [
                'cache_key' => $cacheKey,
                'offers_count' => count($offers),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
