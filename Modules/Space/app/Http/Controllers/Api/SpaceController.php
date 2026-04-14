<?php

namespace Modules\Space\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Space\Actions\CheckoutSpaceAction;
use Modules\Space\Actions\GetSpaceOrderAction;
use Modules\Space\DTOs\CheckoutSpaceDto;
use Modules\Space\DTOs\PrebookSpaceDto;
use Modules\Space\DTOs\SearchSpaceDto;
use Modules\Space\Exceptions\SpaceException;
use Modules\Space\Http\Requests\CheckoutSpaceRequest;
use Modules\Space\Http\Requests\PrebookSpaceRequest;
use Modules\Space\Http\Requests\SearchSpaceRequest;
use Modules\Space\Resources\SpaceOfferResource;
use Modules\Space\Resources\SpaceOrderResource;
use Modules\Space\Services\SpaceService;

class SpaceController extends Controller
{
    public function __construct(private SpaceService $service) {}

    // -------------------------------------------------------------------------
    // Search
    // -------------------------------------------------------------------------

    public function search(SearchSpaceRequest $request): JsonResponse
    {
        try {
            $result = $this->service->search(SearchSpaceDto::fromArray($request->validated()));

            return response()->json([
                'success'      => true,
                'count'        => count($result['items']),
                'total'        => $result['total'],
                'per_page'     => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page'    => $result['last_page'],
                'data'         => SpaceOfferResource::collection(collect($result['items'])),
            ]);

        } catch (SpaceException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 502);
        }
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(int $id): JsonResponse
    {
        $space = $this->service->find($id);

        if (! $space) {
            return response()->json(['success' => false, 'message' => 'Space not found.'], 404);
        }

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
                'images'              => $space->orderedImages(),
                'amenities'           => $space->amenities->pluck('name'),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Availability
    // -------------------------------------------------------------------------

    public function availability(int $id): JsonResponse
    {
        $space = $this->service->find($id);

        if (! $space) {
            return response()->json(['success' => false, 'message' => 'Space not found.'], 404);
        }

        return response()->json(['success' => true, ...$this->service->availability($space)]);
    }

    // -------------------------------------------------------------------------
    // Prebook
    // -------------------------------------------------------------------------

    public function prebook(PrebookSpaceRequest $request): JsonResponse
    {
        try {
            $result = $this->service->prebook(PrebookSpaceDto::fromRequest($request));
            $offer  = $result['offer'];
            $token  = $result['token'];

            return response()->json([
                'success'            => true,
                'base_total_price'        => $offer->baseTotalPrice,
                'base_currency'           => $offer->baseCurrency,
                'converted_price'    => $offer->convertedTotalPrice,
                'converted_currency' => $offer->convertedCurrency,
                'checkout_url'       => route('homes.checkout', ['token' => $token]),
                'checkout_token'     => $token,
            ]);

        } catch (SpaceException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }
    }

    // -------------------------------------------------------------------------
    // Checkout
    // -------------------------------------------------------------------------

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

            return response()->json(['success' => true, 'url' => $result['url']]);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Could not initiate payment. Please try again.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Order
    // -------------------------------------------------------------------------

    public function order(string $orderId): JsonResponse
    {
        try {
            $order = (new GetSpaceOrderAction)->handle($orderId);

            return response()->json(['success' => true, 'data' => new SpaceOrderResource($order)]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // -------------------------------------------------------------------------
    // Cancel
    // -------------------------------------------------------------------------

    public function cancel(string $orderId): JsonResponse
    {
        try {
            $this->service->cancel($orderId);

            return response()->json(['success' => true, 'message' => 'Booking cancelled successfully.']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
