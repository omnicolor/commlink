<?php

declare(strict_types=1);

/**
 * Data file for Shadowrun 5th edition vehicles.
 */
return [
    /*
    '' => [
        'acceleration' => ,
        'armor' => ,
        'availability' => '',
        'body' => ,
        'category' => '',
        'cost' => ,
        'description' => '',
        'deviceRating' => ,
        'handling' => ,
        'handlingOffRoad' => ,
        'id' => '',
        'name' => '',
        'page' => ,
        'pilot' => ,
        'ruleset' => '',
        'seats' => ,
        'sensor' => ,
        'speed' => ,
        'type' => '',
    ],
    */
    'dodge-scoot' => [
        'acceleration' => 1,
        'armor' => 4,
        'availability' => '',
        'body' => 4,
        'category' => 'bike',
        'cost' => 3000,
        'description' => 'Dodge Scoot description.',
        'deviceRating' => 1,
        'handling' => 4,
        'handlingOffRoad' => 3,
        'id' => 'dodge-scoot',
        'name' => 'Dodge Scoot',
        'pilot' => 1,
        'seats' => 1,
        'sensor' => 1,
        'speed' => 3,
        'type' => 'groundcraft',
    ],
    'mct-fly-spy' => [
        'acceleration' => 2,
        'armor' => 0,
        'availability' => '8',
        'body' => 1,
        'category' => 'mini drone',
        'cost' => 2000,
        'description' => 'Fly-Spy description.',
        'deviceRating' => 3,
        'handling' => 4,
        'id' => 'mct-fly-spy',
        'modifications' => ['rigger-interface'],
        'name' => 'MCT Fly-Spy',
        'pilot' => 3,
        'seats' => 0,
        'sensor' => 3,
        'speed' => 3,
        'type' => 'aircraft',
    ],
];
