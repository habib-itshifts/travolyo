<?php

return [
    'enabled'       => env('DUFFEL_ENABLED', true),
    'token'         => env('DUFFEL_TOKEN', ''),
    'base_url'      => env('DUFFEL_BASE_URL', 'https://api.duffel.com'),
    'api_version'   => env('DUFFEL_API_VERSION', 'v2'),
    'timeout'       => env('DUFFEL_TIMEOUT', 300),
    'default_limit' => env('DUFFEL_DEFAULT_LIMIT', 20),
];
