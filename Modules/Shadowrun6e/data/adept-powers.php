<?php

declare(strict_types=1);

use Modules\Shadowrun6e\Enums\AdeptPowerActivation;

return [
    /*
    [
        'activation' => AdeptPowerActivation::,
        'cost' => ,
        'description' => '',
        'effects' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
    */
    [
        'activation' => AdeptPowerActivation::MinorAction,
        'cost' => 0.25,
        'description' => 'You get a surge of energy so you can get the jump on others. For each level of the power, add 2 to your initiative score for (Magic) combat rounds. At the the end of those combat rounds, experience drain equal to the level of the power, which you can resist as normal.',
        'effects' => '{"initiative-score":2}',
        'id' => 'adrenaline-boost-1',
        'name' => 'Adrenaline Boost (1)',
        'page' => 156,
        'ruleset' => 'core',
    ],
    [
        'activation' => AdeptPowerActivation::MinorAction,
        'cost' => 1,
        'description' => 'You gain the ability to astrally perceive and follow the rules involved in so doing (see p. 159). You are dual-natured while using this power and can attack astral forms.',
        'effects' => null,
        'id' => 'astral-perception',
        'name' => 'Astral Perception',
        'page' => 156,
        'ruleset' => 'core',
    ],
];
