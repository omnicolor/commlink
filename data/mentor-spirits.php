<?php

/**
 * List of mentor spirits.
 */

declare(strict_types=1);

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
