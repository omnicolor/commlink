<?php

declare(strict_types=1);

/**
 * List of backgrounds for The Expanse.
 */
return [
    /*
    '' => [
        'ability' => '',
        'benefits' => [
            2 => [],
            3 => [],
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
        ],
        'description' => '',
        'focuses' => [],
        'id' => '',
        'name' => '',
        'page' => ,
        'talents' => [],
    ],
     */
    'trade' => [
        'ability' => 'dexterity',
        'benefits' => [
            2 => ['strength' => 1],
            3 => ['focus' => 'technology'],
            4 => ['focus' => 'technology'],
            5 => ['focus' => 'art'],
            6 => ['focus' => 'tolerance'],
            7 => ['perception' => 1],
            8 => ['perception' => 1],
            9 => ['grappling' => 1],
            10 => ['focus' => 'stamina'],
            11 => ['focus' => 'stamina'],
            12 => ['constitution' => 1],
        ],
        'description' => 'Trade background description.',
        'focuses' => [
            'crafting',
            'engineering',
        ],
        'id' => 'trade',
        'name' => 'Trade',
        'page' => 33,
        'talents' => [
            'improvisation',
            'maker',
        ],
    ],
];
