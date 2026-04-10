<?php

return [
    'name' => 'Hotel',

    'default_nationality' => env('HOTEL_DEFAULT_NATIONALITY', 'AE'),

    'hyperguest' => [
        'api_key'  => env('HYPERGUEST_API_KEY', ''),
        'base_url' => env('HYPERGUEST_BASE_URL', 'https://book-api.hyperguest.com/2.0'),
        'search_url' => env('HYPERGUEST_SEARCH_URL', 'https://search-api.hyperguest.io/2.0/'),
        'is_test'  => env('HYPERGUEST_TEST_MODE', true),
    ],
];
