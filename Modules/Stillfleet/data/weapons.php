<?php

declare(strict_types=1);

use Modules\Stillfleet\Enums\TechStrata;
use Modules\Stillfleet\Enums\WeaponType;

return [
    /*
    [
        'damage' => '',
        'id' => '',
        'name' => '',
        'notes' => '',
        'other_names' => null,
        'page' => ,
        'price' => ,
        'range' => null,
        'ruleset' => 'core',
        'tech_cost' => ,
        'tech_strata' => TechStrata::,
        'type' => WeaponType::,
    ],
     */
    [
        'damage' => 'd4',
        'id' => 'club',
        'name' => 'Club',
        'notes' => 'Any simple tool for beating things.',
        'other_names' => 'brass knuckles,claw hammer,lead pipe,nunchaku,quarterstaff',
        'page' => 150,
        'price' => 'd4',
        'range' => null,
        'ruleset' => 'core',
        'tech_cost' => 1,
        'tech_strata' => TechStrata::Clank,
        'type' => WeaponType::Melee,
    ],
    [
        'damage' => '2d10',
        'id' => 'musket',
        'name' => 'Musket',
        'notes' => '0.5–0.9-caliber, smoothbore. After you have attacked once, you must spend one round reloading. Two-handed. Doubles as a club.',
        'other_names' => null,
        'page' => 152,
        'price' => 50,
        'range' => '80 M',
        'ruleset' => 'core',
        'tech_cost' => 3,
        'tech_strata' => TechStrata::Clank,
        'type' => WeaponType::Missile,
    ],
    [
        'damage' => 'd2',
        'id' => 'unarmed',
        'name' => 'Unarmed',
        'notes' => 'Cannot be well-made.',
        'other_names' => null,
        'page' => 150,
        'price' => 0,
        'range' => null,
        'ruleset' => 'core',
        'tech_cost' => 1,
        'tech_strata' => TechStrata::Bio,
        'type' => WeaponType::Melee,
    ],
];
