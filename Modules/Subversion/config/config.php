<?php

declare(strict_types=1);

return [
    'name' => 'Subversion',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('SUBVERSION_DATA_PATH', 'Modules/Subversion/data/'),
];
