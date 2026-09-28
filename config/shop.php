<?php

return [
    'default_currency' => env('SHOP_DEFAULT_CURRENCY', 'KES'),

    'currencies' => [
        'KES' => ['symbol' => 'KSh', 'decimals' => 2],
        'USD' => ['symbol' => '$',   'decimals' => 2],
    ],

    // Fallback rate: 1 USD = N KES. Used only when a product has no explicit USD price.
    'usd_rate' => env('SHOP_USD_RATE', 130),


    'promotions' => [
    // If a product has multiple live promotions and none are stackable,
    // only the highest-priority one applies.
    'allow_stacking' => false,

    // Cache TTL for storefront promotion queries (seconds).
    'cache_ttl' => 300,
],
];