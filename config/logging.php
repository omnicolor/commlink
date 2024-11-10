<?php

declare(strict_types=1);

return [

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    'channels' => [
        'deprecations' => [
            'driver' => 'single',
            'path' => storage_path('logs/deprecation-warnings.log'),
        ],
    ],

];
