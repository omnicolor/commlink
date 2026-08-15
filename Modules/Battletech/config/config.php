<?php

declare(strict_types=1);

return [
    'name' => 'Battletech',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('BATTLETECH_DATA_PATH', 'Modules/Battletech/data/'),
];
