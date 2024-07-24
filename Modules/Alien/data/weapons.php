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
    '357-magnum-revolver' => [
        'bonus' => 1,
        'class' => Weapon::CLASS_PISTOL,
        'cost' => 300,
        'damage' => 2,
        'description' => 'Magnum description.',
        'modifiers' => [],
        'name' => '.357 Magnum Revolver',
        'page' => 119,
        'range' => Weapon::RANGE_MEDIUM,
        'ruleset' => 'core',
        'weight' => 1,
    ],
    'armat-m41ae2-heavy-pulse-rifle' => [
        'bonus' => 1,
        'class' => Weapon::CLASS_HEAVY_WEAPON,
        'cost' => 1500,
        'damage' => 3,
        'description' => 'Pulse rifle description.',
        'modifiers' => [
            Weapon::MODIFIER_ARMOR_PIERCING,
            Weapon::MODIFIER_FULL_AUTO,
        ],
        'name' => 'Armat M41AE2 Heavy Pulse Rifle',
        'page' => 124,
        'range' => Weapon::RANGE_EXTREME,
        'ruleset' => 'core',
        'weight' => 2,
    ],
    'g2-electroshock-grenade' => [
        'bonus' => 0,
        'class' => Weapon::CLASS_HEAVY_WEAPON,
        'cost' => 400,
        'damage' => null,
        'description' => 'Grenade description.',
        'modifiers' => [
            Weapon::MODIFIER_STUN_EFFECT_2,
        ],
        'name' => 'G2 Electroshock Grenade',
        'page' => 124,
        'range' => Weapon::RANGE_MEDIUM,
        'ruleset' => 'core',
        'weight' => 0.5,
    ],
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
    'm56a2-smart-gun' => [
        'bonus' => 3,
        'class' => Weapon::CLASS_HEAVY_WEAPON,
        'cost' => 6000,
        'damage' => 3,
        'description' => 'Smart gun description.',
        'modifiers' => [
            Weapon::MODIFIER_ARMOR_PIERCING,
            Weapon::MODIFIER_FULL_AUTO,
        ],
        'name' => 'M56A2 Smart Gun',
        'page' => 124,
        'range' => Weapon::RANGE_LONG,
        'ruleset' => 'core',
        'weight' => 3,
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
