<?php

declare(strict_types=1);

return [
    'name' => 'Expanse',
    // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
    'data_path' => env('EXPANSE_DATA_PATH', 'Modules/Expanse/data'),
];
