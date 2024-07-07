<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'description' => '',
        'downtime' => [
            'description' => '',
            'effects' => [
            ],
            'name' => '',
        ],
        'id' => '',
        'name' => '',
        'page' => ,
        'responses' => [
            '' => [
                'description' => '',
                'effects' => [
                ],
                'id' => '',
                'name' => '',
            ],
        ],
        'ruleset' => 'core',
        'triggers' => '',
    ],
    */
    'indulgence' => [
        'description' => 'You have a hedonistic pleasure that you turn to when you want to have fun, when you need a pick me up, or sometimes, just because it’s there. On a good day you just tell yourself you’re enjoying the finer things in life, on a bad day it’s just easier than dealing with the important things in life. Sample Indulgences: Alcohol, Drugs, Gambling, Clubbing, Sex, Videogames, etc.',
        'downtime' => [
            'description' => 'You spent a large chunk of the time partaking of your indulgence to excess. Spend 1 fortune, recover 3 Grit, and the GM gains 5 Grit.',
            'effects' => [
                'fortune' => -1,
                'grit' => 3,
                'grit-gm' => 5,
            ],
            'name' => 'Indulge',
        ],
        'id' => 'indulgence',
        'name' => 'Indulgence',
        'page' => 27,
        'responses' => [
            'intoxication' => [
                'description' => 'You partake of a drug or other activity that numbs your abilities or connection to the world. If you are addicted to a substance with specific rules (see "Drugs", core ruleset pg 100), you take a dose of the drug, following those rules. For other substances or activities, you partake to the point of impairment. Gain a temporary Consequence until the next time you get a full nights sleep.',
                'effects' => [
                    'consequence' => 1,
                ],
                'id' => 'intoxication',
                'name' => 'Intoxication',
            ],
            'distraction' => [
                'description' => 'You get distracted by your indulgence, missing your other responsibilities. Gain two instanced of Dulled until the next scene unless you are pursuing (or have indulged in) the subject of your indulgence.',
                'effects' => [
                    'dulled' => 2,
                ],
                'id' => 'distraction',
                'name' => 'Distraction',
            ],
            'largesse' => [
                'description' => 'You make a large, irresponsible purchase or expenditures. Spend 1 Fortune.',
                'effects' => [
                    'fortune' => -1,
                ],
                'id' => 'largesse',
                'name' => 'Largesse',
            ],
        ],
        'ruleset' => 'core',
        'triggers' => 'Object of desire being easily available, location where you’ve indulged in the past, being with someone you’ve indulged with in the past, being under stress, feeling down.',
    ],
];
