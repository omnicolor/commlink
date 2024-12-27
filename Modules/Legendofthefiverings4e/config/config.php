<?php

declare(strict_types=1);

return [
    'name' => 'Legendofthefiverings4e',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env(
        'LEGENDOFTHEFIVERINGS4E_DATA_PATH',
        'Modules/Legendofthefiverings4e/data/',
    ),
];
