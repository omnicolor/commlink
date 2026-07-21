<?php

declare(strict_types=1);

return [
    /*
    [
        'anchored' => false,
        'description' => '',
        'id' => '',
        'material_link' => false,
        'minion' => false,
        'name' => '',
        'page' => ,
        'ruleset' => '',
        'spell' => false,
        'spotter' => false,
        'threshold' => ,
    ],
    */
    [
        'anchored' => true,
        'description' => 'This ritual is used to cast a Health spell on a number of targets at once. Even though it’s called a circle, it creates a sphere around the anchor that has a radius in meters equal to the leader’s Magic rating. This ritual takes five hours to complete. The net hits from the sealing step are used as the net hits for the spell and applied as a positive dicepool modifier to any healing tests performed in the circle. If the spell has a particular elemental aspect (e.g., Cooling Heal or Warming Heal), that is also applied to individuals in the circle. The circle lasts for a number of days equal to the net hits on the sealing step.',
        'id' => 'circle-of-healing',
        'material_link' => false,
        'minion' => false,
        'name' => 'Circle Of Healing',
        'page' => 144,
        'ruleset' => 'core',
        'spell' => true,
        'spotter' => false,
        'threshold' => 7,
    ],
];
