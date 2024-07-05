<?php

declare(strict_types=1);

/**
 * Data file for Shadowrun 5E spirits.
 */
return [
    /*
    '' => [
        'agility' => 'F',
        'body' => 'F',
        'charisma' => 'F',
        'edge' => 'F/2',
        'essence' => 'F',
        'id' => '',
        'initiative-astral' => '(F)+3d6',
        'initiative' => '(F)+2d6',
        'intuition' => 'F',
        'logic' => 'F',
        'name' => '',
        'magic' => 'F',
        'page' => ,
        'powers' => [
        ],
        'powers-optional' => [
        ],
        'reaction' => 'F',
        'ruleset' => '',
        'skills' => [
        ],
        'special' => '',
        'strength' => 'F',
        'willpower' => 'F',
    ],
     */
    'air' => [
        'agility' => 'F+3',
        'body' => 'F-2',
        'charisma' => 'F',
        'edge' => 'F/2',
        'essence' => 'F',
        'id' => 'air',
        'initiative-astral' => '(F*2)+3d6',
        'initiative' => '(F*2+4)+2d6',
        'intuition' => 'F',
        'logic' => 'F',
        'name' => 'Spirit of Air',
        'magic' => 'F',
        'page' => 303,
        'powers' => [
            'accident',
            'astral-form',
            'concealment',
            'confusion',
            'engulf',
            'materialization',
            'movement',
            'sapience',
            'search',
        ],
        'powers-optional' => [
            'elemental-attack',
            'energy-aura',
            'fear',
            'guard',
            'noxious-breath',
            'psychokinesis',
        ],
        'reaction' => 'F+4',
        'ruleset' => 'core',
        'skills' => [
            'assensing',
            'astral-combat',
            'exotic-ranged-weapon',
            'perception',
            'running',
            'unarmed-combat',
        ],
        'special' => 'Spirits of Air get +10 meters per hit when Sprinting',
        'strength' => 'F-3',
        'willpower' => 'F',
    ],
];
