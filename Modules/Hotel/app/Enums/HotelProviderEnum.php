<?php

namespace Modules\Hotel\Enums;

enum HotelProviderEnum: string
{
    case Local       = 'local';         // LOCAL      — our own DB (admin/vendor hotels)
    case TravolyoB2B = 'travolyo_b2b';  // B2B        — single call, all sources (local/netstorming_api/tasspro_api)
    case Hyperguest  = 'hyperguest';    // HYPERGUEST — JSON file (mock) → live API later
}
