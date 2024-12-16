<?php

declare(strict_types=1);

return [
    'name' => 'Alien',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('ALIEN_DATA_PATH', 'Modules/Alien/data/'),
];
