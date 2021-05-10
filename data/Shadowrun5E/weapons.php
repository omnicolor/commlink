<?php

declare(strict_types=1);

/**
 * List of Shadowrun 5th edition weapons.
 */
return [
    /*
    '' => [
        'accuracy' => '',
        'armor-piercing' => ,
        'availability' => '',
        'class' => '',
        'cost' => ,
        'damage' => '',
        'description' => '',
        'id' => '',
        'image' => '',
        'name' => '',
        'page' => ,
        'reach' => ,
        'ruleset' => '',
        'skill' => '',
        'type' => 'melee',
    ],
    '' => [
        'accuracy' => '',
        'ammo-capacity' => ,
        'ammo-container' => '',
        'armor-piercing' => ,
        'availability' => '',
        'class' => '',
        'cost' => ,
        'damage' => '',
        'description' => '',
        'id' => '',
        'image' => '',
        'modes' => [],
        'modifications' => [],
        'mounts' => [],
        'name' => '',
        'page' => ,
        'recoil-compensation' => ,
        'ruleset' => '',
        'skill' => '',
        'type' => 'firearm',
    ],
    */
    'ak-98' => [
        'id' => 'ak-98',
        'accuracy' => 5,
        'ammo-capacity' => 38,
        'ammo-container' => 'c',
        'armor-piercing' => -2,
        'availability' => '8F',
        'class' => 'Assault Rifle',
        'cost' => 1250,
        'damage' => '10P',
        'description' => 'AK-98 description.',
        'modes' => ['SA', 'BF', 'FA'],
        // TODO Add grenade launcher when supported
        //'modifications' => ['grenade-launcher'],
        'mounts' => ['top', 'barrel', 'stock'],
        'name' => 'AK-98',
        'range' => '25/150/350/550',
        'ruleset' => 'run-and-gun',
        'skill' => 'automatics',
        'type' => 'firearm',
    ],
    'ares-predator-v' => [
        'accuracy' => 5,
        'ammo-capacity' => 15,
        'ammo-container' => 'c',
        'armor-piercing' => -1,
        'availability' => '5R',
        'class' => 'Heavy Pistol',
        'cost' => 725,
        'damage' => '8P',
        'description' => 'Ares Predator description.',
        'id' => 'ares-predator-v',
        'image' => '/images/ares-predator-v.png',
        'modes' => ['SA'],
        'modifications' => ['smartlink-internal'],
        'mounts' => ['barrel', 'top'],
        'name' => 'Ares Predator V',
        'page' => 426,
        'ruleset' => 'core',
        'skill' => 'pistol',
        'type' => 'firearm',
    ],
    'combat-knife' => [
        'accuracy' => 6,
        'armor-piercing' => -3,
        'availability' => '4',
        'class' => 'Blade',
        'cost' => 300,
        'damage' => '(STR+2)P',
        'description' => 'Combat knife description',
        'id' => 'combat-knife',
        'mounts' => ['stock'],
        'name' => 'Combat Knife',
        'page' => 422,
        'reach' => 0,
        'ruleset' => 'core',
        'skill' => 'blades',
        'type' => 'melee',
    ],
];
