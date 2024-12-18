<?php

declare(strict_types=1);

return [
    'name' => 'Startrekadventures',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env(
        'STAR_TREK_ADVENTURES_DATA_PATH',
        'Modules/Startrekadventures/data',
    ),
];
