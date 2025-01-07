<?php

declare(strict_types=1);

return [
    'name' => 'Transformers',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('TRANSFORMERS_DATA_PATH', 'Modules/Transformers/data/'),
];
