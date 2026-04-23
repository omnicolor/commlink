<?php

declare(strict_types=1);

use Modules\Stillfleet\Enums\TechStrata;
use Modules\Stillfleet\Enums\VoidwareType;

return [
    /*
    [
        'description' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'price' => ,
        'ruleset' => 'core',
        'tech_cost' => ,
        'tech_strata' => TechStrata::,
        'type' => VoidwareType::,
    ],
    */
    [
        'description' => 'Grants a +2 bonus to initiative. Breaks if you ever roll a 1 on an initiative check, but can be repaired.',
        'id' => 'accelerator-belt',
        'name' => 'Accelerator belt',
        'page' => 161,
        'price' => 600,
        'ruleset' => 'core',
        'tech_cost' => 2,
        'tech_strata' => TechStrata::Force,
        'type' => VoidwareType::Ventureware,
    ],
    [
        'description' => 'This looks like the head of a giant black beetle. You put it on over your head, and for ten minutes, you can see anywhere in the universe that you can imagine by making a REA check and burning d20 GRT. The Archive sometimes leases these out to particularly trusted venturers.',
        'id' => 'aleph',
        'name' => 'Aleph',
        'page' => 157,
        'price' => 30000,
        'ruleset' => 'core',
        'tech_cost' => 3,
        'tech_strata' => TechStrata::Bug,
        'type' => VoidwareType::Comm,
    ],
    [
        'description' => 'You can try to drive on province-worlds, although roads tend to be shit. You cannot drive on Spindle.',
        'id' => 'automobile',
        'name' => 'Automobile',
        'page' => 160,
        'price' => 2500,
        'ruleset' => 'core',
        'tech_cost' => 3,
        'tech_strata' => TechStrata::Clank,
        'type' => VoidwareType::Vehicle,
    ],
    [
        'description' => 'These are very intelligent, very annoying rats with exposed brains that communicate with you telepathically and fuck constantly. They hate you but rely on you for food and water and haven’t figured out a way to kill you yet. All of their scores are 1 including HEA and GRT except REA, which is d10 as long as the four rats are within 500 M of one another. Each lost rat lowers their REA die by one type. As long as the rats are with you and you are willing to talk to them, you gain +1 to REA rolls relating to political/business strategy and CHA rolls related to haggling/advertising.',
        'id' => 'brainrat-erotic-jesters',
        'name' => 'Brainrat erotic jesters',
        'page' => 159,
        'price' => 450,
        'ruleset' => 'core',
        'tech_cost' => 4,
        'tech_strata' => TechStrata::Bio,
        'type' => VoidwareType::Pet,
    ],
    [
        'description' => 'Now ubiquitous across the Co. worlds, “goblyn moss” is a bitter-tasting but edible nonvascular plant that acts as an anti-inflammatory and stimulates the human body’s immune response. Each patch, if chewed for one round, heals d4 HEA. A human can safely heal 4d4 HEA in this way per day; additional chewed patches carry a 1 in 6 risk of serious liver disease (as radiation poisoning; see “Gears”). Graxanna has no effect on amphibians and reptiles and is (perhaps obviously) useless to plantoids and automata. A chewed patch of “gob” will heal a wetan, harajoon, or mongrel only 1 HEA.',
        'id' => 'graxanna',
        'name' => 'Graxanna',
        'page' => 158,
        'price' => 2,
        'ruleset' => 'core',
        'tech_cost' => 1,
        'tech_strata' => TechStrata::Bio,
        'type' => VoidwareType::Drug,
    ],
];
