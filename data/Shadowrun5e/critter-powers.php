<?php

declare(strict_types=1);

/**
 * List of Shadowrun 5E critter/spirit powers.
 */
return [
    /*
    '' => [
        'action' => '',
        'description' => '',
        'duration' => '',
        'name' => '',
        'page' => ,
        'range' => '',
        'ruleset' => '',
        'type' => '',
    ],
     */
    'accident' => [
        'action' => 'Complex',
        'description' => 'Critters with this power can cause seemingly normal accidents to occur. The exact nature of the accident is for the gamemaster to determine, based on what the target is doing and what’s going on around him. This power isn’t, in and of itself, dangerous, but circumstance and environment can come into play to make it so. Tripping on your own feet in front of rush-hour traffic could be hazardous to your health, for instance.||When a critter targets someone with this power, make an Opposed Test, using the critter’s Magic + Willpower against the target’s Reaction + Intuition. If the critter wins, treat it as if the target rolled a glitch on a test. If the critter scores 4 or more net hits, the accident is treated as a critical glitch—it’s not just an embarrassing fumble, it’s a potential catastrophe. A critter can use this power on a number of targets at once equal to its Magic rating.',
        'duration' => 'Instant',
        'name' => 'Accident',
        'page' => 394,
        'range' => 'LOS',
        'ruleset' => 'core',
        'type' => 'P',
    ],
    'fear' => [
        'action' => 'Complex',
        'description' => 'This power gives a critter the power to fill its victims with overwhelming terror. The victim flees in panic and doesn’t stop until he is safely away and out of the critter’s sight. The critter makes an Opposed Test using its Willpower + Magic against the target’s Willpower + Logic. The terror lasts for 1 Combat Turn per net hit scored by the critter. Even once the fear fades, the target must succeed in a Willpower + Logic (critter’s net hits) Test to gather the nerve to face the critter again.',
        'duration' => 'Special',
        'name' => 'Fear',
        'page' => 397,
        'range' => 'LOS',
        'ruleset' => 'core',
        'type' => 'M',
    ],
];
