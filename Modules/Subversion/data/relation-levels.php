<?php

declare(strict_types=1);

use Modules\Subversion\Models\RelationLevel;

return [
    /*
    '' => [
        'cost' => ,
        'description' => '',
        'level' => RelationLevel::MINOR,
        'name' => '',
        'page' => 132,
        'power' => ,
        'regard' => ,
        'ruleset' => 'core',
    ],
     */
    'big-shot' => [
        'cost' => 2,
        'description' => 'You know someone who can do a lot, and they take your calls—but not much more than that without convincing.',
        'level' => RelationLevel::MINOR,
        'name' => 'Big shot',
        'page' => 132,
        'power' => 6,
        'regard' => 1,
        'ruleset' => 'core',
    ],
    'friend' => [
        'cost' => 10,
        'description' => 'You have a strong, capable friend you can rely on.',
        'level' => RelationLevel::NORMAL,
        'name' => 'Friend',
        'page' => 132,
        'power' => 4,
        'regard' => 10,
        'ruleset' => 'core',
    ],
    'personal-connection' => [
        'cost' => 2,
        'description' => 'You know a typical member of the Community on a first name basis, which makes them easier to find and gives you a slight edge in asking for favors.',
        'level' => RelationLevel::MINOR,
        'name' => 'Personal connection',
        'page' => 132,
        'power' => 4,
        'regard' => 6,
        'ruleset' => 'core',
    ],
    'sponsor' => [
        'cost' => 10,
        'description' => 'You know someone more competent than you who’s willing to do you some favors from time to time.',
        'level' => RelationLevel::NORMAL,
        'name' => 'Sponsor',
        'page' => 132,
        'power' => 6,
        'regard' => 5,
        'ruleset' => 'core',
    ],
];
