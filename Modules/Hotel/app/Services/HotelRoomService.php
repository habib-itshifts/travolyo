<?php

namespace Modules\Hotel\Services;

use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Providers\HotelProviderInterface;
use Modules\Hotel\Providers\Hyperguest\HyperguestHotelProvider;
use Modules\Hotel\Providers\Local\LocalHotelProvider;
use Modules\Hotel\Providers\TravolyoB2B\TravolyoB2BHotelProvider;

class HotelRoomService
{
    public function resolveProvider(HotelProviderEnum $provider): HotelProviderInterface
    {
        return match ($provider) {
            HotelProviderEnum::Local       => new LocalHotelProvider(),
            HotelProviderEnum::TravolyoB2B => new TravolyoB2BHotelProvider(),
            HotelProviderEnum::Hyperguest  => new HyperguestHotelProvider(),
        };
    }

    public function resolveProviderBySource(?string $source): HotelProviderInterface
    {
        return match ($source) {
            HotelProviderEnum::TravolyoB2B->value => new TravolyoB2BHotelProvider(),
            HotelProviderEnum::Hyperguest->value  => new HyperguestHotelProvider(),
            default                               => new LocalHotelProvider(),
        };
    }

    /**
     * @return \Modules\Hotel\DTOs\HotelRoomOfferDto[]
     */
    public function getRooms(
        string $provider,
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int    $adults,
        int    $children,
        string $currency,
    ): array {
        $providerEnum    = HotelProviderEnum::from($provider);
        $providerInstance = $this->resolveProvider($providerEnum);

        return $providerInstance->getRooms(
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