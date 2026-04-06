<?php

namespace Modules\Activity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Activity\Actions\CheckoutActivityAction;
use Modules\Activity\Actions\PrebookActivityAction;
use Modules\Activity\Actions\SearchActivityAction;
use Modules\Activity\DTOs\CheckoutActivityDto;
use Modules\Activity\DTOs\PrebookActivityDto;
use Modules\Activity\DTOs\SearchActivityDto;
use Modules\Activity\Enums\ActivityProviderEnum;
use Modules\Activity\Exceptions\ActivityException;
use Modules\Activity\Http\Requests\Api\CheckoutActivityRequest;
use Modules\Activity\Http\Requests\Api\PrebookActivityRequest;
use Modules\Activity\Http\Requests\Api\SearchActivityRequest;
use Modules\Activity\Providers\Local\LocalActivityProvider;
use Modules\Activity\Resources\ActivityOfferResource;
use Modules\Activity\Resources\ActivityOrderResource;

class ActivityController extends Controller
{
    /**
     * POST /api/activities/search
     */
    public function search(SearchActivityRequest $request): JsonResponse
    {
        try {
            $dto = SearchActivityDto::fromArray($request->validated());

            $result = (new SearchActivityAction)->handle($dto);

            return response()->json([
                'success'    => true,
                'count'      => count($result['offers']),
                'data'       => ActivityOfferResource::collection(collect($result['offers'])),
                'pagination' => $result['pagination'],
            ]);

        } catch (ActivityException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 502);
        }
    }

    /**
     * GET /api/activities/details/{offerId}
     */
    public function details(string $offerId): JsonResponse
    {
        try {
            $provider = new LocalActivityProvider();
            $offer    = $provider->getDetails($offerId);

            return response()->json([
                'success' => true,
                'data'    => new ActivityOfferResource($offer),
            ]);

        } catch (ActivityException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    /**
     * POST /api/activities/prebook
     */
    public function prebook(PrebookActivityRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $providerEnum = ActivityProviderEnum::from($validated['provider']);

            $dto = new PrebookActivityDto(
                offerId:      $validated['offer_id'],
                provider:     $providerEnum,
                activityDate: $validated['activity_date'],
                participants: (int) $validated['participants'],
                currency:     $validated['currency'] ?? 'AED',
            );

            $offer = (new PrebookActivityAction)->handle($dto);

            $participants = (int) $validated['participants'];
            $unitPrice    = $offer->basePricePerPerson;
            $totalPrice   = round($unitPrice * $participants, 2);

            $checkoutData = [
                'offer_id'          => $validated['offer_id'],
                'provider'          => $validated['provider'],
                'activity_title'    => $validated['activity_title'],
                'activity_slug'     => $offer->slug,
                'activity_image_id' => null, // resolved from offer if local
                'city'              => $validated['city'] ?? $offer->city,
                'country'           => $validated['country'] ?? $offer->country,
                'category'          => $validated['category'] ?? $offer->category,
                'duration'          => $validated['duration'] ?? $offer->duration,
                'activity_date'     => $validated['activity_date'],
                'participants'      => $participants,
                'unit_price'        => $unitPrice,
                'total_price'       => $totalPrice,
                'currency'          => $offer->baseCurrency,
            ];

            // For local provider, store the image_id for the confirmation page
            if ($offer->dbActivityId) {
                $checkoutData['activity_image_id'] = \Modules\Activity\Models\Activity::find($offer->dbActivityId)?->image_id;
            }

            $token = Str::uuid()->toString();
            Cache::put('activity_checkout_' . $token, $checkoutData, now()->addMinutes(30));

            return response()->json([
                'success'       => true,
                'total_price'   => $totalPrice,
                'unit_price'    => $unitPrice,
                'currency'      => $offer->baseCurrency,
                'checkout_token'=> $token,
                'checkout_url'  => url('/activities/checkout?token=' . $token),
            ]);

        } catch (ActivityException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 502);
        }
    }

    /**
     * POST /api/activities/checkout
     */
    public function checkout(CheckoutActivityRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $dto = new CheckoutActivityDto(
                checkoutToken:  $validated['checkout_token'],
                contactEmail:   $validated['contact_email'],
                contactPhone:   $validated['contact_phone'],
                paymentGateway: $validated['payment_gateway'],
                passengers:     $validated['passengers'],
                specialRequests:$validated['special_requests'] ?? null,
                customerId:     Auth::id(),
            );

            $result = (new CheckoutActivityAction)->handle($dto);

            return response()->json([
                'success'      => true,
                'url'          => $result['url'],
                'booking_code' => $result['booking_code'],
            ]);

        } catch (ActivityException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);
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

    /**
     * GET /api/activities/order/{orderId}
     */
    public function order(string $orderId): JsonResponse
    {
        try {
            $provider = new LocalActivityProvider();
            $order    = $provider->getOrder($orderId);

            return response()->json([
                'success' => true,
                'data'    => new ActivityOrderResource($order),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * POST /api/activities/cancel/{orderId}
     */
    public function cancel(string $orderId): JsonResponse
    {
        try {
            $provider = new LocalActivityProvider();
            $provider->cancelOrder($orderId);

            return response()->json([
                'success' => true,
                'message' => 'Activity booking cancelled successfully.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
