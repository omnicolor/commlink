<?php

declare(strict_types=1);

use Modules\Stillfleet\Enums\TechStrata;

return [
    /*
    [
        'id' => '',
        'cost' => ,
        'damage_reduction' => ,
        'name' => '',
        'notes' => '',
        'page' => ,
        'ruleset' => 'core',
        'tech_cost' => ,
        'tech_strata' => TechStrata::,
    ],
    */
    [
        'id' => 'chainmail',
        'cost' => 10,
        'damage_reduction' => 1,
        'name' => 'Chainmail',
        'notes' => '−2 penalty to non-dodge MOV checks.',
        'page' => 156,
        'ruleset' => 'core',
        'tech_cost' => 2,
        'tech_strata' => TechStrata::Clank,
    ],
];
