<?php

namespace Modules\Hotel\Providers;

use Modules\Hotel\DTOs\CheckoutHotelDto;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;

interface HotelProviderInterface
{
    /** Search available hotels */
    public function search(SearchHotelDto $dto): array; // HotelOfferDto[]

    /** Check availability and hold a specific room before payment */
    public function prebook(PrebookHotelDto $dto): HotelOfferDto;

    /** Retrieve an existing hotel order/booking */
    public function getOrder(string $orderId): HotelOrderDto;

    /** Cancel an existing hotel order */
    public function cancelOrder(string $orderId): bool;
}
