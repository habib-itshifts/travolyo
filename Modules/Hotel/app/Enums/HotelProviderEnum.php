<?php

namespace Modules\Hotel\Enums;

enum HotelProviderEnum: string
{
    case Local                  = 'local';                    // Our own DB (admin/vendor hotels)
    case TravolyoB2BNetStreaming = 'travolyo_b2b_net_streaming'; // Travolyo B2B Net Streaming API
    case TravolyoB2BLocal       = 'travolyo_b2b_local';      // Travolyo B2B Local API
}
