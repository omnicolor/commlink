<?php

declare(strict_types=1);

/**
 * Lifestyle options for Shadowrun 5E.
 */
return [
    /*
    '' => [
        'cost' => ,
        'costMultiplier' => ,
        'description' => '',
        'minimumLifestyle' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'points' => ,
        'ruleset' => '',
        'type' => '', // Should be Asset, Outing, or Service
    ],
    */
    'increase-neighborhood' => [
        'costMultiplier' => 0.1,
        'description' => 'Increase Neighborhood description.',
        'id' => 'increase-neighborhood',
        'minimumLifestyle' => 'None',
        'name' => 'Increase Neighborhood',
        'page' => 216,
        'points' => 1,
        'ruleset' => 'run-faster',
        'type' => 'Option',
    ],
    'swimming-pool' =>[
        'cost' => 100,
        'description' => 'Swimming Pool description.',
        'id' => 'swimming-pool',
        'minimumLifestyle' => 'Middle',
        'name' => 'Swimming Pool',
        'page' => 224,
        'points' => 1,
        'ruleset' => 'run-faster',
        'type' => 'Asset',
    ],
];
