<?php

declare(strict_types=1);

use Modules\Transformers\Models\Classification;

return [
    /*
    '' => [
        'class' => Classification::,
        'cost' => ,
        'description' => '',
        'name' => '',
        'requirements' => [
            'allegiance' => '',
            'era' => '',
            'subgroup' => '',
        ],
    ],
    */
    'actionmaster' => [
        'class' => Classification::Standard,
        'cost' => 2,
        'description' => 'Nuclean fuel pumps through the Transformer’s veins, preventing Transformation, but granting Damage Reduction of 1d6 / 2 rounded up. See Partner Units for more options with Actionmaster.',
        'name' => 'Actionmaster',
    ],
    'actionmaster-elite' => [
        'class' => Classification::Minor,
        'cost' => 1,
        'description' => 'For an additional Point, a character can be an “Actionmaster Elite”, meaning they are capable of Transforming, but not as a free Action. If the Robot had an Alt.Mode before becoming an Action Master, they do not need to choose that again as their new Alt. Mode.',
        'name' => 'Actionmaster elite',
        'requirements' => [
            'subgroup' => 'actionmaster',
        ],
    ],
];
