<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'asks' => [
            [
                'cost' => ,
                'description' => '',
            ],
        ],
        'description' => '',
        'faction-only' => false,
        'has-additional' => false,
        'id' => '',
        'name' => '',
        'page' => ,
        'ruleset' => 'core',
    ],
     */
    'care' => [
        'asks' => [
            [
                'cost' => 0,
                'description' => 'Basic medical advice, provide a referral.',
            ],
            [
                'cost' => 5,
                'description' => 'Provide immediate medical attention.',
            ],
            [
                'cost' => 10,
                'description' => 'Provide long term care to someone else for you.',
            ],
            [
                'cost' => 15,
                'description' => 'Go with you someplace dangerous in case you get hurt.',
            ],
        ],
        'description' => 'This Relation provides medical or mental care for you. As long as you have a positive Regard with this character or Faction, they will take the provide care action on your behalf in Downtime whenever you take the recover action. For every five points of Regard you have with them, gain one Grit whenever they help you.',
        'faction-only' => false,
        'has-additional' => false,
        'id' => 'care',
        'name' => 'Care',
        'page' => 122,
        'ruleset' => 'core',
    ],
    'clout' => [
        'asks' => [
            [
                'cost' => 0,
                'description' => 'Provide advice related to their domain.',
            ],
            [
                'cost' => 5,
                'description' => 'Cut through red tape (automatic Success on an Influence test to get something done).',
            ],
            [
                'cost' => 10,
                'description' => 'Exempt you from the rules of their domain (reduce 5 Grit or progress due to consequences they’re involved in).',
            ],
            [
                'cost' => 15,
                'description' => 'Use the power of their domain directly for you.',
            ],
        ],
        'description' => 'This Faction has official or unofficial responsibility over a domain of daily life, such as law enforcement, local crime, Community relations, or government. Reduce the Ask by 2 for any Asks related to the functioning of this Faction and their domain.',
        'faction-only' => true,
        'has-additional' => false,
        'id' => 'clout',
        'name' => 'Clout',
        'page' => 122,
        'ruleset' => 'core',
    ],
];
