<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'crew_minimum' => ,
        'crew_standard' => ,
        'favored_range' => '',
        'favored_stunts' => [],
        'flaws' => [],
        'has_epstein' => false,
        'length' => 'm',
        'name' => '',
        'page' => ,
        'qualities' => [],
        'ruleset' => '',
        'sensors' => ,
        'size' => '',
        'weapons' => [],
    ],
     */
    'destroyer' => [
        'name' => 'Destroyer',
        'page' => 127,
        'qualities' => [
            'advanced-sensor-package-1',
            'good-juice',
            'hull-plating-1',
            'self-destruct-system',
        ],
        'ruleset' => 'core',
        'sensors' => 2,
        'size' => 'huge',
        'weapons' => [
            ['id' => 'point-defense-cannon', 'mount' => 'full', 'quantity' => 8],
            ['id' => 'rail-gun', 'mount' => 'fore'],
            ['id' => 'torpedo', 'mount' => 'fore'],
        ],
    ],
    'munroe' => [
        'crew_minimum' => 18,
        'crew_standard' => 70,
        'favored_range' => 'medium',
        'favored_stunts' => [
            'multi-target',
            'perceived-weakness',
            'tactics',
        ],
        'flaws' => ['lumbering'],
        'length' => '152m',
        'name' => 'Munroe-class destroyer',
        'page' => 9,
        'qualities' => [
            'advanced-communications-systems',
            'advanced-targeting-systems',
            'emergency-batteries',
            'hull-plating-3',
            'medical-expert-system',
            'rapid-reload-torpedo-tubes',
            'redundant-hull-3',
            'sensor-scrambling',
            'self-destruct-system',
        ],
        'ruleset' => 'ships-of-the-expanse',
        'sensors' => 2,
        'size' => 'huge',
        'weapons' => [
            ['id' => 'point-defense-cannon', 'mount' => 'full', 'quantity' => 8],
            ['id' => 'rail-gun', 'mount' => 'fore'],
            ['id' => 'torpedo', 'mount' => 'fore', 'quantity' => 2],
            ['id' => 'torpedo', 'mount' => 'aft', 'quantity' => 2],
            ['id' => 'torpedo', 'mount' => 'ventral', 'quantity' => 2],
        ],
    ],
    'shuttle' => [
        'name' => 'Shuttle',
        'page' => 125,
        'ruleset' => 'core',
        'sensors' => 0,
        'size' => 'small',
    ],
];
