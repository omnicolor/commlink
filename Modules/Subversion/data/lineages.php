<?php

declare(strict_types=1);

return [
    /**
    '' => [
        'description' => '',
        'id' => '',
        'name' => '',
        'options' => [
            [
                'description' => '',
                'id' => '',
                'name' => '',
            ],
        ],
        'page' => ,
        'ruleset' => 'core',
    ],
     */
    'dwarven' => [
        'description' => 'Dwarves tend to be shorter and sturdier than most other lineages. While Dwarves are the most common lineage in Acape Anya, they are a common sight throughout the world.',
        'id' => 'dwarven',
        'name' => 'Dwarven',
        'options' => [
            [
                'description' => 'Gain +4 to defenses when resisting Toxins or Metabolic Damage.',
                'id' => 'toxin-resistant',
                'name' => 'Toxin resistant',
            ],
            [
                'description' => 'Increase Brawn to 2.',
                'id' => 'lessons-from-the-ground',
                'name' => 'Lessons from the ground',
            ],
            [
                'description' => 'You and/or your family are smaller and less stocky than most dwarves. Gain +2 on physicality rolls related to hiding or navigating tight spaces.',
                'id' => 'small',
                'name' => 'Small',
            ],
            [
                'description' => 'You grow moss or grass in place of hair. Your Unarmed attacks do 2d6 damage (+Brawn) and have AP 1.',
                'id' => 'monstrous-heritage',
                'name' => 'Monstrous heritage',
            ],
        ],
        'page' => 82,
        'ruleset' => 'core',
    ],
];
