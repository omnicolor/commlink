<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'cash' => 'd6',
        'cash-multiplier' => 100,
        'description' => '',
        'key-attribute' => '',
        'key-skills' => [
        ],
        'gear' => [
            [
                ['id' => ''],
                ['id' => ''],
            ],
        ],
        'name' => '',
        'page' => ,
        'ruleset' => 'core',
        'talents' => [
        ],
    ],
     */
    'colonial-marine' => [
        'cash' => 'd6',
        'cash-multiplier' => 100,
        'description' => 'Most of your friends will never see another world… but not you. As soon as you were old enough, you signed up for the USCMC. The pay is crap and the food is worse, but you’ve always got a bunk to sleep in and you get to shoot all sorts of weapons at all sorts of things. Life in the Corps is never dull—but the luster has begun to fade. You’ve seen things that you’ll never be able to forget, and plenty you wish you could.',
        'key-attribute' => 'strength',
        'key-skills' => [
            'close-combat',
            'ranged-combat',
            'stamina',
        ],
        'gear' => [
            [
                // Gear in a sub-array is a choice in character generation.
                ['id' => 'armat-m41ae2-heavy-pulse-rifle'],
                ['id' => 'm56a2-smart-gun'],
            ],
            [
                ['id' => 'm314-motion-tracker'],
                ['id' => 'g2-electroshock-grenade', 'quantity' => 2],
            ],
            [
                ['id' => 'irc-mk-35-pressure-suit'],
                ['id' => 'm3-personnel-armor'],
            ],
            [
                ['id' => 'signal-flare'],
                ['id' => 'deck-of-cards'],
            ],
        ],
        'name' => 'Colonial marine',
        'page' => 38,
        'ruleset' => 'core',
        'talents' => [
            'banter',
            'overkill',
            'past-the-limit',
        ],
    ],
    'colonial-marshal' => [
        'cash' => 'd6',
        'cash-multiplier' => 100,
        'description' => 'Colonial marshal description.',
        'key-attribute' => 'wits',
        'key-skills' => [
            'observation',
            'ranged-combat',
            'manipulation',
        ],
        'gear' => [
            [
                ['id' => '357-magnum-revolver'],
                ['id' => 'armat-model-37a2-12-gauge-pump-action'],
            ],
            [
                ['id' => 'binoculars'],
                ['id' => 'hi-beam-flashlight'],
            ],
            [
                ['id' => 'personal-medkit'],
                ['id' => 'stun-baton'],
            ],
            [
                ['id' => 'neversleep-pills', 'quantity' => 'D6'],
                ['id' => 'hand-radio'],
            ],
        ],
        'name' => 'Colonial marshal',
        'page' => 40,
        'ruleset' => 'core',
        'talents' => [
            'authority',
            'investigator',
            'subdue',
        ],
    ],
];
