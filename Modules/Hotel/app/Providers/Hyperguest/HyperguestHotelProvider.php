<?php

namespace Modules\Hotel\Providers\Hyperguest;

use Carbon\Carbon;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Providers\HotelProviderInterface;

/**
 * Hyperguest hotel provider.
 *
 * Currently reads from a local JSON fixture at public/data/hyperguest/hotels.json.
 * When the live Hyperguest API credentials are available, replace loadData() with
 * an HTTP call to the Hyperguest REST API — the rest of the provider stays the same.
 */
class HyperguestHotelProvider implements HotelProviderInterface
{
    private HyperguestHotelMapper $mapper;

    /** Absolute path to the mock JSON file. */
    private string $dataPath;

    public function __construct()
    {
        $this->mapper   = new HyperguestHotelMapper();
        $this->dataPath = public_path('data/hyperguest/hotels.json');
    }

    // -------------------------------------------------------------------------
    // Interface implementation
    // -------------------------------------------------------------------------

    public function search(SearchHotelDto $dto): array
    {
        $hotels = $this->loadData();
        $nights = $dto->nights();

        return collect($hotels)
            ->filter(fn (array $hotel) => $this->matchesSearch($hotel, $dto))
            ->map(fn (array $hotel) => $this->mapper->toOfferDto($hotel, $nights, $dto->currency))
            ->filter(fn (HotelOfferDto $offer) => $this->matchesPriceFilter($offer, $dto))
            ->values()
            ->all();
    }

    /**
     * Hyperguest rooms are already embedded in the search result, so this is a
     * convenience method that re-loads the JSON and returns only the rooms for
     * the requested hotel — useful when the front-end calls /api/hotels/rooms
     * with provider=hyperguest.
     */
    public function getRooms(
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int    $adults,
        int    $children,
    ): array {
        $hotel = $this->findHotel($offerId);

        $nights = (int) Carbon::parse($checkIn)->diffInDays($checkOut);

        return collect($hotel['rooms'] ?? [])
            ->map(fn (array $room) => $this->mapper->toRoomOfferDto($room, max($nights, 1), 'USD'))
            ->values()
            ->all();
    }

    /**
     * For the JSON-based mock we simply re-read the data and return the offer.
     * When moving to the live API, call the Hyperguest "prebook" endpoint here.
     */
    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        $hotel  = $this->findHotel($dto->offerId);
        $nights = (int) Carbon::parse($dto->checkIn)->diffInDays($dto->checkOut);

        return $this->mapper->toOfferDto($hotel, max($nights, 1), $dto->currency);
    }

    /**
     * Not yet backed by the live Hyperguest API.
     * Bookings made through Hyperguest will be tracked in the local Booking table
     * with provider=hyperguest in the meta; implement getOrder() once the API is available.
     */
    public function getOrder(string $orderId): HotelOrderDto
    {
        throw new \RuntimeException('Hyperguest getOrder() is not yet implemented.');
    }

    /**
     * Not yet backed by the live Hyperguest API.
     */
    public function cancelOrder(string $orderId): bool
    {
        throw new \RuntimeException('Hyperguest cancelOrder() is not yet implemented.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** Load and decode the JSON fixture. */
    private function loadData(): array
    {
        if (! file_exists($this->dataPath)) {
            throw new \RuntimeException("Hyperguest data file not found at [{$this->dataPath}].");
        }

        $contents = file_get_contents($this->dataPath);
        $decoded  = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Hyperguest data file contains invalid JSON: ' . json_last_error_msg());
        }

        return $decoded['hotels'] ?? [];
    }

    /** Find a single hotel by hotel_id or throw. */
    private function findHotel(string $hotelId): array
    {
        $hotel = collect($this->loadData())->firstWhere('hotel_id', $hotelId);

        if (! $hotel) {
            throw HotelException::notFound(0);
        }

        return $hotel;
    }

    /** Case-insensitive city match + optional star-rating filter. */
    private function matchesSearch(array $hotel, SearchHotelDto $dto): bool
    {
        $cityMatch = str_contains(
            mb_strtolower($hotel['city'] ?? ''),
            mb_strtolower($dto->city),
        );

        if (! $cityMatch) {
            return false;
        }

        if ($dto->starRating && (int) ($hotel['star_rating'] ?? 0) !== $dto->starRating) {
            return false;
        }

        return true;
    }

    /** Apply price_min / price_max filter against the offer's lowestPrice. */
    private function matchesPriceFilter(HotelOfferDto $offer, SearchHotelDto $dto): bool
    {
        if ($dto->priceMin !== null && $offer->lowestPrice < $dto->priceMin) {
            return false;
        }

        if ($dto->priceMax !== null && $offer->lowestPrice > $dto->priceMax) {
            return false;
        }

        return true;
    }
}
