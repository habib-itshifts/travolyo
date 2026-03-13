<?php

namespace Modules\Hotel\Providers;

use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;

interface HotelProviderInterface
{
    /** Search available hotels. B2B providers return rooms=[] here; call getRooms() separately. */
    public function search(SearchHotelDto $dto): array; // HotelOfferDto[]

    /**
     * Fetch available rooms for a specific hotel.
     * B2B providers call the rooms API; local provider returns [] (rooms already in search result).
     * Room IDs returned are opaque strings that can be passed directly to prebook().
     *
     * @return \Modules\Hotel\DTOs\HotelRoomOfferDto[]
     */
    public function getRooms(
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int    $adults,
        int    $children,
    ): array;

    /** Lock a room before payment. */
    public function prebook(PrebookHotelDto $dto): HotelOfferDto;

    /** Retrieve an existing hotel order/booking. */
    public function getOrder(string $orderId): HotelOrderDto;

    /** Cancel an existing hotel order. */
    public function cancelOrder(string $orderId): bool;
}