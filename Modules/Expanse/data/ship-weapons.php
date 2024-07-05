<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'damage' => '',
        'description' => '',
        'name' => '',
        'page' => ,
        'range' => '',
        'ruleset' => '',
    ],
     */
    'grapplers' => [
        'description' => 'Grapplers do not inflict damage and can only be used at Close Range (usually a kilometer or less). A grappler hit that is not evaded locks on to the target ship and, when it moves, the grappling ship moves with it. The grappled ship is treated as having a Size category equal to one greater than the Size of the larger of the two ships, if its drive is moving their total mass. Once a ship is grappled, neither ship can evade the other, which is why grappling is usually only performed on ships unable to fire weapons.',
        'name' => 'Grapplers',
        'page' => 132,
        'range' => 'close',
        'ruleset' => 'core',
    ],
    'point-defense-cannon' => [
        'damage' => '2d6',
        'description' => 'Primarily defensive weapons, PDCs can be used for attacks at Close Range (5 kilometers or less). A PDC hit does 2d6 damage. If the ship’s PDCs are used to attack that round, the TN for any Point Defense test increases by +2 (see Point Defense under Defensive Actions, following).',
        'name' => 'Point defense cannon',
        'page' => 132,
        'range' => 'close',
        'ruleset' => 'core',
    ],
    'rail-gun' => [
        'damage' => '3d6',
        'description' => 'Rail guns are useful out to Medium Range, after which their shots are easy to evade. A rail gun attack can only be made against a target in the gun’s firing arc, either in front of or behind the ship (for spinal-mounted rail guns) or along one side of the ship (for turret-mounted rail guns). A rail gun hit does 3d6 damage.',
        'name' => 'Rail gun',
        'page' => 132,
        'range' => 'medium',
        'ruleset' => 'core',
    ],
    'torpedo' => [
        'damage' => '4d6',
        'description' => 'Torpedoes are Long Range weapons since they can accelerate faster than any ship, becoming virtually impossible to evade, although they can still be shot down with PDCs. A torpedo hit does 4d6 damage.',
        'name' => 'Torpedo',
        'page' => 133,
        'range' => 'long',
        'ruleset' => 'core',
    ],
    'torpedo-plasma' => [
        'damage' => '3d6',
        'description' => 'Torpedoes are Long Range weapons since they can accelerate faster than any ship, becoming virtually impossible to evade, although they can still be shot down with PDCs. Plasma torpedoes (see Ship Qualities) do 3d6 damage, but reduce the target ship’s Hull score by one category against their damage.',
        'name' => 'Torpedo - plasma',
        'page' => 133,
        'range' => 'long',
        'ruleset' => 'core',
    ],
];
