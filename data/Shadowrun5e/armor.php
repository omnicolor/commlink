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
    'ballistic-mask' => [
        'availability' => '6',
        'capacity' => 8,
        'cost' => 150,
        'description' => 'Ballistic mask description.',
        'effects' => [
            'intimidation-limit' => 1,
        ],
        'id' => 'ballistic-mask',
        'name' => 'Ballistic Mask',
        'rating' => 2,
        'ruleset' => 'run-and-gun',
        'stack-rating' => 2,
        'wireless-effects' => [],
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
    'forearm-guards' => [
        'availability' => '6',
        'capacity' => 3,
        'cost' => 300,
        'description' => 'Forearm guards description.',
        'features' => ['custom-fit-stack'],
        'id' => 'forearm-guards',
        'name' => 'Forearm Guards',
        'rating' => 1,
        'ruleset' => 'run-and-gun',
        'stack-rating' => 1,
    ],
    'armored-team-jerseys-licensed' => [
        'availability' => '4',
        'capacity' => 8,
        'cost' => 750,
        'description' => 'Description',
        'id' => 'armored-team-jerseys-licensed',
        'name' => 'Armored Team Jerseys (Licensed)',
        'page' => 136,
        'rating' => 8,
        'ruleset' => 'cutting-aces',
    ],
];
