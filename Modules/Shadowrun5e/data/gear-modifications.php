<?php

declare(strict_types=1);

/**
 * List of modifications that can be added to gear.
 */
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
    // Commlink modification example.
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
    // Generic modification example.
    'biomonitor' => [
        'availability' => '3',
        'capacity-cost' => 1,
        'container-type' => 'commlink|cyberdeck|rcc',
        'cost' => 300,
        'description' => 'Biomonitor description.',
        'id' => 'biomonitor',
        'name' => 'Biomonitor',
    ],
    // Vision modification example.
    'flare-compensation' => [
        'id' => 'flare-compensation',
        'availability' => '+1',
        'container-type' => 'vision',
        'capacity-cost' => 1,
        'cost' => 250,
        'description' => 'Flare comp description.',
        'name' => 'Flare compensation',
        'page' => 444,
        'ruleset' => 'core',
    ],
];
