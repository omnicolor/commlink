<?php

declare(strict_types=1);

use Modules\Transformers\Models\AltMode;

return [
    'Ambulance' => [
        'rank' => 4,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Audio-visual' => [
        'rank' => 4,
        'restricted' => 'Inanimate',
        'size' => 0,
        'type' => AltMode::TYPE_MACHINE,
    ],
    'Car' => [
        'rank' => 1,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Carnivore' => [
        'rank' => 3,
        'type' => AltMode::TYPE_PRIMITIVE,
    ],
    'Deployer' => [
        'rank' => 1,
        'type' => AltMode::TYPE_MACHINE,
    ],
    'Dune buggy' => [
        'rank' => 3,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Farming' => [
        'rank' => 3,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Herbivore' => [
        'rank' => 1,
        'type' => AltMode::TYPE_PRIMITIVE,
    ],
    'Jeep' => [
        'rank' => 3,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Moon buggy' => [
        'rank' => 2,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Motorcycle' => [
        'rank' => 4,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Platform' => [
        'rank' => 4,
        'type' => AltMode::TYPE_WEAPON,
    ],
    'Race car' => [
        'rank' => 3,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Small vehicle' => [
        'rank' => 2,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Snowmobile' => [
        'rank' => 2,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Sports car' => [
        'rank' => 2,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Tow truck' => [
        'rank' => 4,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Truck' => [
        'rank' => 2,
        'type' => AltMode::TYPE_VEHICLE,
    ],
    'Van' => [
        'rank' => 2,
        'type' => AltMode::TYPE_VEHICLE,
    ],
];
