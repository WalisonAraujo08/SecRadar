<?php
// config/scan_sources.php
// ATENÇÃO: Estas chaves são internas — NUNCA expostas ao cliente via UI

return [
    'hibp' => [
        'api_key' => env('HIBP_API_KEY'),
        'enabled' => true,
    ],
    'leakcheck' => [
        'api_key' => env('LEAKCHECK_API_KEY'),
        'enabled' => true,
    ],
    'dehashed' => [
        'email'   => env('DEHASHED_EMAIL'),
        'api_key' => env('DEHASHED_API_KEY'),
        'enabled' => true,
    ],
    'breachdir' => [
        'api_key' => env('BREACHDIR_API_KEY'),
        'enabled' => true,
    ],
    'leaklookup' => [
    'api_key' => env('LEAKLOOKUP_API_KEY', ''),
    ],
];
