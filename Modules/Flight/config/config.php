<?php

return [
    'name' => 'Flight',

    'duffel' => [
        'enabled'       => true,
        'token'         => '',   // Add your Duffel token here
        'base_url'      => 'https://api.duffel.com',
        'api_version'   => 'v2',
        'timeout'       => 20,
        'default_limit' => 20,
    ],

    'travolyo_b2b' => [
        'enabled'  => false,
        'base_url' => '',
        'username' => '',
        'password' => '',
        'timeout'  => 30,
    ],
];
