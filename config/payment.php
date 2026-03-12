<?php

return [

    'stripe' => [
        'secret_key'      => env('STRIPE_SECRET_KEY', ''),
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
        'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET', ''),
    ],

    'ngenius' => [
        'api_key'        => env('NGENIUS_API_KEY', ''),
        'outlet_ref'     => env('NGENIUS_OUTLET_REF', ''),
        'base_url'       => env('NGENIUS_BASE_URL', 'https://api-gateway.sandbox.ngenius-payments.com'),
        'currency'       => env('NGENIUS_CURRENCY', 'AED'),
        'webhook_secret' => env('NGENIUS_WEBHOOK_SECRET', ''),
        'exchange_rate'  => env('NGENIUS_EXCHANGE_RATE', 4.17),
    ],

];
