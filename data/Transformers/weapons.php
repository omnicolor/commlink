<?php

declare(strict_types=1);

use App\Models\Transformers\Classification;

return [
    /*
    '' => [
        'class' => Classification::,
        'damage' => '',
        'explanation' => '',
        'name' => '',
    ],
     */
    'buzzsaw' => [
        'class' => Classification::Minor,
        'damage' => '1d6 vs. HP + 2x Map + Radius',
        'explanation' => 'Melee range. Hand-replacement. Can also be Chainsaw.',
        'name' => 'Buzzsaw',
    ],
    'gun-gyro' => [
        'class' => Classification::Standard,
        'damage' => '1d6 vs. Speed & Accuracy + Fall',
        'explanation' => 'Disturbs the balance centre of Robots. Target immune to the Accuracy DMG if they do not have Accuracy.',
    ],
];
