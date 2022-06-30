<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'agility' => ,
        'armor' => [
        ],
        'augmentations' => [
        ],
        'body' => ,
        'charisma' => ,
        'complex-forms' => [
        ],
        'condition_monitor' => ,
        'description' => '',
        'essence' => 6.0,
        'gear' => [
        ],
        'initiate_grade' => ,
        'initiative_base' => ,
        'initiative_dice' => 1,
        'intuition' => ,
        'knowledge' => [
        ],
        'logic' => ,
        'magic' => ,
        'name' => '',
        'page' => ,
        'professional_rating' => ,
        'qualities' => [
        ],
        'reaction' => ,
        'resonance' => ,
        'ruleset' => '',
        'skill-groups' => [
            'group' => ,
        ],
        'skills' => [
            ['id' => '', 'level' => , 'specialization' => ''],
        ],
        'spells' => [
        ],
        'strength' => ,
        'weapons' => [
        ],
        'willpower' => ,
    ],
     */
    'citizen-soldier' => [
        'agility' => 3,
        'armor' => [
            ['id' => 'armor-clothing'],
        ],
        'body' => 3,
        'charisma' => 2,
        'condition_monitor' => 10,
        'description' => 'Poorly trained, poorly equipped, but ready to defend what’s theirs.',
        'essence' => 6.0,
        'gear' => [
            ['id' => 'commlink-meta-link'],
        ],
        'initiative_base' => 12,
        'initiative_dice' => 4,
        'intuition' => 3,
        'logic' => 2,
        'name' => 'Citizen soldier',
        'page' => 214,
        'professional_rating' => 1,
        'reaction' => 3,
        'ruleset' => 'lockdown',
        'skill-groups' => [
            'athletics' => 1,
            'close-combat' => 2,
            'firearms' => 2,
        ],
        'skills' => [
            ['id' => 'perception', 'level' => 2],
            ['id' => 'sneaking', 'level' => 1],
        ],
        'strength' => 3,
        'weapons' => [
            ['id' => 'defiance-t-250'],
            ['id' => 'colt-america-l36'],
        ],
        'willpower' => 2,
    ],
    'pr-0' => [
        'agility' => 3,
        'body' => 3,
        'charisma' => 2,
        'condition_monitor' => 10,
        'description' => 'These are the kind of knuckle-dragging, slope-browed Neanderthals that typify the phrase “angry mob.” They’re employed by the likes of the Humanis policlub or TerraFirst!, or they simply coalesce on the street whenever something bad is about to happen. They’re used to rough up and intimidate random groups of people. They’re no match for an experienced combatant, however, and if they meet any real resistance, they’re out of there.',
        'essence' => 6.0,
        'gear' => [
            ['id' => 'commlink-meta-link'],
        ],
        'initiative_base' => 6,
        'initiative_dice' => 1,
        'intuition' => 3,
        'logic' => 2,
        'name' => 'Thugs & mouth breathers',
        'page' => 381,
        'professional_rating' => 0,
        'reaction' => 3,
        'ruleset' => 'core',
        'skills' => [
            ['id' => 'blades', 'level' => 3],
            ['id' => 'clubs', 'level' => 3],
            ['id' => 'intimidation', 'level' => 3],
            ['id' => 'unarmed-combat', 'level' => 3],
        ],
        'strength' => 3,
        'weapons' => [
            ['id' => 'club'],
            ['id' => 'combat-knife'],
        ],
        'willpower' => 3,
    ],
    'pr-4-lieutenant' => [
        'agility' => 3,
        'armor' => [
            ['id' => 'lined-coat'],
        ],
        'body' => 3,
        'charisma' => 4,
        'complex-forms' => [
            'cleaner',
            'diffusion-of-data-processing',
            'diffusion-of-firewall',
            'editor',
            'infusion-of-attack',
            'infusion-of-data-processing',
            'resonance-spike',
            'tattletale',
            'transcendent-grid',
        ],
        'condition_monitor' => 10,
        'description' => 'Technomancers are rare commodities in the Sixth World. Often made into pariahs by normal society, many find they have no choice but to use their extraordinary gift in pursuit of a life of crime. The various criminal syndicates, while leery of them, are not going to turn them down. Even the hidebound Mafia won’t reject a technomancer willing to lead or support a squad of soldiers on some particularly important errand.',
        'essence' => 6.0,
        'gear' => [
            ['id' => 'commlink-erika-elite'],
        ],
        'initiative_base' => 9,
        'initiative_dice' => 1,
        'intuition' => 5,
        'logic' => 5,
        'name' => 'Organized crime gang - Lieutenant',
        'page' => 383,
        'professional_rating' => 4,
        'qualities' => [
            ['id' => 'natural-hardening'],
        ],
        'reaction' => 4,
        'resonance' => 5,
        'ruleset' => 'core',
        'skills' => [
            ['id' => 'compiling', 'level' => 7],
            ['id' => 'computer', 'level' => 5],
            ['id' => 'cybercombat', 'level' => 6],
            ['id' => 'decompiling', 'level' => 6],
            ['id' => 'leadership', 'level' => 4],
            ['id' => 'perception', 'level' => 5],
            ['id' => 'pistols', 'level' => 3],
            ['id' => 'registering', 'level' => 7],
            ['id' => 'software', 'level' => 6],
        ],
        'strength' => 3,
        'weapons' => [
            ['id' => 'beretta-201t'],
        ],
        'willpower' => 5,
    ],
    'pr-6-lieutenant' => [
        'adept_powers' => [
            'improved-ability-automatics-3',
            'improved-agility-3',
            'improved-reflexes-3',
        ],
        'agility' => 9,
        'armor' => [
            ['id' => 'full-body-armor'],
            ['id' => 'full-body-armor-helmet'],
        ],
        'body' => 6,
        'charisma' => 5,
        'condition_monitor' => 11,
        'description' => 'Not all warriors on the Sixth World’s battlefields are augmented. The world’s military forces are always happy to accept magical recruits. Adepts fight alongside their cybernetic brothers-in-arms, and they are every bit as fast and deadly.',
        'essence' => 6.0,
        'gear' => [
            ['id' => 'grapple-gun'],
            ['id' => 'commlink-hermes-ikon'],
            ['id' => 'qi-focus-strength-6'],
        ],
        'initiate_grade' => 2,
        'initiative_base' => 15,
        'initiative_dice' => 4,
        'intuition' => 6,
        'logic' => 5,
        'magic' => 6,
        'name' => 'Elite special forces - Lieutenant',
        'page' => 384,
        'professional_rating' => 6,
        'reaction' => 9,
        'ruleset' => 'core',
        'skill-groups' => [
            'athletics' => 7,
            'close-combat' => 8,
            'firearms' => 9,
            'stealth' => 6,
        ],
        'skills' => [
            ['id' => 'demolitions', 'level' => 7],
            ['id' => 'perception', 'level' => 7],
        ],
        'strength' => 8,
        'weapons' => [
            ['id' => 'hk-227'],
            ['id' => 'grenade-smoke'],
            ['id' => 'grenade-smoke'],
            ['id' => 'grenade-thermal-smoke'],
            ['id' => 'grenade-thermal-smoke'],
        ],
        'willpower' => 5,
    ],
    'security-mage' => [
        'agility' => 3,
        'armor' => [
            ['id' => 'armored-jacket'],
        ],
        'body' => 3,
        'charisma' => 3,
        'condition_monitor' => 10,
        'description' => '',
        'essence' => 6.0,
        'gear' => [
            ['id' => 'contacts-2', 'mods' => ['image-link', 'smartlink']],
            ['id' => 'commlink-erika-elite'],
            ['id' => 'mage-sight-gogles'],
            ['id' => 'focus-sustaining-health-2'],
        ],
        'initiative_base' => 8,
        'initiative_dice' => 1,
        'intuition' => 4,
        'knowledge' => [
            [
                'category' => 'professional',
                'name' => 'Small Unit Tactics',
                'level' => 4,
            ],
        ],
        'logic' => 5,
        'magic' => 5,
        'name' => 'Security mage',
        'page' => 159,
        'professional_rating' => 4,
        'reaction' => 4,
        'ruleset' => 'bloody-business',
        'skills' => [
            ['id' => 'assensing', 'level' => 3],
            ['id' => 'counterspelling', 'level' => 4],
            ['id' => 'etiquette', 'level' => 3, 'specialization' => 'Corporate'],
            ['id' => 'pistols', 'level' => 4],
            ['id' => 'spellcasting', 'level' => 5, 'specialization' => 'Illusion'],
            ['id' => 'summoning', 'level' => 5],
        ],
        'spells' => [
            'bugs',
            'hot-potato',
            'increase-reflexes',
            'mass-confusion',
            'phantasm',
        ],
        'strength' => 2,
        'weapons' => [
            ['id' => 'ares-predator-v'],
        ],
        'willpower' => 5,
    ],
];
