<?php

/**
 * List of modifications that can be added to gear.
 */

declare(strict_types=1);

return [
    /*
    '' => [
        'availability' => '',
        'capacity-cost' => ,
        'container-type' => '', // vision | cyberdeck | audio
        'cost' => ,
        'description' => '',
        'effects' => [],
        'id' => '',
        'name' => '',
        'page' => ,
        'rating' => ,
        'ruleset' => '',
        'wireless-effects' => [],
    ],
     */
    'attack-dongle-2' => [
        'id' => 'attack-dongle-2',
        'availability' => '4R',
        'container-type' => 'commlink',
        'cost' => 3000 * 2 * 2,
        'description' => 'Dongle description.',
        'effects' => [
            'attack' => 2,
        ],
        'name' => 'Attack dongle',
        'page' => 61,
        'rating' => 2,
        'ruleset' => 'data-trails',
    ],
    'biomonitor' => [
        'availability' => '3',
        'capacity-cost' => 1,
        'container-type' => 'commlink|cyberdeck|rcc',
        'cost' => 300,
        'description' => 'Biomonitor description.',
        'id' => 'biomonitor',
        'name' => 'Biomonitor',
    ],
];
