<?php

declare(strict_types=1);

/**
 * Collection of talents for The Expanse.
 */
return [
    /*
    '' => [
        'benefits' => [
            'novice' => '',
            'expert' => '',
            'master' => '',
        ],
        'description' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'requirements' => [],
    ],
     */
    'fringer' => [
        'benefits' => [
            'novice' => 'Description of novice Fringer.',
            'expert' => 'Description of expert Fringer.',
            'master' => 'Description of master Fringer.',
        ],
        'description' => 'Fringer description.',
        'id' => 'fringer',
        'name' => 'Fringer',
        'page' => 52,
        'requirements' => [],
    ],
    'maker' => [
        'benefits' => [
            'novice' => 'Description of novice Maker.',
            'expert' => 'Description of expert Maker.',
            'master' => 'Description of master Maker.',
        ],
        'description' => 'Description of a Maker.',
        'id' => 'maker',
        'name' => 'Maker',
        'page' => 55,
        'requirements' => [
            'focus' => [
                'crafting',
                'engineering',
            ],
        ],
    ],
];
