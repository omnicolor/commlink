<?php

declare(strict_types=1);

use Modules\Shadowrun6e\Enums\SpellCategory;
use Modules\Shadowrun6e\Enums\SpellDuration;
use Modules\Shadowrun6e\Enums\SpellRange;
use Modules\Shadowrun6e\Enums\SpellType;

return [
    /*
    [
        'category' => SpellCategory::,
        'damage' => '["P","Special"]',
        'damage' => null,
        'description' => '',
        'drain_value' => ,
        'duration' => SpellDuration::,
        'duration' => 5,
        'id' => '',
        'indirect' => false,
        'name' => '',
        'page' => ,
        'range' => SpellRange::,
        'ruleset' => '',
        'type' => SpellType::,
    ],
     */
    [
        'category' => SpellCategory::Combat,
        'damage' => '["Special","P"]',
        'description' => 'These spells shoot acid at targets, doing immediate damage while also doing Chemical damage (p. 109) and giving hit targets the Corroded status (p. 52) with a rating equal to net hits on the Spellcasting test. Acid Stream is a single-target spell, Toxic Wave is area effect.',
        'drain_value' => 5,
        'duration' => SpellDuration::Instantaneous,
        'id' => 'acid-stream',
        'indirect' => true,
        'name' => 'Acid Stream',
        'page' => 132,
        'range' => SpellRange::LineOfSight,
        'ruleset' => 'core',
        'type' => SpellType::Physical,
    ],
    [
        'category' => SpellCategory::Detection,
        'damage' => null,
        'description' => 'Sure, you know what a commlink looks like, but that doesn’t mean you can identify the function of the weird black box with a single input jack. And Ghost help you with a machine from the ’50s or something. Analyze Device provides information about the unknown device, based on the number of net hits (the device’s Object Resistance is used in the Opposed Test). The first time a character tries to use a device while sustaining this spell on it, they receive Edge equal to their net hits on this test (though the customary limit of gaining no more than 2 Edge in a combat round applies).',
        'drain_value' => 2,
        'duration' => 5,
        'id' => 'analyze-device',
        'indirect' => false,
        'name' => 'Analyze Device',
        'page' => 132,
        'range' => SpellRange::Touch,
        'ruleset' => 'core',
        'type' => SpellType::Physical,
    ],
    [
        'category' => SpellCategory::Combat,
        'damage' => '["S"]',
        'description' => 'A tricky little spell—the magic doesn’t hit the target, but it shapes the air to make the blow. The power of wind to shape rock formations is demonstrated solidly on the head of the target. Clout targets individuals, Blast is area effect.',
        'drain_value' => 3,
        'duration' => SpellDuration::Instantaneous,
        'id' => 'clout',
        'indirect' => true,
        'name' => 'Clout',
        'page' => 133,
        'range' => SpellRange::LineOfSight,
        'ruleset' => 'core',
        'type' => SpellType::Physical,
    ],
    [
        'category' => SpellCategory::Combat,
        'damage' => '["P"]',
        'description' => 'Essential spellcasting, shaping mana to crack skulls. Who can argue with this purity? Manabolt targets individuals, while Manaball is area effect.',
        'drain_value' => 5,
        'duration' => SpellDuration::Instantaneous,
        'id' => 'manaball',
        'indirect' => false,
        'name' => 'Manaball',
        'page' => 133,
        'range' => SpellRange::LineOfSightArea,
        'ruleset' => 'core',
        'type' => SpellType::Mana,
    ],
];
