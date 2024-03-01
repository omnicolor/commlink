<?php

declare(strict_types=1);

/**
 * Shadowrun 5E echoes: powers a technomancer can take when they submerge.
 */
return [
    /*
    '' => [
        'chummer-id' => '',
        'description' => '',
        'effects' => [
        ],
        'id' => '',
        'limit' => ,
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
     */
    'attack-upgrade' => [
        'chummer-id' => '36aa9af4-5c04-40d9-ba09-31b401cc1ff0',
        'description' => 'Description of Attack upgrade echo.',
        'effects' => [
            'attack' => 1,
        ],
        'id' => 'attack-upgrade',
        'limit' => 2,
        'name' => 'Attack upgrade',
        'page' => 258,
        'ruleset' => 'core',
    ],
    'resonance-armor' => [
        'chummer-id' => 'd5dbe3f7-8a44-466b-8d5d-db9f0c68ee6b',
        'description' => 'Description of the Resonance [Armor] echo.',
        'effects' => [
            'program' => 'armor',
        ],
        'id' => 'resonance-armor',
        'limit' => 1,
        'name' => 'Resonance [Armor]',
        'page' => 258,
        'ruleset' => 'core',
    ],
];
