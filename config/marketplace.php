<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Marketplace Settings
    |--------------------------------------------------------------------------
    |
    | All money values are stored as integers in the smallest currency unit
    | (kopecks). The scale is used to format and convert amounts for display.
    |
    */

    'currency' => env('MARKETPLACE_CURRENCY', 'Руб'),

    'currency_scale' => 100,

    'delivery' => [
        'courier_price' => 500,
        'free_from' => 10000,
        'default_city' => 'Минск',
        'default_zone' => 'Другой город',

        // Courier zones: surcharge on top of courier_price plus delivery time.
        'zones' => [
            'Минск' => ['surcharge' => 0, 'min_days' => 1, 'max_days' => 1],
            'Гомель' => ['surcharge' => 200, 'min_days' => 1, 'max_days' => 2],
            'Брест' => ['surcharge' => 200, 'min_days' => 1, 'max_days' => 2],
            'Витебск' => ['surcharge' => 200, 'min_days' => 1, 'max_days' => 2],
            'Гродно' => ['surcharge' => 200, 'min_days' => 1, 'max_days' => 2],
            'Могилёв' => ['surcharge' => 200, 'min_days' => 1, 'max_days' => 2],
            'Другой город' => ['surcharge' => 400, 'min_days' => 2, 'max_days' => 4],
        ],

        'pickup_days' => ['min' => 1, 'max' => 2],
        'postamat_days' => ['min' => 2, 'max' => 3],
    ],

    'catalog' => [
        'per_page' => 12,
    ],

];
