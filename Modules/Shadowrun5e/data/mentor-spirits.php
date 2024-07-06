<?php

declare(strict_types=1);

/**
 * List of mentor spirits.
 */
return [
    /*
    '' => [
        'description' => '',
        'effects' => [],
        'id' => '',
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
     */
    'bear' => [
        'description' => 'Description of Bear.',
        'effects' => [
            'damage-resistance' => 2,
            'health-spell' => 2,
            'health-preparations' => 2,
            'health-ritual' => 2,
            'rapid-healing' => 1,
        ],
        'id' => 'bear',
        'name' => 'Bear',
        'ruleset' => 'core',
    ],
    'goddess' => [
        'description' => 'Description of Goddess.',
        'effects-adept' => ['adept-power' => 'authoritative-tone-1'],
        'effects-all' => ['instruction-dice-pool' => 2],
        'effects-magician' => ['ritual-spellcasting-dice-pool' => 2],
        'id' => 'goddess',
        'name' => 'Goddess',
        'page' => 129,
        'ruleset' => 'book-of-the-lost',
    ],
];
