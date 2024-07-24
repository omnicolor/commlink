<?php

declare(strict_types=1);

use Modules\Alien\Models\Gear;

return [
    /*
    '' => [
        'category' => Gear::CATEGORY_,
        'cost' => ,
        'description' => '',
        'effects' => [],
        'effects_text' => '',
        'name' => '',
        'page' => ,
        'ruleset' => 'core',
        'weight' => ,
    ],
     */
    'deck-of-cards' => [
        'category' => Gear::CATEGORY_MISCELLANEOUS,
        'cost' => 4,
        'description' => 'A deck of cards. The graphics on the backs is up to you.',
        'effects' => [],
        'effects_text' => '',
        'name' => 'Deck of Cards',
        'page' => 0,
        'ruleset' => 'core',
        'weight' => 0,
    ],
    'm314-motion-tracker' => [
        'category' => Gear::CATEGORY_VISION,
        'cost' => 1200,
        'description' => 'Motion tracker description.',
        'effects' => [],
        'effects_text' => 'See page 86. LONG range indoors.',
        'name' => 'M314 Motion Tracker',
        'page' => 133,
        'ruleset' => 'core',
        'weight' => 1,
    ],
    'neversleep-pills' => [
        'category' => Gear::CATEGORY_PHARMACEUTICALS,
        'cost' => 2,
        'description' => 'Neversleep pills description.',
        'effects' => [],
        'effects_text' => 'STRESS LEVEL +1 per dose.',
        'name' => 'Neversleep Pills',
        'page' => 136,
        'ruleset' => 'core',
        'weight' => 0,
    ],
    'optical-scope' => [
        'category' => Gear::CATEGORY_VISION,
        'cost' => 60,
        'description' => 'Optical scope description.',
        'effects' => [],
        'effects_text' => 'Range increased one category',
        'name' => 'Optical Scope',
        'page' => 133,
        'ruleset' => 'core',
        'weight' => 0,
    ],
    'signal-flare' => [
        'category' => Gear::CATEGORY_TOOLS,
        'cost' => 10,
        'description' => 'A small flare for lighting things up, marking locations, or burning things.',
        'effects' => [],
        'effects_text' => '',
        'name' => 'Signal Flare',
        'page' => 0,
        'ruleset' => 'core',
        'weight' => 0.25,
    ],
];
