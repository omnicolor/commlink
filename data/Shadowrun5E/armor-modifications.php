<?php

/**
 * List of armor modifications.
 */

declare(strict_types=1);

return [
    /*
    '' => [
        'availability' => '',
        'capacity-cost' => ,
        'cost' => ,
        'cost-multiplier' => ,
        'description' => '',
        'effects' => [],
        'id' => '',
        'name' => '',
        'page' => ,
        'rating' => ,
        'ruleset' => '',
    ],
     */
    'auto-injector' => [
        'availability' => '4',
        'capacity-cost' => 2,
        'cost' => 1500,
        'description' => 'Modification description goes here.',
        'id' => 'auto-injector',
        'name' => 'Auto-injector',
        'ruleset' => 'run-and-gun',
    ],
    'fire-resistance-2' => [
        'availability' => '6',
        'capacity-cost' => 2,
        'cost' => 250 * 2,
        'description' => 'Mod description goes here.',
        'effects' => [
            'fire-resistance' => 2,
        ],
        'id' => 'fire-resistance-2',
        'name' => 'Fire Resistance',
        'rating' => 2,
    ],
    'gel-packs' => [
        'availability' => '6',
        'cost' => 1500,
        'description' => 'Modification description.',
        'effects' => [
            'armor' => 2,
        ],
        'id' => 'gel-packs',
        'name' => 'Gel Packs',
        'ruleset' => 'run-and-gun',
    ],
    'ynt-softweave-armor' => [
        'id' => 'ynt-softweave-armor',
        'availability' => '+4',
        'capacity-multiplier' => 1.5,
        'cost-multiplier' => 2,
        'description' => 'Mod description.',
        'effects' => [],
        'name' => 'YNT Softweave Armor',
        'ruleset' => 'run-and-gun',
    ],
];
