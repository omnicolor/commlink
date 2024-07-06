<?php

declare(strict_types=1);

/**
 * List of spells.
 */
return [
    /*
    '' => [
        'category' => '',
        'damage' => '',
        'description' => '',
        'drain' => 'F',
        'duration' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'range' => '',
        'ruleset' => '',
        'tags' => [],
        'type' => '',
    ],
     */
    'control-emotions' => [
        'category' => 'Manipulation',
        'description' => 'Spell description.',
        'drain' => 'F-1',
        'duration' => 'S',
        'id' => 'control-emotions',
        'name' => 'Control Emotions',
        'page' => 21,
        'range' => 'LOS',
        'ruleset' => 'shadow-spells',
        'tags' => ['mental'],
        'type' => 'M',
    ],
    'control-pack' => [
        'category' => 'Manipulation',
        'description' => 'Control pack description.',
        'drain' => 'F-1',
        'duration' => 'S',
        'id' => 'control-pack',
        'name' => 'Control Pack',
        'page' => 115,
        'range' => 'LOS',
        'ruleset' => 'street-grimoire',
        'tags' => ['area', 'mental'],
        'type' => 'M',
    ],
];
