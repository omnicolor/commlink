<?php

/**
 * List of matrix programs
 */

declare(strict_types=1);

return [
    /*
    '' => [
        'allowedDevices' => ['commlink', 'cyberdeck', 'rcc', 'vehicle'],
        'availability' => '',
        'cost' => ,
        'description' => '',
        'effects' => [],
        'id' => '',
        'name' => '',
        'page' => ,
        'rating' => ,
        'ruleset' => '',
        'specificVehicle' => false,
    ],
     */
    'armor' => [
        'id' => 'armor',
        'allowedDevices' => ['cyberdeck', 'rcc'],
        'availability' => '4R',
        'cost' => 250,
        'description' => 'Description goes here.',
        'effects' => [
            'damage-resist' => 2,
        ],
        'name' => 'Armor',
        'page' => 245,
        'ruleset' => 'core',
    ],
    'bootstrap' => [
        'id' => 'bootstrap',
        'allowedDevices' => ['cyberdeck'],
        'availability' => '',
        'cost' => 80,
        'description' => 'Bootstrap description.',
        'name' => 'Bootstrap',
        'page' => 56,
        'ruleset' => 'data-trails',
    ],
];
