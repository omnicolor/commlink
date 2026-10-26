<?php

declare(strict_types=1);

use Modules\Shadowrun6e\Enums\ComplexFormDuration;

return [
    /*
    [
        'description' => '',
        'duration' => ComplexFormDuration::,
        'fade_value' => ,
        'id' => '',
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
    */
    [
        'description' => 'Make an Electronics + Resonance test. Each hit reduces your Overwatch Score by 1.',
        'duration' => ComplexFormDuration::Permanent,
        'fade_value' => 2,
        'id' => 'cleaner',
        'name' => 'Cleaner',
        'page' => 190,
        'ruleset' => 'core',
    ],
];
