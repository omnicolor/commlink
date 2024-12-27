<?php

declare(strict_types=1);

return [
    'name' => 'Root',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('ROOT_DATA_PATH', 'Modules/Root/data/'),
];
