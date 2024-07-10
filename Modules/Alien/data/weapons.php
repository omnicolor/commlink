<?php

declare(strict_types=1);

use Modules\Alien\Models\Weapon;

return [
    /*
    '' => [
        'bonus' => ,
        'class' => Weapon::CLASS_,
        'cost' => ,
        'damage' => ,
        'description' => '',
        'modifiers' => [],
        'name' => '',
        'page' => ,
        'range' => Weapon::RANGE_,
        'ruleset' => 'core',
        'weight' => ,
    ],
    */
    'm4a3-service-pistol' => [
        'bonus' => 2,
        'class' => Weapon::CLASS_PISTOL,
        'cost' => 200,
        'damage' => 1,
        'description' => 'Service pistol description.',
        'modifiers' => [],
        'name' => 'M4A3 Service Pistol',
        'page' => 119,
        'range' => Weapon::RANGE_MEDIUM,
        'ruleset' => 'core',
        'weight' => 0.5,
    ],
    'spacesub-asso-400-harpoon-grappling-gun' => [
        'bonus' => 0,
        'class' => Weapon::CLASS_RIFLE,
        'cost' => 300,
        'damage' => 1,
        'description' => 'Grappling gun description.',
        'modifiers' => [
            Weapon::MODIFIER_ARMOR_DOUBLED,
            Weapon::MODIFIER_SINGLE_SHOT,
        ],
        'name' => 'SpaceSub ASSO-400 Harpoon Grappling Gun',
        'page' => 120,
        'range' => Weapon::RANGE_MEDIUM,
        'ruleset' => 'core',
        'weight' => 1,
    ],
];
