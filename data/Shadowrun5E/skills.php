<?php

/**
 * List of skills.
 */

declare(strict_types=1);

return [
    /*
    '' => [
        'id' => '',
        'name' => '',
        'attribute' => '',
        'group' => '',
        'description' => '',
        'magicOnly' => true,
        'limit' => '',
        'specializations' => [
        ],
    ],
     */
    'astral-combat' => [
        'id' => 'astral-combat',
        'name' => 'Astral Combat',
        'attribute' => 'willpower',
        'description' => 'Astral combat description.',
        // Group is optional.
        'magicOnly' => true,
        'limit' => 'weapon',
        'specializations' => [],
    ],
    'automatics' => [
        'id' => 'automatics',
        'name' => 'Automatics',
        'default' => true,
        'group' => 'firearms',
        'attribute' => 'agility',
        'description' => 'Skill description here.',
        'limit' => 'weapon',
        'specializations' => [
            'Assault Rifles',
            'Cyber-Implant',
            'Machine Pistols',
            'Submachine Guns',
        ],
    ],
    'computer' => [
        'id' => 'computer',
        'name' => 'Computer',
        'attribute' => 'logic',
        'default' => true,
        'group' => 'electronics',
        'description' => 'Computer description. Needed for courier sprite.',
        'limit' => 'matrix',
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
    'hacking' => [
        'id' => 'hacking',
        'name' => 'Hacking',
        'attribute' => 'logic',
        'default' => true,
        'group' => 'cracking',
        'description' => 'Hacking description goes here. Needed for courier sprite.',
        'limit' => 'matrix',
        'specializations' => [
            'Devices',
            'Files',
            'Hosts',
            'Personas',
        ],
    ],
];
