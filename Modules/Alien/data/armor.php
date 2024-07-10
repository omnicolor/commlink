<?php

declare(strict_types=1);

use Modules\Alien\Models\Armor;

return [
    /*
    '' => [
        'air_supply' => ,
        'cost' => ,
        'description' => '',
        'modifiers' => [
        ],
        'name' => '',
        'page' => 127,
        'rating' => ,
        'ruleset' => 'core',
        'weight' => ,
    ],
     */
    'm3-personnel-armor' => [
        'air_supply' => 0,
        'cost' => 1200,
        'description' => 'M3 Personnel Armor description.',
        'modifiers' => [
            Armor::MODIFIER_COMM_UNIT,
        ],
        'name' => 'M3 Personnel Armor',
        'page' => 127,
        'rating' => 6,
        'ruleset' => 'core',
        'weight' => 1,
    ],
    'irc-mk-50-compression-suit' => [
        'air_supply' => 5,
        'cost' => 15000,
        'description' => 'IRC Mk.50 Compression Suit description.',
        'modifiers' => [],
        'name' => 'IRC Mk.50 Compression Suit',
        'page' => 127,
        'rating' => 2,
        'ruleset' => 'core',
        'weight' => 1,
    ],
];
