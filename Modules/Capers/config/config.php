<?php

declare(strict_types=1);

return [
    'name' => 'Capers',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('CAPERS_DATA_PATH', 'Modules/Capers/data/'),
];
