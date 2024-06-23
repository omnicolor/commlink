<?php

declare(strict_types=1);

use App\Models\Cyberpunkred\CostCategory;

/**
 * Armor for Cyberpunk Red.
 *
 * Cost category can either be a value from the CostCategory enum (preferred) or
 * as a string.
 */
return [
    /*
    '' => [
        'cost' => ,
        'cost-category' => CostCategory::,
        'description' => '',
        'page' => 97,
        'penalty' => ,
        'ruleset' => 'core',
        'stopping-power' => ,
        'type' => '',
    ],
    */
    'leathers' => [
        'cost-category' => CostCategory::Everyday,
        'description' => 'Favored by Nomads and other ‘punks who ride bikes.',
        'page' => 97,
        'penalty' => 0,
        'ruleset' => 'core',
        'stopping-power' => 4,
        'type' => 'Leathers',
    ],
    'kevlar' => [
        'cost-category' => 'Costly',
        'description' => 'Can be made into clothes, vests, jackets, business suits, and even bikinis.',
        'page' => 97,
        'penalty' => 0,
        'ruleset' => 'core',
        'stopping-power' => 7,
        'type' => 'Kevlar',
    ],
    'light-armorjack' => [
        'cost-category' => CostCategory::Premium,
        'description' => 'A combination of Kevlar® and plastic meshes inserted into the weave of the fabric.',
        'page' => 97,
        'penalty' => 0,
        'ruleset' => 'core',
        'stopping-power' => 11,
        'type' => 'Light armorjack',
    ],
    'bodyweight-suit' => [
        'cost-category' => CostCategory::VeryExpensive,
        'description' => 'Skinsuit with impact absorbing, sintered armorgel layered in key body areas. Also has a place to store your Cyberdeck and supports your Interface Plugs. For more information see page 350.',
        'page' => 97,
        'penalty' => 0,
        'ruleset' => 'core',
        'stopping-power' => 11,
        'type' => 'Bodyweight suit',
    ],
    'medium-armorjack' => [
        'cost-category' => CostCategory::Premium,
        'description' => 'Heavier Armorjack, with solid plastic plating, reinforced with thicker Kevlar® mesh.',
        'page' => 97,
        'penalty' => -2,
        'ruleset' => 'core',
        'stopping-power' => 12,
        'type' => 'Medium armorjack',
    ],
    'heavy-armorjack' => [
        'cost-category' => CostCategory::Expensive,
        'description' => 'The thickest Armorjack, combining denser Kevlar® and a layered mix of plastic and mesh weaves.',
        'page' => 97,
        'penalty' => -2,
        'ruleset' => 'core',
        'stopping-power' => 13,
        'type' => 'Heavy armorjack',
    ],
    'flak' => [
        'cost-category' => CostCategory::Expensive,
        'description' => 'This is the 21st century version of the time honored flak vest and pants.',
        'page' => 97,
        'penalty' => -4,
        'ruleset' => 'core',
        'stopping-power' => 15,
        'type' => 'Flak',
    ],
    'metalgear' => [
        'cost-category' => CostCategory::Luxury,
        'description' => 'Metalgear® will stop almost anything, but you\'re going to be easier to hit than a one-legged bantha in a potho race.',
        'page' => 97,
        'penalty' => -4,
        'ruleset' => 'core',
        'stopping-power' => 18,
        'type' => 'Metalgear',
    ],
    'bulletproof-shield' => [
        'cost-category' => CostCategory::Premium,
        'description' => 'A transparent polycarbonate shield that can protect you in a firefight. See page 183.',
        'page' => 97,
        'penalty' => 0,
        'ruleset' => 'core',
        'stopping-power' => 10,
        'type' => 'Bulletproof shield',
    ],
];
