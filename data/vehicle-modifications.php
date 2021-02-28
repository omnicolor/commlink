<?php

/**
 * Data file for Shadowrun 5E vehicle modifications.
 */

return [
    /*
    '' => [
        'availability' => '',
        'cost' => ,
        'description' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'rating' => ,
        'ruleset' => '',
        'slot-type' => '',
        'slots' => ,
    ],
     */
    'manual-control-override' => [
        'availability' => '6',
        'cost' => 500,
        'description' => 'Manual control override description.',
        'id' => 'manual-control-override',
        'name' => 'Manual Control Override',
        'page' => 154,
        'ruleset' => 'rigger-5',
        'slot-type' => 'Power train',
        'slots' => 1,
    ],
    'rigger-interface' => [
        'availability' => '4',
        'cost' => 1000,
        'description' => 'Rigger interface description.',
        'id' => 'rigger-interface',
        'name' => 'Rigger Interface',
        'page' => 461,
        'ruleset' => 'core',
    ],
    'sensor-array-2' => [
        'availability' => '7',
        'cost' => 1000 * 2,
        'description' => 'Sensor array description.',
        'id' => 'sensor-array-2',
        'name' => 'Sensor Array',
        'page' => 445,
        'rating' => 2,
        'ruleset' => 'core',
    ],
];
