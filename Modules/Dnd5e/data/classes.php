<?php

declare(strict_types=1);

use Modules\Dnd5e\Enums\Ability;

return [
    [
        'id' => 'barbarian',
        'description' => 'A fierce warrior of primitive background who can enter a battle rage',
        'hit_die' => 12,
        'name' => 'Barbarian',
        'page' => 45,
        'primary_ability' => Ability::Strength,
        'ruleset' => 'core',
        'saving_throw_proficiencies' => json_encode([Ability::Strength, Ability::Constitution]),
    ],
];
