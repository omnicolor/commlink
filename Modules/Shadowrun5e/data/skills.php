<?php

declare(strict_types=1);

/**
 * List of skills.
 */
return [
    /*
    '' => [
        'attribute' => '',
        'default' => true,
        'description' => '',
        'group' => '', // Group is optional.
        'id' => '',
        'limit' => '',
        'magicOnly' => true,
        'name' => '',
        'specializations' => [],
    ],
     */
    'astral-combat' => [
        'attribute' => 'willpower',
        'description' => 'Astral combat description.',
        'id' => 'astral-combat',
        'limit' => 'weapon',
        'magicOnly' => true,
        'name' => 'Astral Combat',
        'specializations' => [],
    ],
    'automatics' => [
        'attribute' => 'agility',
        'default' => true,
        'description' => 'Skill description here.',
        'group' => 'firearms',
        'id' => 'automatics',
        'limit' => 'weapon',
        'name' => 'Automatics',
        'specializations' => [
            'Assault Rifles',
            'Cyber-Implant',
            'Machine Pistols',
            'Submachine Guns',
        ],
    ],
    'blades' => [
        'attribute' => 'agility',
        'default' => true,
        'description' => 'Blades description.',
        'group' => 'close-combat',
        'id' => 'blades',
        'limit' => 'weapon',
        'name' => 'Blades',
        'specializations' => [
            'Axes',
            'Knives',
            'Swords',
            'Parrying',
        ],
    ],
    'computer' => [
        'attribute' => 'logic',
        'default' => true,
        'description' => 'Computer description. Needed for courier sprite.',
        'group' => 'electronics',
        'id' => 'computer',
        'limit' => 'matrix',
        'name' => 'Computer',
        'specializations' => [
            'Edit file',
            'Erase mark',
            'Erase matrix signature',
            'Format device',
            'Jack out',
            'Matrix perception',
            'Matrix search',
            'Reboot device',
            'Trace icon',
        ],
    ],
    'disguise' => [
        'attribute' => 'intuition',
        'default' => true,
        'description' => 'Disguise description.',
        'group' => 'stealth',
        'id' => 'disguise',
        'limit' => 'physical',
        'name' => 'Disguise',
        'specializations' => [
            'Camouflage',
            'Cosmetic',
            'Theatrical',
            'Trideo & Video',
        ],
    ],
    'first-aid' => [
        'attribute' => 'logic',
        'default' => true,
        'description' => 'First aid description.',
        'group' => 'biotech',
        'id' => 'first-aid',
        'limit' => 'mental',
        'name' => 'First Aid',
        'specializations' => [
            'Broken bones',
            'Burns',
            'Gunshot wounds',
            'Resuscitation',
        ],
    ],
    'gymnastics' => [
        'attribute' => 'agility',
        'default' => true,
        'description' => 'Gymnastics description.',
        'group' => 'athletics',
        'id' => 'gymnastics',
        'limit' => 'physical',
        'name' => 'Gymnastics',
        'specializations' => [],
    ],
    'hacking' => [
        'attribute' => 'logic',
        'default' => true,
        'description' => 'Hacking description goes here. Needed for courier sprite.',
        'group' => 'cracking',
        'id' => 'hacking',
        'name' => 'Hacking',
        'limit' => 'matrix',
        'specializations' => [
            'Devices',
            'Files',
            'Hosts',
            'Personas',
        ],
    ],
    'longarms' => [
        'id' => 'longarms',
        'name' => 'Longarms',
        'default' => true,
        'attribute' => 'agility',
        'group' => 'firearms',
        'description' => 'The Longarms skill is for firing extended-barrel weapons such as sporting rifles and sniper rifles. This grouping also includes weapons like shotguns that are designed to be braced against the shoulder.',
        'limit' => 'weapon',
        'specializations' => [
            'Extended-range shots',
            'Long-Range shots',
            'Shotguns',
            'Sniper rifles',
        ],
    ],
    'medicine' => [
        'attribute' => 'logic',
        'description' => 'Medicine description.',
        'group' => 'biotech',
        'id' => 'medicine',
        'limit' => 'mental',
        'name' => 'Medicine',
        'specializations' => [],
    ],
    'palming' => [
        'attribute' => 'agility',
        'description' => 'Palming description.',
        'group' => 'stealth',
        'id' => 'palming',
        'limit' => 'physical',
        'name' => 'Palming',
        'specializations' => [],
    ],
    'perception' => [
        'attribute' => 'intuition',
        'default' => true,
        'description' => 'Perception description.',
        'id' => 'perception',
        'limit' => 'mental',
        'name' => 'Perception',
        'specializations' => [],
    ],
    'pilot-ground-craft' => [
        'attribute' => 'reaction',
        'default' => true,
        'description' => 'Pilot Ground Craft description.',
        'id' => 'pilot-ground-craft',
        'limit' => 'handling',
        'name' => 'Pilot Ground Craft',
        'specializations' => [],
    ],
    'pistols' => [
        'attribute' => 'agility',
        'default' => true,
        'description' => 'Pistols description.',
        'group' => 'firearms',
        'id' => 'pistols',
        'limit' => 'weapon',
        'name' => 'Pistols',
        'specializations' => [],
    ],
    'running' => [
        'attribute' => 'strength',
        'default' => true,
        'description' => 'Running description.',
        'group' => 'athletics',
        'id' => 'running',
        'limit' => 'physical',
        'name' => 'Running',
        'specializations' => [],
    ],
    'sneaking' => [
        'attribute' => 'agility',
        'id' => 'sneaking',
        'name' => 'Sneaking',
        'default' => true,
        'group' => 'stealth',
        'description' => 'Need to get where you\'re not supposed to be? This skill allows you to remain inconspicuous in various situations. See Using Stealth Skills, p. 136.',
        'limit' => 'physical',
        'specializations' => [
            'Desert',
            'Jungle',
            'Urban',
        ],
    ],
    'unarmed-combat' => [
        'attribute' => 'agility',
        'default' => true,
        'description' => 'Unarmed combat description.',
        'group' => 'close-combat',
        'id' => 'unarmed-combat',
        'limit' => 'weapon',
        'name' => 'Unarmed Combat',
        'specializations' => [],
    ],
];
