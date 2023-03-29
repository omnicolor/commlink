<?php

declare(strict_types=1);

use App\Models\Shadowrun5e\Vehicle;

/**
 * Data file for Shadowrun 5th edition vehicle modifications.
 *
 * A vehicle mod can either have a plain cost or a cost that is based on the
 * vehicle it's being added to. For modifications that cost the same no matter
 * what kind of vehicle they're added to, just use the 'cost' field and leave
 * 'cost-attribute' empty or unset. For modifications that depend on the vehicle
 * being modified, use the 'cost' and 'cost-attribute' fields. For example, if
 * the cost is listed as "Accel × 10,000¥", use 'cost' as 10000 and
 * 'cost-attribute' as 'acceleration'.
 */
return [
    /*
    '' => [
        'availability' => '',
        'cost' => ,
        'cost-multiplier' => '',
        'description' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'rating' => ,
        'ruleset' => '',
        'slot-type' => '',
        'slots' => ,
        'type' => '',
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
        'slot-type' => 'power-train',
        'slots' => 4,
        'type' => 'vehicle-mod',
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
        'slot-type' => 'power-train',
        'slots' => 1,
        'type' => 'vehicle-mod',
    ],
    'manual-control-override' => [
        'availability' => '6',
        'cost' => 500,
        'description' => 'Manual control override description.',
        'id' => 'manual-control-override',
        'name' => 'Manual Control Override',
        'page' => 154,
        'ruleset' => 'rigger-5',
        'slot-type' => 'Power train',
        'slots' => 1,
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
];
