<?php

return [
    'cache' => [
        'enabled' => env('TOOLBOX_CACHE_ENABLED', true),
        'ttl' => env('TOOLBOX_CACHE_TTL', 7200), // Time-to-live in minutes. Default is 5 days (60 minutes * 24 hours * 5 days).
    ],
    'enviroments' => [],
    'contexts' => [],
];