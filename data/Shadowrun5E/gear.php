<?php

/**
 * List of gear.
 */
return [
    /*
    '' => [
        'attributes' => [],
        'attributes' => [
            'attack' => ,
            'sleaze' => ,
            'data-processing' => ,
            'firewall' => ,
        ],
        'availability' => '',
        'capacity' => ,
        'container-type' => [''],
        'cost' => ,
        'description' => '',
        'effects' => [],
        'id' => '',
        'name' => '',
        'page' => ,
        'programs' => ,
        'rating' => ,
        'ruleset' => '',
        'subname' => '',
        'wireless-effects' => [],
    ],
     */
    // Commlink example.
    'commlink-sony-angel' => [
        'id' => 'commlink-sony-angel',
        'availability' => '2',
        'container-type' => ['commlink'],
        'cost' => 100,
        'description' => 'Commlink description.',
        'name' => 'Commlink',
        'programs' => 1,
        'rating' => 1,
        'ruleset' => 'data-trails',
        'subname' => 'Sony Angel',
    ],
    // Example of a non-configurable cyberdeck.
    'cyberdeck-ares-echo-unlimited' => [
        'id' => 'cyberdeck-ares-echo-unlimited',
        'attributes' => [
            'attack' => 9,
            'sleaze' => 6,
            'data-processing' => 4,
            'firewall' => 5,
        ],
        'availability' => '15R',
        'container-type' => ['cyberdeck'],
        'cost' => 395900,
        'description' => 'Cyberdeck description.',
        'name' => 'Cyberdeck',
        'page' => 64,
        'programs' => 3,
        'rating' => 5,
        'ruleset' => 'data-trails',
        'subname' => 'Ares Echo Unlimited',
    ],
    // Example of a configurable cyberdeck.
    'cyberdeck-evo-sublime' => [
        'attributes' => [7, 6, 5, 5],
        'availability' => '12R',
        'container-type' => ['cyberdeck'],
        'cost' => 375000,
        'description' => 'Cyberdeck description.',
        'id' => 'cyberdeck-evo-sublime',
        'name' => 'Cyberdeck',
        'page' => 62,
        'programs' => 4,
        'rating' => 4,
        'ruleset' => 'kill-code',
        'subname' => 'Evo Sublime',
    ],
    // Common item example.
    'credstick-gold' => [
        'id' => 'credstick-gold',
        'availability' => '5',
        'cost' => 100,
        'description' => 'Credstick description.',
        'name' => 'Certified Credstick',
        'subname' => 'Gold',
    ],
    // Vision container example.
    'goggles-2' => [
        'id' => 'goggles-2',
        'availability' => '',
        'capacity' => 2,
        'container-type' => ['vision'],
        'cost' => 50 * 2,
        'description' => 'Goggles description.',
        'name' => 'Goggles',
        'rating' => 2,
    ],
];
