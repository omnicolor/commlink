<?php

declare(strict_types=1);

use Modules\Battletech\Enums\ExperienceItemType;

return [
    'mechwarrior' => [
        'attributes' => [
            'strength' => 4,
            'body' => 5,
            'dexterity' => 5,
            'reflexes' => 6,
            'intelligence' => 4,
            'willpower' => 4,
            'charisma' => 4,
            'edge' => 3,
        ],
        'description' => 'You are one of an elite branch of the military, respected and revered. MechWarriors are feared by their enemies and are the favored protectors of their realms. So you believed as a child, but like so many stories told to the young, these were only half-truths. Because you came from humble beginnings, you find yourself increasingly ostracized by the ’Mech-owning nobles, who consider you and those of similar background a threat to their power and wealth. Intrigue is common, and only those skilled in protocol, business or both can get ahead—unless you are exceptionally gifted, or lucky. You have yet to be given the chance to demonstrate either, as military service so far has included more boredom than action. A three- month campaign hunting pirates barely qualified as a proper diversion, and their eventual demise was little more than an execution. You yearn deeply for proper action, that you might shed the yoke of politics and focus on what you truly love in life: to pilot a BattleMech and destroy your enemies.',
        'experience_log' => [
            ['amount' => 5000, 'type' => ExperienceItemType::Starting, 'name' => 'Starting XP'],
            ['amount' => -400, 'type' => ExperienceItemType::Attribute, 'name' => 'strength'],
            ['amount' => -500, 'type' => ExperienceItemType::Attribute, 'name' => 'body'],
            ['amount' => -500, 'type' => ExperienceItemType::Attribute, 'name' => 'dexterity'],
            ['amount' => -600, 'type' => ExperienceItemType::Attribute, 'name' => 'reflexes'],
            ['amount' => -400, 'type' => ExperienceItemType::Attribute, 'name' => 'intelligence'],
            ['amount' => -400, 'type' => ExperienceItemType::Attribute, 'name' => 'willpower'],
            ['amount' => -400, 'type' => ExperienceItemType::Attribute, 'name' => 'charisma'],
            ['amount' => -300, 'type' => ExperienceItemType::Attribute, 'name' => 'edge'],
            ['amount' => -200, 'type' => ExperienceItemType::Trait, 'name' => 'dark-secret-2'],
            ['amount' => 100, 'type' => ExperienceItemType::Trait, 'name' => 'equipped-1'],
            ['amount' => -300, 'type' => ExperienceItemType::Trait, 'name' => 'in-for-life-3'],
            ['amount' => 400, 'type' => ExperienceItemType::Trait, 'name' => 'vehicle-4'],
        ],
        'money' => 552,
        'skills' => [
            ['id' => 'art-painting', 'level' => 0],
            ['id' => 'career-soldier', 'level' => 3],
            ['id' => 'computers', 'level' => 1],
            ['id' => 'gunnery-mech', 'level' => 3],
            ['id' => 'interest-battlemechs', 'level' => 1],
            ['id' => 'language-english', 'level' => 1],
            ['id' => 'language-french', 'level' => 0],
            ['id' => 'leadership', 'level' => 1],
            ['id' => 'martial-arts', 'level' => 3],
            ['id' => 'medtech', 'level' => 2],
            ['id' => 'melee-weapons', 'level' => 1],
            ['id' => 'navigation-ground', 'level' => 2],
            ['id' => 'perception', 'level' => 2],
            ['id' => 'piloting-mech', 'level' => 3],
            ['id' => 'protocol-fedsuns', 'level' => 3],
            ['id' => 'sensor-operations', 'level' => 2],
            ['id' => 'small-arms', 'level' => 3],
            ['id' => 'streetwise-fedsuns', 'level' => 2],
            ['id' => 'tactics-land', 'level' => 2],
            ['id' => 'technician-weapons', 'level' => 1],
        ],
        'traits' => [
            'dark-secret-2',
            'equipped-1',
            'in-for-life-3',
            'vehicle-4',
        ],
    ],
];
