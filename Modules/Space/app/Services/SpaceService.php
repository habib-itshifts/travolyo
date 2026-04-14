<?php

namespace Modules\Space\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Space\Actions\PrebookSpaceAction;
use Modules\Space\Actions\SearchSpaceAction;
use Modules\Space\DTOs\PrebookSpaceDto;
use Modules\Space\DTOs\SearchSpaceDto;
use Modules\Space\DTOs\SpaceOfferDto;
use Modules\Space\Models\Space;

class SpaceService
{
    public function __construct(
        private SearchSpaceAction  $search,
        private PrebookSpaceAction $prebook,
    ) {}

    // -------------------------------------------------------------------------
    // Search — paginated, file-cached per unique query
    // -------------------------------------------------------------------------

    public function search(SearchSpaceDto $dto): array
    {
        $store    = Cache::store('file');
        $cacheKey = $this->searchCacheKey($dto);

        if ($dto->page === 1) {
            $all = $this->search->handle($dto);
            $this->writeSearchCache($store, $cacheKey, $all);
        } else {
            $all = $store->get($cacheKey, []);

            if (empty($all)) {
                $all = $this->search->handle($dto);
                $this->writeSearchCache($store, $cacheKey, $all);
            }
        }

        $total  = count($all);
        $items  = array_slice($all, ($dto->page - 1) * $dto->perPage, $dto->perPage);

        return [
            'items'        => $items,
            'total'        => $total,
            'per_page'     => $dto->perPage,
            'current_page' => $dto->page,
            'last_page'    => (int) ceil($total / $dto->perPage),
        ];
    }

    // -------------------------------------------------------------------------
    // Find — single active space with amenities loaded
    // -------------------------------------------------------------------------

    public function find(int $id): ?Space
    {
        return Space::with('amenities')->active()->find($id);
    }

    // -------------------------------------------------------------------------
    // Availability — calendar dates + booked ranges
    // -------------------------------------------------------------------------

    public function availability(Space $space): array
    {
        $availabilities = $space->availabilities()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get()
            ->map(fn ($a) => [
                'date'           => $a->date->toDateString(),
                'is_available'   => $a->is_available,
                'price_override' => $a->price_override,
            ]);

        $bookedRanges = $space->spaceBookings()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_out', '>=', now()->toDateString())
            ->get(['check_in', 'check_out']);

        return [
            'availabilities' => $availabilities,
            'booked_ranges'  => $bookedRanges,
        ];
    }

    // -------------------------------------------------------------------------
    // Prebook — validate + price, store checkout cache, return offer + token
    // -------------------------------------------------------------------------

    public function prebook(PrebookSpaceDto $dto): array
    {
        $offer = $this->prebook->handle($dto);
        $token = Str::uuid()->toString();

        Cache::put('space_checkout_' . $token, $this->buildCheckoutPayload($offer, $dto), now()->addMinutes(30));

        return ['offer' => $offer, 'token' => $token];
    }

    // -------------------------------------------------------------------------
    // Cancel — cancel booking + its space sub-bookings
    // -------------------------------------------------------------------------

    public function cancel(string $code): void
    {
        $booking = Booking::where('code', $code)
            ->where('object_model', 'space')
            ->firstOrFail();

        $booking->update(['status' => 'cancelled']);

        $booking->spaceBookings?->each(fn ($sb) => $sb->update(['status' => 'cancelled']));
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function searchCacheKey(SearchSpaceDto $dto): string
    {
        return 'space_search_' . md5(json_encode([
            $dto->destination, $dto->checkIn, $dto->checkOut,
            $dto->guests, $dto->type, $dto->priceMin, $dto->priceMax,
            $dto->bedrooms, $dto->bathrooms, $dto->amenities,
            $dto->currency, $dto->sortBy,
        ]));
    }

    private function writeSearchCache($store, string $key, array $offers): void
    {
        try {
            $store->put($key, $offers, now()->addMinutes(15));
        } catch (\Throwable $e) {
            Log::warning('Space search cache write skipped.', [
                'cache_key'    => $key,
                'offers_count' => count($offers),
                'message'      => $e->getMessage(),
            ]);
        }
    }

    private function buildCheckoutPayload(SpaceOfferDto $offer, PrebookSpaceDto $dto): array
    {
        return [
            'space_id'        => $offer->id,
            'space_name'      => $offer->name,
            'space_type'      => $offer->type,
            'city'            => $offer->city,
            'country'         => $offer->country,
            'address'         => $offer->address,
            'check_in'        => $dto->checkIn,
            'check_out'       => $dto->checkOut,
            'adults'          => $dto->adults,
            'children'        => $dto->children,
            'infants'         => $dto->infants,
            'guests'          => $dto->guests,
            'price_per_night' => $offer->basePricePerNight,
            'cleaning_fee'    => $offer->cleaningFee,
            'service_fee'     => $offer->serviceFee,
            'total_price'     => $offer->baseTotalPrice,
            'currency'        => $offer->baseCurrency,
        ];
    }
}
