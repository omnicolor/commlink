<?php

declare(strict_types=1);

return [
    /*
    [
        'description' => '',
        'effects' => '{}',
        'id' => '',
        'karma_cost' => ,
        'level' => ,
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
     */
     [
        'description' => 'You are equally adept at using either your right or left side. Whether shooting a gun, throwing a grenade, or kicking a ball, you can switch it up with the best of them.||Game effect: No penalty for off-hand weapon use (see p. 110).',
        'effects' => null,
        'id' => 'ambidextrous',
        'karma_cost' => 4,
        'level' => null,
        'name' => 'Ambidextrous',
        'page' => 70,
        'ruleset' => 'core',
    ],
    [
        'description' => 'Whether it’s being a powerhouse, taking a hit from a troll, holding your synthahol, ducking a fast right, holding that inside turn, selling coal in Newcastle, or making a cat look clumsy, you’re just naturally built to be better.',
        'effects' => '{"maximum-strength":1}',
        'id' => 'exceptional-attribute-strength',
        'karma_cost' => 12,
        'level' => null,
        'name' => 'Exceptional Attribute (Strength)',
        'page' => 71,
        'ruleset' => 'core',
    ],
    [
        'description' => 'You know how to compartmentalize your mind and keep hold of arcane and emergent manipulations without straining yourself.||Game Effect: You can sustain multiple spells or complex forms without penalty. For each level, you can sustain 1 additional spell or complex form without suffering the associated penalty. The spell cannot have a modified Drain Value of 7 or greater.',
        'effects' => null,
        'id' => 'focused-concentration-1',
        'karma_cost' => 12,
        'level' => 1,
        'name' => 'Focused concentration',
        'page' => 71,
        'ruleset' => 'core',
    ],
    'focused-concentration-2' => [
        'description' => 'You know how to compartmentalize your mind and keep hold of arcane and emergent manipulations without straining yourself.||Game Effect: You can sustain multiple spells or complex forms without penalty. For each level, you can sustain 1 additional spell or complex form without suffering the associated penalty. The spell cannot have a modified Drain Value of 7 or greater.',
        'effects' => null,
        'id' => 'focused-concentration-2',
        'karma_cost' => 12 * 2,
        'level' => 2,
        'name' => 'Focused concentration',
        'page' => 71,
        'ruleset' => 'core',
    ],
    'focused-concentration-3' => [
        'description' => 'You know how to compartmentalize your mind and keep hold of arcane and emergent manipulations without straining yourself.||Game Effect: You can sustain multiple spells or complex forms without penalty. For each level, you can sustain 1 additional spell or complex form without suffering the associated penalty. The spell cannot have a modified Drain Value of 7 or greater.',
        'effects' => null,
        'id' => 'focused-concentration-3',
        'karma_cost' => 12 * 3,
        'level' => 3,
        'name' => 'Focused concentration',
        'page' => 71,
        'ruleset' => 'core',
    ],
    [
        'description' => 'Some folks are just not meant to be naturally talented. A bum knee, poor genetics, or an illness as a kid has you lacking the maximum achievement level of your peers.',
        'effects' => '{"maximum-body":-1}',
        'id' => 'impaired-body-1',
        'karma_cost' => -8,
        'level' => 1,
        'name' => 'Impaired (Body)',
        'page' => 76,
        'ruleset' => 'core',
    ],
];
