<?php

declare(strict_types=1);

/**
 * List of Shadowrun armor.
 */
return [
    /*
    '' => [
        'availability' => '',
        'capacity' => ,
        'cost' => ,
        'description' => '',
        'effects' => [],
        'features' => [],
        'id' => '',
        'name' => '',
        'page' => ,
        'rating' => ,
        'ruleset' => '',
        'stack-rating' => ,
        'wireless-effects' => [],
    ],
    */
    'armor-jacket' => [
        'availability' => '2',
        'cost' => 1000,
        'description' => 'Description goes here.',
        'id' => 'armor-jacket',
        'name' => 'Armor Jacket',
        'rating' => 12,
    ],
    'berwick-suit' => [
        'availability' => '9',
        'capacity' => 5,
        'cost' => 2600,
        'description' => 'Armor description.',
        'effects' => [
            'concealability' => -2,
            'social-limit' => 1,
        ],
        'features' => ['custom-fit'],
        'id' => 'berwick-suit',
        'name' => 'Berwick Suit',
        'rating' => 9,
        'ruleset' => 'run-and-gun',
        'wireless-effects' => ['social-tests' => 1],
    ],
];
