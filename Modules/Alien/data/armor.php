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
    'irc-mk-35-pressure-suit' => [
        'air_supply' => 4,
        'cost' => 2000,
        'description' => 'Standard issue for the USCMC, the Mk.35 is an unfortunately bulky combat pressure suit with a cumbersome recycler unit. You want to be careful wearing one of these in a fight, as the hard joints tend to seize up with extreme motion. While the inexpensive suit offers full protection from the vacuum of space, you have to spend time in a decompression chamber after spacewalking in one. Basically, this suit sucks, but if the choice is a Mk.35 or the cold of space, shut up and suit up. Armor Rating 5, Maximum Air Supply 4. Heavy item.',
        'modifiers' => [
            Armor::MODIFIER_AGILITY_DECREASE,
        ],
        'name' => 'IRC Mk.35 Pressure Suit',
        'page' => 127,
        'rating' => 5,
        'ruleset' => 'core',
        'weight' => 2,
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
