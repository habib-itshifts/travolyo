<?php

namespace Modules\Hotel\Actions;

use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Providers\HotelProviderInterface;
use Modules\Hotel\Providers\Hyperguest\HyperguestHotelProvider;
use Modules\Hotel\Providers\Local\LocalHotelProvider;
use Modules\Hotel\Providers\TravolyoB2B\TravolyoB2BHotelProvider;

class PrebookHotelAction
{
    public function handle(PrebookHotelDto $dto): HotelOfferDto
    {
        return $this->resolveProvider($dto->provider)->prebook($dto);
    }

    private function resolveProvider(HotelProviderEnum $provider): HotelProviderInterface
    {
        return match ($provider) {
            HotelProviderEnum::Local       => new LocalHotelProvider(),
            HotelProviderEnum::TravolyoB2B => new TravolyoB2BHotelProvider(),
            HotelProviderEnum::Hyperguest  => new HyperguestHotelProvider(),
        };
    }
}
