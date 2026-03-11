<?php

/*
|--------------------------------------------------------------------------
| Supported Languages
|--------------------------------------------------------------------------
| Add / remove languages here. No other files need to change.
| Also create the matching lang/{locale}/admin.php translation file.
|
| Usage:  config('language.default')
|         config('language.supported')
|         config('language.supported.en')
*/

return [

    'default' => 'en',

    'supported' => [
        'en' => ['label' => 'English',  'flag' => 'gb', 'dir' => 'ltr'],
        'ar' => ['label' => 'العربية',  'flag' => 'sa', 'dir' => 'rtl'],
        // 'fr' => ['label' => 'Français', 'flag' => 'fr', 'dir' => 'ltr'],
        // 'de' => ['label' => 'Deutsch',  'flag' => 'de', 'dir' => 'ltr'],
        // 'ur' => ['label' => 'اردو',     'flag' => 'pk', 'dir' => 'rtl'],
    ],

];
