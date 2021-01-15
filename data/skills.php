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
        'description' => '',
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
];
