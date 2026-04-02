<?php

return [
    'name' => 'Hotel',
    'search' => [
        'default_providers' => array_values(array_filter(array_map(
            static fn (mixed $provider) => trim((string) $provider),
            explode(',', (string) env('HOTEL_SEARCH_DEFAULT_PROVIDERS', 'local,travolyo_b2b,hyperguest'))
        ))),
        'enabled_providers' => [
            'local' => filter_var(env('HOTEL_PROVIDER_LOCAL_ENABLED', true), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
            'travolyo_b2b' => filter_var(env('HOTEL_PROVIDER_TRAVOLYO_B2B_ENABLED', true), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
            'hyperguest' => filter_var(env('HOTEL_PROVIDER_HYPERGUEST_ENABLED', true), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
        ],
        'overall_timeout' => max(5, (int) env('HOTEL_SEARCH_OVERALL_TIMEOUT', 35)),
        'hyperguest' => [
            'connect_timeout' => max(2, (int) env('HYPERGUEST_CONNECT_TIMEOUT', 3)),
            'static_timeout' => max(3, (int) env('HYPERGUEST_STATIC_TIMEOUT', 6)),
            'search_timeout' => max(3, (int) env('HYPERGUEST_SEARCH_TIMEOUT', 8)),
            'static_cache_ttl_minutes' => max(10, (int) env('HYPERGUEST_STATIC_CACHE_TTL', 720)),
            'chunk_size' => max(1, (int) env('HYPERGUEST_CHUNK_SIZE', 25)),
            'max_hotel_ids_per_search' => max(1, (int) env('HYPERGUEST_MAX_HOTEL_IDS', 75)),
        ],
    ],
];
