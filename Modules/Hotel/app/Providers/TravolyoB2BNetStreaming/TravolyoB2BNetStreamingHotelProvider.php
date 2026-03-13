<?php

namespace Modules\Hotel\Providers\TravolyoB2BNetStreaming;

use Modules\Hotel\Providers\TravolyoB2BBaseHotelProvider;

/**
 * Handles hotels where source = "netstorming_api" in the Travolyo B2B API response.
 */
class TravolyoB2BNetStreamingHotelProvider extends TravolyoB2BBaseHotelProvider
{
    protected function sourceTag(): string
    {
        return 'netstorming_api';
    }
}