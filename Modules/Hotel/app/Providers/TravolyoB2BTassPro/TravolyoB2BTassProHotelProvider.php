<?php

namespace Modules\Hotel\Providers\TravolyoB2BTassPro;

use Modules\Hotel\Providers\TravolyoB2BBaseHotelProvider;

/**
 * Handles hotels where source = "tasspro_api" in the Travolyo B2B API response.
 */
class TravolyoB2BTassProHotelProvider extends TravolyoB2BBaseHotelProvider
{
    protected function sourceTag(): string
    {
        return 'tasspro_api';
    }
}
