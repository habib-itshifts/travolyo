<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    */
    'stripe' => [
        'secret_key'      => '',
        'publishable_key' => '',
        'webhook_secret'  => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | N-Genius (Network International)
    |--------------------------------------------------------------------------
    */
    'ngenius' => [
        'api_key'        => '',
        'outlet_ref'     => '',
        'base_url'       => 'https://api-gateway.sandbox.ngenius-payments.com',
        'currency'       => 'AED',
        'webhook_secret' => '',
        'exchange_rate'  => 4.17
    ],

];