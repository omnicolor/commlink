<?php

declare(strict_types=1);

/**
 * Lifestyles for Shadowrun 5E.
 */
return [
    /*
    '' => [
        'attributes' => [
            'comforts' => ,
            'comfortsMax' => ,
            'neighborhood' => ,
            'neighborhoodMax' => ,
            'security' => ,
            'securityMax' => ,
        ],
        'cost' => ,
        'description' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'points' => ,
        'ruleset' => '',
    ],
     */
    'commercial' => [
        'attributes' => [
            'comforts' => 3,
            'comfortsMax' => 4,
            'neighborhood' => 4,
            'neighborhoodMax' => 6,
            'security' => 3,
            'securityMax' => 7,
        ],
        'cost' => 8000,
        'description' => 'Commercial description.',
        'id' => 'commercial',
        'name' => 'Commercial',
        'page' => 218,
        'points' => 4,
        'ruleset' => 'run-faster',
    ],
    'high' => [
        'attributes' => [
            'comforts' => 4,
            'comfortsMax' => 6,
            'neighborhood' => 5,
            'neighborhoodMax' => 6,
            'security' => 4,
            'securityMax' => 6,
        ],
        'cost' => 10000,
        'description' => 'High description.',
        'id' => 'high',
        'name' => 'High',
        'page' => 373,
        'points' => 6,
        'ruleset' => 'core',
    ],
    'low' => [
        'attributes' => [
            'comforts' => 2,
            'comfortsMax' => 3,
            'neighborhood' => 2,
            'neighborhoodMax' => 3,
            'security' => 2,
            'securityMax' => 3,
        ],
        'cost' => 2000,
        'description' => 'Low description.',
        'id' => 'low',
        'name' => 'Low',
        'page' => 373,
        'points' => 3,
        'ruleset' => 'core',
    ],
    'luxury' => [
        'attributes' => [
            'comforts' => 5,
            'comfortsMax' => 7,
            'neighborhood' => 5,
            'neighborhoodMax' => 7,
            'security' => 5,
            'securityMax' => 8,
        ],
        'cost' => 100000,
        'description' => 'Luxury description.',
        'id' => 'luxury',
        'name' => 'Luxury',
        'page' => 373,
        'points' => 12,
        'ruleset' => 'core',
    ],
    'middle' => [
        'attributes' => [
            'comforts' => 3,
            'comfortsMax' => 4,
            'neighborhood' => 4,
            'neighborhoodMax' => 5,
            'security' => 3,
            'securityMax' => 4,
        ],
        'cost' => 5000,
        'description' => 'Middle description.',
        'id' => 'middle',
        'name' => 'Middle',
        'page' => 373,
        'points' => 4,
        'ruleset' => 'core',
    ],
    'squatter' => [
        'attributes' => [
            'comforts' => 1,
            'comfortsMax' => 2,
            'neighborhood' => 1,
            'neighborhoodMax' => 2,
            'security' => 1,
            'securityMax' => 3,
        ],
        'cost' => 500,
        'description' => 'Squatter description.',
        'id' => 'squatter',
        'name' => 'Squatter',
        'page' => 373,
        'points' => 2,
        'ruleset' => 'core',
    ],
    'street' => [
        'attributes' => [
            'comforts' => 0,
            'comfortsMax' => 1,
            'neighborhood' => 0,
            'neighborhoodMax' => 1,
            'security' => 0,
            'securityMax' => 1,
        ],
        'cost' => 0,
        'description' => 'Street description.',
        'id' => 'street',
        'name' => 'Street',
        'page' => 373,
        'points' => 2,
        'ruleset' => 'core',
    ],
];
