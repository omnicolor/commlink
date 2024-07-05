<?php

declare(strict_types=1);

use Modules\Shadowrun5e\Models\Vehicle;
use Modules\Shadowrun5e\Models\VehicleModificationSlotType;
use Modules\Shadowrun5e\Models\VehicleModificationType;

/**
 * Data file for Shadowrun 5th edition vehicle modifications.
 *
 * A vehicle mod can either have a plain cost or a cost that is based on the
 * vehicle it's being added to. For modifications that cost the same no matter
 * what kind of vehicle they're added to, just use the 'cost' field and leave
 * 'cost-attribute' and 'cost-multiplier' fields empty or unset.
 *
 * For modifications that depend on particular attributes of the vehicle being
 * modified, use the 'cost' and 'cost-attribute' fields. For example, if the
 * cost is listed as "Accel × 10,000¥", use 'cost' as 10000 and 'cost-attribute'
 * as 'acceleration'.
 *
 * For modifications that depend on the cost of the vehicle, such as Off-road
 * suspension (Rigger 5.0 p158) that cost "Vehicle cost × 25%", leave the 'cost'
 * and 'cost-attribute' fields unset and set 'cost-multiplier' to 0.25.
 */
return [
    /*
    '' => [
        'availability' => '',
        'cost' => ,
        'cost-attribute' => '',
        'cost-multiplier' => '',
        'description' => '',
        'effects' => [],
        'id' => '',
        'name' => '',
        'page' => ,
        'rating' => ,
        'requirements' => [],
        'ruleset' => '',
        'slot-type' => '', // VehicleModificationSlotType
        'slots' => ,
        'type' => '', // VehicleModificationType
    ],
     */
    'acceleration-enhancement-1' => [
        'availability' => '6',
        'cost' => 10000,
        'cost-attribute' => 'acceleration',
        'description' => 'description of acceleration enhancement.',
        'effects' => [
            'acceleration' => 1,
        ],
        'id' => 'acceleration-enhancement-1',
        'name' => 'Acceleration enhancement',
        'page' => 154,
        'rating' => 1,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::PowerTrain,
        'slots' => 4,
        'type' => VehicleModificationType::VehicleModification,
    ],
    'control-remote' => [
        'availability' => '',
        'cost' => 0,
        'description' => 'Remote control weapon mount description.',
        'id' => 'control-remote',
        'name' => 'Control: remote',
        'page' => 163,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::Weapons,
        'slots' => 0,
        'type' => VehicleModificationType::ModificationModification,
    ],
    'flexibility-fixed' => [
        'availability' => '',
        'cost' => 0,
        'description' => 'Fixed flexibility weapon mount description.',
        'id' => 'flexibility-fixed',
        'name' => 'Flexibility: fixed',
        'page' => 163,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::Weapons,
        'slots' => 0,
        'type' => VehicleModificationType::ModificationModification,
    ],
    'flexibility-flexible' => [
        'availability' => '+2',
        'cost' => 2000,
        'description' => 'Description of a flexible weapon mount.',
        'id' => 'flexibility-flexible',
        'name' => 'Flexibility: flexible',
        'page' => 163,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::Weapons,
        'slots' => 1,
        'type' => VehicleModificationType::ModificationModification,
    ],
    'gecko-tips-small' => [
        'availability' => '6',
        'cost' => 1000,
        'description' => 'description of gecko tips.',
        'id' => 'gecko-tips-small',
        'name' => 'Gecko tips (small vehicle)',
        'page' => 154,
        'requirements' => [
            function (Vehicle $vehicle): bool {
                return $vehicle->body <= 3;
            },
        ],
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::PowerTrain,
        'slots' => 1,
        'type' => VehicleModificationType::VehicleModification,
    ],
    'improved-economy' => [
        'availability' => '4',
        'cost' => 7500,
        'description' => 'Improved economy description.',
        'id' => 'improved-economy',
        'name' => 'Improved economy',
        'page' => 154,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::PowerTrain,
        'slots' => 2,
        'type' => VehicleModificationType::VehicleModification,
    ],
    'manual-control-override' => [
        'availability' => '6',
        'cost' => 500,
        'description' => 'Manual control override description.',
        'id' => 'manual-control-override',
        'name' => 'Manual Control Override',
        'page' => 154,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::PowerTrain,
        'slots' => 1,
        'type' => VehicleModificationType::VehicleModification,
    ],
    'off-road-suspension' => [
        'availability' => '4',
        'cost-multiplier' => 0.25,
        'description' => 'Off-road suspension description.',
        'effects' => [
            'handling' => -1,
            'handlingOffRoad' => 1,
        ],
        'id' => 'off-road-suspension',
        'name' => 'Off-road suspension',
        'page' => 155,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::PowerTrain,
        'slots' => 2,
        'type' => VehicleModificationType::VehicleModification,
    ],
    'rigger-interface' => [
        'availability' => '4',
        'cost' => 1000,
        'description' => 'Rigger interface description.',
        'id' => 'rigger-interface',
        'name' => 'Rigger Interface',
        'page' => 461,
        'ruleset' => 'core',
    ],
    'sensor-array-2' => [
        'availability' => '7',
        'cost' => 1000 * 2,
        'description' => 'Sensor array description.',
        'id' => 'sensor-array-2',
        'name' => 'Sensor Array',
        'page' => 445,
        'rating' => 2,
        'ruleset' => 'core',
    ],
    'visibility-external' => [
        'availability' => '',
        'cost' => 0,
        'description' => '',
        'id' => 'visibility-external',
        'name' => 'Visibility: external',
        'page' => 163,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::Weapons,
        'slots' => 0,
        'type' => VehicleModificationType::ModificationModification,
    ],
    'visibility-internal' => [
        'availability' => '+2',
        'cost' => 1500,
        'description' => 'Internal visibility modifier description.',
        'id' => 'visibility-internal',
        'name' => 'Internal visibility',
        'page' => 163,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::Weapons,
        'slots' => 2,
        'type' => VehicleModificationType::ModificationModification,
    ],
    'weapon-mount-standard' => [
        'allowedWeapons' => [
            'Assault Rifle',
            'Exotic',
            'Flamethrower',
            'Heavy Pistol',
            'Hold-Out Pistol',
            'Light Pistol',
            'Machine Pistol',
            'Shotgun',
            'Sniper Rifle',
            'Taser',
        ],
        'availability' => '8F',
        'cost' => 1500,
        'description' => 'Shadowrunning is not a business where one wants to be defenseless (or offense-less, for that matter), so riggers often awant to put weapons on their vehicle. While a small number of security or military-class vehicles are designed to mount weapon systems, the vast majority of vehicles are designed and built for civilian use, so installing a weapon system is strictly an after-market affair. Just like the weapons that they support, weapon mounts come in a large variety of shapes and sizes to suit the needs of the weapon as well as the owner. All weapon mounts have four attributes: Size, Visibility, Flexibility, and Control.',
        'id' => 'weapon-mount-standard',
        'name' => 'Weapon mount, standard',
        'page' => 163,
        'ruleset' => 'rigger-5',
        'slot-type' => VehicleModificationSlotType::Weapons,
        'slots' => 2,
        'type' => VehicleModificationType::VehicleModification,
    ],
];
