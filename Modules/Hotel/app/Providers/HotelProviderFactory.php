<?php

namespace Modules\Hotel\Providers;

use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Providers\Hyperguest\HyperguestHotelProvider;
use Modules\Hotel\Providers\Local\LocalHotelProvider;
use Modules\Hotel\Providers\TravolyoB2B\TravolyoB2BHotelProvider;

class HotelProviderFactory
{
    public static function make(HotelProviderEnum $provider): HotelProviderInterface
    {
        return match ($provider) {
            HotelProviderEnum::Local       => app(LocalHotelProvider::class),
            // HotelProviderEnum::TravolyoB2B => app(TravolyoB2BHotelProvider::class),
            HotelProviderEnum::Hyperguest  => app(HyperguestHotelProvider::class),
        };
    }

    public static function fromSource(?string $source): HotelProviderInterface
    {
        $enum = HotelProviderEnum::tryFrom($source ?? '') ?? HotelProviderEnum::Local;

        return self::make($enum);
    }
}
