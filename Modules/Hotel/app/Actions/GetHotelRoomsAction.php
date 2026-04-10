<?php

namespace Modules\Hotel\Actions;

use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Providers\HotelProviderFactory;

class GetHotelRoomsAction
{
    /** @return \Modules\Hotel\DTOs\HotelRoomOfferDto[] */
    public function handle(
        HotelProviderEnum $provider,
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int    $adults,
        int    $children,
        string $currency,
    ): array {
        return HotelProviderFactory::make($provider)->getRooms(
            offerId:  $offerId,
            cityCode: $cityCode,
            checkIn:  $checkIn,
            checkOut: $checkOut,
            adults:   $adults,
            children: $children,
            currency: $currency,
        );
    }
}
