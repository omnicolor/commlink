<?php

declare(strict_types=1);

/**
 * List of Shadowrun 5E adept powers.
 */
return [
    /*
    '' => [
        'cost' => ,
        'description' => '',
        'effects' => [],
        'id' => '',
        'incompatible-with' => [],
        'level' => ,
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
    */
    'adrenaline-boost-1' => [
        'id' => 'adrenaline-boost-1',
        'cost' => .25 * 1,
        'description' => 'Adrenaline boost ',
        'effects' => [
            'initiative' => 2 * 1,
        ],
        'incompatible-with' => [
            'adrenaline-boost-1',
            'adrenaline-boost-2',
            'adrenaline-boost-3',
            'adrenaline-boost-4',
            'adrenaline-boost-5',
            'adrenaline-boost-6',
            'adrenaline-boost-7',
        ],
        'level' => 1,
        'name' => 'Adrenaline boost',
    ],
    'combat-sense-2' => [
        'cost' => .5 * 2,
        'description' => 'Combat sense description',
        'effects' => [
            'melee-defense' => 2,
            'ranged-defense' => 2,
        ],
        'id' => 'combat-sense-2',
        'level' => 2,
        'name' => 'Combat Sense',
    ],
    'critical-strike-unarmed-combat' => [
        'cost' => .5,
        'id' => 'critical-strike-unarmed-combat',
        'description' => 'Critical strike: unarmed combat description.',
        'effects' => [],
        'incompatible-with' => [
            'critical-strike-unarmed-combat',
        ],
        'name' => 'Critical Strike (Unarmed Combat)',
    ],
    'empathic-healing' => [
        'activation' => 'See description',
        'cost' => 0.5,
        'description' => 'Empathetic healing description.',
        'id' => 'empathic-healing',
        'incompatible-with' => ['empathic-healing'],
        'name' => 'Empathic Healing',
        'page' => 171,
        'ruleset' => 'street-grimoire',
    ],
    'improved-reflexes-3' => [
        'cost' => 3.5,
        'description' => 'Improved reflexes description.',
        'effects' => [
            'reaction' => 3,
            'initiative-dice' => 3,
        ],
        'id' => 'improved-reflexes-3',
        'incompatible-with' => [
            'improved-reflexes-1',
            'improved-reflexes-2',
            'improved-reflexes-3',
        ],
        'level' => 3,
        'name' => 'Improved Reflexes',
        'page' => 310,
        'ruleset' => 'core',
    ],
    'improved-sense-direction-sense' => [
        'cost' => 0.25,
        'description' => 'This power gives you sensory improvements not normally possessed by your character\'s metatype.||Add +2 dice to Navigational skill tests when traveling. In addition, with a Perception + Intuition (2) Test, you can identify the direction you\'re facing and if you\'re above or below the mean sea level.',
        'effects' => [
            'navigation' => 2,
        ],
        'id' => 'improved-sense-direction-sense',
        'incompatible-with' => [
            'improved-sense-direction-sense',
        ],
        'name' => 'Improved Sense: Direction Sense',
        'page' => 310,
        'ruleset' => 'core',
    ],
    'pain-resistance-1' => [
        'cost' => 0.5 * 1,
        'description' => 'Pain resistance description.',
        'id' => 'pain-resistance-1',
        'incompatible-with' => [
            'pain-resistance-1',
            'pain-resistance-2',
            'pain-resistance-3',
            'pain-resistance-4',
            'pain-resistance-5',
            'pain-resistance-6',
        ],
        'level' => 1,
        'name' => 'Pain Resistance',
        'page' => 311,
        'ruleset' => 'core',
    ],
    'rapid-healing-1' => [
        'cost' => 0.5 * 1,
        'description' => 'Rapid healing description.',
        'effects' => [],
        'id' => 'rapid-healing-1',
        'incompatible-with' => [],
        'level' => 1,
        'name' => 'Rapid Healing',
        'page' => 311,
        'ruleset' => 'core',
    ],
];
