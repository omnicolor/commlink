<?php

declare(strict_types=1);

use Modules\Dnd5e\Enums\CreatureSize;

return [
    [
        'ability_increases' => '{"constitution":2,"wisdom":1}',
        'id' => 'hill-dwarf',
        'languages' => '',
        'name' => 'Hill Dwarf',
        'page' => 18,
        'parent_race' => 'Dwarf',
        'ruleset' => 'core',
        'size' => CreatureSize::Medium->value,
        'tool_proficiencies' => '[["smiths-tools"],["brewers-supplies"],["masons-tools"]]',
        'weapon_proficiencies' => '["battleaxe","handaxe","throwing-hammer","warhammer"]',
    ],
];
