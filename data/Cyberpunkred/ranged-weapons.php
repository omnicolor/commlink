<?php

declare(strict_types=1);

/**
 * Ranged weapons for Cyberpunk Red.
 */
return [
    /*
    '' => [
        'concealable' => true,
        'cost' => ,
        'damage' => '',
        'examples' => [
            'poor' => [],
            'standard' => [],
            'excellent' => [],
        ],
        'hands-required' => ,
        'magazine' => ,
        'page' => 341,
        'rate-of-fire' => ,
        'ruleset' => 'core',
        'skill' => '',
        'type' => '',
    ],
     */
    'medium-pistol' => [
        'concealable' => true,
        'cost' => 50,
        'damage' => '2d6',
        'examples' => [
            'poor' => ['Dai Lung Streetmaster'],
            'standard' => ['Federated Arms X-9mm'],
            'excellent' => ['Militech "Avenger"'],
        ],
        'hands-required' => 1,
        'magazine' => 12,
        'page' => 341,
        'rate-of-fire' => 2,
        'ruleset' => 'core',
        'skill' => 'handgun',
        'type' => 'Medium pistol',
    ],
    'stun-gun' => [
        'concealable' => true,
        'cost' => 100,
        'damage' => '3d6',
        'examples' => [
            'poor' => [],
            'standard' => [],
            'excellent' => [],
        ],
        'hands-required' => 1,
        'magazine' => 8,
        'page' => 349,
        'rate-of-fire' => 2,
        'ruleset' => 'core',
        'skill' => 'handgun',
        'type' => 'Stun gun',
    ],
];
