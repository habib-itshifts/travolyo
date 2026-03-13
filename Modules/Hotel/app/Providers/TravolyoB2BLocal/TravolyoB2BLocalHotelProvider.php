<?php

namespace Modules\Hotel\Providers\TravolyoB2BLocal;

use Modules\Hotel\Providers\TravolyoB2BBaseHotelProvider;

/**
 * Handles hotels where source = "local" in the Travolyo B2B API response.
 */
class TravolyoB2BLocalHotelProvider extends TravolyoB2BBaseHotelProvider
{
    protected function sourceTag(): string
    {
        return 'local';
    }
}