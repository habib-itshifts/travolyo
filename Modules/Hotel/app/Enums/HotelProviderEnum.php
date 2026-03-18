<?php

namespace Modules\Hotel\Enums;

enum HotelProviderEnum: string
{
    case Local                  = 'local';                       // LOCAL       — our own DB (admin/vendor hotels)
    case TravolyoB2BLocal       = 'travolyo_b2b_local';          // B2B_LOCAL   — source: local
    case TravolyoB2BNetStreaming = 'travolyo_b2b_net_streaming';  // B2B_NET_STREAMING — source: netstorming_api
    case TravolyoB2BTassPro     = 'travolyo_b2b_tasspro';        // B2B_TASSPRO — source: tasspro_api
    case Hyperguest             = 'hyperguest';                  // HYPERGUEST  — JSON file (mock) → live API later
}
