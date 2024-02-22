<?php

declare(strict_types=1);

/**
 * List of ammunition.
 */
return [
    /*
    '' => [
        'id' => '',
        'ap-modifier' => ,
        'availability' => '',
        'cost' => ,
        'damage-modifier' => '',
        'description' => '',
        'name' => '',
        'page' => '',
        'ruleset' => '',
    ],
     */
    'apds' => [
        'id' => 'apds',
        'ap-modifier' => -4,
        'availability' => '12F',
        'cost' => 120,
        'description' => 'These are military-grade armor piercing rounds—their full name is armor piercing discarding sabot. They are designed to travel at high velocities and punch through personal body armor.',
        'name' => 'APDS',
        'page' => 433,
        'ruleset' => 'core',
    ],
    'depleted-uranium' => [
        'id' => 'depleted-uranium',
        'ap-modifier' => -5,
        'availability' => '28F',
        'cost' => 1000,
        'damage-modifier' => '+1',
        'description' => '',
        'name' => 'Depleted uranium',
        'ruleset' => 'hard-targets',
    ],
    'regular-ammo' => [
        'id' => 'regular-ammo',
        'availability' => '2R',
        'cost' => 20,
        'description' => 'Also called ball or full metal jacket rounds, these solid slugs are useful for numerous applications (mainly killing things).',
        'name' => 'Regular ammo',
        'page' => 433,
        'ruleset' => 'core',
    ],
];
