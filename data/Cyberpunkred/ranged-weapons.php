<?php

declare(strict_types=1);

/**
 * Ranged weapons for Cyberpunk Red.
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
        'magazine' => ,
        'rate-of-fire' => ,
        'skill' => '',
        'type' => '',
    ],
     */
    'medium-pistol' => [
        'concealable' => true,
        'cost' => 50,
        'damage' => '2d6',
        'examples' => [
            'poor' => ['Dai Lung Streetmaster'],
            'standard' => ['Federated Arms X-9mm'],
            'excellent' => ['Militech "Avenger"'],
        ],
        'hands-required' => 1,
        'magazine' => 12,
        'rate-of-fire' => 2,
        'skill' => 'handgun',
        'type' => 'Medium pistol',
    ],
];
