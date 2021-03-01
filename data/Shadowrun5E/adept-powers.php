<?php

/**
 * List of Shadowrun 5E adept powers.
 */

declare(strict_types=1);

return [
    /*
    '' => [
        'cost' => ,
        'description' => '',
        'effects' => [],
        'id' => '',
        'incompatible-with' => [],
        'level' => ,
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
    */
    'improved-sense-direction-sense' => [
        'cost' => 0.25,
        'description' => 'This power gives you sensory improvements not normally possessed by your character\'s metatype.||Add +2 dice to Navigational skill tests when traveling. In addition, with a Perception + Intuition (2) Test, you can identify the direction you\'re facing and if you\'re above or below the mean sea level.',
        'effects' => [
            'navigation' => 2,
        ],
        'id' => 'improved-sense-direction-sense',
        'incompatible-with' => [
            'improved-sense-direction-sense',
        ],
        'name' => 'Improved Sense: Direction Sense',
        'page' => 310,
        'ruleset' => 'core',
    ],
];
