<?php

return [
    'base_url'      => env('TRAVOLYO_B2B_BASE_URL', 'https://travolyob2b.com/'),
    'auth_api_key'  => env('TRAVOLYO_B2B_API_KEY', ''),
    'timeout'       => env('TRAVOLYO_B2B_TIMEOUT', 20),
    'search_timeout'=> env('TRAVOLYO_B2B_SEARCH_TIMEOUT', env('TRAVOLYO_B2B_TIMEOUT', 20)),
    'connect_timeout' => env('TRAVOLYO_B2B_CONNECT_TIMEOUT', 5),
    'default_limit' => env('TRAVOLYO_B2B_DEFAULT_LIMIT', 20),
];
