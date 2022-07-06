<?php

declare(strict_types=1);

/**
 * Melee weapons for Cyberpunk Red.
 */
return [
    /*
    '' => [
        'concealable' => true,
        'cost' => ,
        'damage' => '',
        'examples' => [
            'poor' => [],
            'standard' => [],
            'excellent' => [],
        ],
        'hands-required' => ,
        'rate-of-fire' => ,
        'type' => '',
    ],
     */
    'medium-melee' => [
        'concealable' => false,
        'cost' => 50,
        'damage' => '2d6',
        'examples' => [
            'poor' => [],
            'standard' => ['Baseball bat', 'Crowbar'],
            'excellent' => [],
        ],
        'hands-required' => 1,
        'rate-of-fire' => 2,
        'type' => 'Medium melee',
    ],
];
