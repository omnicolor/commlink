<?php

declare(strict_types=1);

return [
    'name' => 'Avatar',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('AVATAR_DATA_PATH', 'Modules/Avatar/data/'),
];
