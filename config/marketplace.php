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

    'currency' => env('MARKETPLACE_CURRENCY', 'BYN'),

    'currency_scale' => 100,

    'delivery' => [
        'courier_price' => 500,
        'free_from' => 10000,
    ],

    'catalog' => [
        'per_page' => 12,
    ],

];
