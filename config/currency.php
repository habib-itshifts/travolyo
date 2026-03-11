<?php

/*
|--------------------------------------------------------------------------
| Supported Currencies
|--------------------------------------------------------------------------
| Add / remove currencies here. No other files need to change.
|
| Usage:  config('currency.default')
|         config('currency.supported')
|         config('currency.supported.USD')
*/

return [

    'default' => 'USD',

    'supported' => [
        'USD' => ['label' => 'USD', 'symbol' => '$',   'flag' => 'us', 'name' => 'US Dollar'],
        'GBP' => ['label' => 'GBP', 'symbol' => '£',   'flag' => 'gb', 'name' => 'Pound'],
        'EUR' => ['label' => 'EUR', 'symbol' => '€',   'flag' => 'eu', 'name' => 'Euro'],
        'AED' => ['label' => 'AED', 'symbol' => 'د.إ', 'flag' => 'ae', 'name' => 'Dirham'],
        // 'SAR' => ['label' => 'SAR', 'symbol' => '﷼',  'flag' => 'sa', 'name' => 'Saudi Riyal'],
        // 'TRY' => ['label' => 'TRY', 'symbol' => '₺',  'flag' => 'tr', 'name' => 'Turkish Lira'],
    ],

];
