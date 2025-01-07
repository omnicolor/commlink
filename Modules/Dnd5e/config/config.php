<?php

declare(strict_types=1);

return [
    'name' => 'Dnd5e',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('DND5E_DATA_PATH', 'Modules/Dnd5e/data/'),
];
