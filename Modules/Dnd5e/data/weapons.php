<?php

declare(strict_types=1);

use Modules\Dnd5e\Enums\CoinType;
use Modules\Dnd5e\Enums\DamageType;

/**
 * Weapons list for Dungeons and Dragons 5E.
 */
return [
    /*
    '' => [
        'cost' => ,
        'currency' => CoinType::,
        'damage' => '',
        'damage-type' => DamageType::,
        'id' => '',
        'name' => '',
        'page' => ,
        'properties' => [
        ],
        'ruleset' => 'core',
        'weight' => ,
    ],
     */
    'club' => [
        'cost' => 1,
        'currency' => CoinType::Silver,
        'damage' => '1d4',
        'damage-type' => DamageType::Bludgeoning,
        'id' => 'club',
        'name' => 'Club',
        'page' => 149,
        'properties' => [
            'light',
        ],
        'ruleset' => 'core',
        'weight' => 2,
    ],
];
