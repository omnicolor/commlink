<?php

declare(strict_types=1);

/**
 * List of Shadowrun 5th edition metamagics.
 */
return [
    /*
    '' => [
        'adeptOnly' => false,
        'description' => '',
        'id' => '',
        'name' => '',
        'page' => 0,
        'ruleset' => 'core',
    ],
     */
    'adept-centering' => [
        'adeptOnly' => true,
        'description' => 'Adept Centering description.',
        'id' => 'adept-centering',
        'name' => 'Adept Centering',
        'page' => 325,
        'ruleset' => 'core',
    ],
    'astral-bluff' => [
        'adeptOnly' => false,
        'description' => 'Astral bluff description.',
        'id' => 'astral-bluff',
        'name' => 'Astral bluff',
        'page' => 150,
        'ruleset' => 'street-grimoire',
    ],
    'centering' => [
        'adeptOnly' => false,
        'description' => 'Centering description.',
        'id' => 'centering',
        'name' => 'Centering',
        'page' => 325,
        'ruleset' => 'core',
    ],
    'channeling' => [
        'adeptOnly' => false,
        'description' => 'When a magician summons a spirit, she may choose to allow the spirit to possess her instead of allowing the spirit to exist in the astral plane to either manifest (p. 314, SR5) or materialize (p. 398, SR5). This must be decided at the time of summoning. Treat channeling the same as if the spirit has the Possession power (p. 197), with a few exceptions:||• The magician can use her own skills and has motor control over her body.||• She may relinquish control of her body to the spirit, but at the cost of a service.||• The magician can use the powers of the spirit, but at the cost of a service.||• Because two minds inhabit this same body, Mana spells or powers are resisted by the lowest Mental attribute of the two. Damage from Mana spells or powers is applied to both (no free rides).||• The spirit cannot leave the magician’s body until either the services are up, the magician dismisses it, or time expires as per the rules of summoned spirits.',
        'id' => 'channeling',
        'name' => 'Channeling',
        'page' => 148,
        'ruleset' => 'street-grimoire',
    ],
];
