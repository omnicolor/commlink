<?php

declare(strict_types=1);

/**
 * List of weapon modifications in Shadowrun 5th edition.
 */
return [
    /*
    '' => [
        'availability' => '',
        'cost' => ,
        'cost-modifier' => ,
        'description' => '',
        'effects' => [],
        'id' => '',
        'incompatible-with' => [],
        'mount' => [],
        'name' => '',
        'page' => ,
        'ruleset' => '',
        'type' => '',
    ],
     */
    'bayonet' => [
        'availability' => '4R',
        'cost' => 50,
        'description' => 'Bayonet description.',
        'id' => 'bayonet',
        'mount' => ['top', 'under'],
        'name' => 'Bayonet',
        'ruleset' => 'run-and-gun',
        'type' => 'accessory',
    ],
    'smartlink-internal' => [
        'availability' => '+2R',
        'capacity' => 1,
        'cost-modifier' => 2,
        'description' => 'Smartlink description.',
        'effects' => [
            'accuracy' => 2,
            'dice-bonus' => 1,
            'dice-bonus-essence' => 1,
        ],
        'id' => 'smartlink-internal',
        'incompatible-with' => [
            'ceramic-plasteel-components-1',
            'ceramic-plasteel-components-2',
            'ceramic-plasteel-components-3',
            'ceramic-plasteel-components-4',
            'ceramic-plasteel-components-5',
            'ceramic-plasteel-components-6',
            'smartlink-external',
        ],
        'name' => 'Internal Smartlink',
        'type' => 'modification',
        'wireless-effects' => [
        ],
    ],
];
