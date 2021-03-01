<?php

/**
 * List of qualities.
 */

declare(strict_types=1);

return [
    /*
    '' => [
        'adeptOnly' => true,
        'changelingOnly' => true,
        'description' => '',
        'effects' => [],
        'id' => '',
        'incompatible-with' => [],
        'karma' => ,
        'level' => ,
        'magicOnly' => true,
        'name' => '',
        'notDoubled' => true,
        'page' => ,
        'requires' => [],
        'ruleset' => '',
        'severity' => '',
        'technomancerOnly' => true,
    ],
     */
    'addiction-mild' => [
        'id' => 'addiction-mild',
        'description' => 'Addiction description.',
        'effects' => [
            'notoriety' => 1,
        ],
        'karma' => 4,
        'name' => 'Addiction',
        'severity' => 'Mild',
    ],
    'allergy-uncommon-mild' => [
        'id' => 'allergy-uncommon-mild',
        'description' => 'Allergy description.',
        'karma' => 5,
        'name' => 'Allergy',
        'severity' => 'Uncommon Mild',
    ],
    'alpha-junkie' => [
        'description' => 'Alpha junkie description.',
        'id' => 'alpha-junkie',
        'incompatible-with' => ['alpha-junkie'],
        'karma' => 12,
        'name' => 'Alpha Junkie',
        'page' => 151,
        'requires' => [],
        'ruleset' => 'cutting-aces',
    ],
    'aptitude-alchemy' => [
        'description' => 'Aptitude description.',
        'effects' => ['maximum-alchemy' => 1],
        'id' => 'aptitude-alchemy',
        'incompatible-with' => ['aptitude-alchemy', 'aptitude-arcana', 'aptitude-archery', 'aptitude-aeronautics-mechanic', 'aptitude-animal-handling', 'aptitude-armorer', 'aptitude-artificing', 'aptitude-artisan', 'aptitude-assensing', 'aptitude-astral-combat', 'aptitude-automatics', 'aptitude-automotive-mechanic', 'aptitude-banishing', 'aptitude-binding', 'aptitude-biotechnology', 'aptitude-blades', 'aptitude-chemistry', 'aptitude-clubs', 'aptitude-compiling', 'aptitude-computer', 'aptitude-con', 'aptitude-counterspelling', 'aptitude-cybercombat', 'aptitude-cybertechnology', 'aptitude-decompiling', 'aptitude-demolitions', 'aptitude-disenchanting', 'aptitude-disguise', 'aptitude-diving', 'aptitude-electronic-warfare', 'aptitude-escape-artist', 'aptitude-etiquette', 'aptitude-exotic-melee-weapon', 'aptitude-exotic-ranged-weapon', 'aptitude-first-aid', 'aptitude-forgery', 'aptitude-free-fall', 'aptitude-gunnery', 'aptitude-gymnastics', 'aptitude-hacking', 'aptitude-hardware', 'aptitude-heavy-weapons', 'aptitude-impersonation', 'aptitude-industrial-mechanic', 'aptitude-instruction', 'aptitude-intimidation', 'aptitude-leadership', 'aptitude-locksmith', 'aptitude-longarms', 'aptitude-medicine', 'aptitude-nautical-mechanic', 'aptitude-navigation', 'aptitude-negotiation', 'aptitude-palming', 'aptitude-perception', 'aptitude-performance', 'aptitude-pilot-aerospace', 'aptitude-pilot-aircraft', 'aptitude-pilot-exotic-vehicle', 'aptitude-pilot-ground-craft', 'aptitude-pilot-walker', 'aptitude-pilot-watercraft', 'aptitude-pistols', 'aptitude-registering', 'aptitude-ritual-spellcasting', 'aptitude-running', 'aptitude-software', 'aptitude-sneaking', 'aptitude-spellcasting', 'aptitude-summoning', 'aptitude-survival', 'aptitude-swimming', 'aptitude-throwing-weapons', 'aptitude-tracking', 'aptitude-unarmed-combat'],
        'karma' => -14,
        'name' => 'Aptitude',
        'page' => 72,
        'ruleset' => 'core',
        'skill' => 'Alchemy',
    ],
    'exceptional-attribute-body' => [
        'id' => 'exceptional-attribute-body',
        'attribute' => 'Body',
        'effects' => [
            'maximum-body' => 1,
        ],
        'incompatible-with' => [
            'exceptional-attribute-agility',
            'exceptional-attribute-body',
            'exceptional-attribute-charisma',
            'exceptional-attribute-intuition',
            'exceptional-attribute-logic',
            'exceptional-attribute-magic',
            'exceptional-attribute-reaction',
            'exceptional-attribute-resonance',
            'exceptional-attribute-strength',
            'exceptional-attribute-willpower',
        ],
        'name' => 'Exceptional Attribute',
        'karma' => -14,
        'description' => 'Exceptional attribute description.',
    ],
    'fame-local' => [
        'id' => 'fame-local',
        'incompatible-with' => [
            'fame-local',
            'fame-global',
            'fame-megacorporate',
            'fame-national',
        ],
        'name' => 'Fame',
        'karma' => -4,
        'requires' => [
            [
                'ids' => [
                    'sinner-national',
                    'sinner-criminal',
                    'sinner-corporate-limited',
                    'sinner-corporate',
                ],
                'name' => 'Sinner',
                'type' => 'qualities',
            ],
        ],
        'ruleset' => 'run-faster',
        'severity' => 'local',
        'description' => 'Fame description.',
    ],
    'indomitable-2' => [
        'id' => 'indomitable-2',
        'name' => 'Indomitable',
        'karma' => -16,
        'level' => 2,
        'effects' => [
            'mental-limit' => 0,
            'physical-limit' => 0,
            'social-limit' => 0,
        ],
        'incompatible-with' => [
            'indomitable-1',
            'indomitable-2',
            'indomitable-3',
        ],
        'description' => 'Indomitable description.',
    ],
    'lucky' => [
        'id' => 'lucky',
        'description' => 'Lucky description.',
        'effects' => [
            'maximum-edge' => 7,
            'notoriety' => -1,
        ],
        'incompatible-with' => [
            'lucky',
        ],
        'karma' => -12,
        'name' => 'Lucky',
    ],
];
