<?php

declare(strict_types=1);

/**
 * List of sprite powers.
 */
return [
    /*
    '' => [
        'description' => '',
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
     */
    'cookie' => [
        'description' => 'A sprite uses its cookie power to “tag” a target persona with a cookie file that can be used to track the icon’s Matrix activities. The sprite must successfully beat the target in a Hacking + Resonance [Sleaze] v. Intuition + Firewall test. If the sprite succeeds, the persona starts carrying the cookie file, none the wiser.||The cookie file runs silent and is protected with a rating equal to the sprite’s Level. The file will log every everything the icon does, for example each host the persona enters, the details of any communications the persona engages in (with whom and when, but not the actual contents), any programs the icon uses, etc. Use the net hits to benchmark the depth of the data the cookie accumulates (1 hit providing a bare outline, 4 or more a detailed report).||At the end of a time determined by the sprite (or its owner) when placed, the cookie file transfers itself and its accumulated data to the sprite. Once the sprite has it, it may turn it over to the technomancer. If the sprite isn’t in the Matrix when the file transfers itself, the file is deleted.||Cookie files may be detected with a successful Matrix Perception Test performed on the carrying persona. Once identified, it may be removed by removing the file’s protection and then deleting it.',
        'name' => 'Cookie',
        'page' => 256,
        'ruleset' => 'core',
    ],
    'hash' => [
        'description' => 'The Hash power allows the sprite to temporarily protect a file with a unique Resonance algorithm in such a way that only the sprite can unprotect it. If the sprite stops carrying the hashed file it reverts to normal. If the sprite is destroyed while carrying the file, the hashed file is permanently corrupted and becomes worthless. The maximum time the sprite can use this power is Level x 10 Combat Turns.',
        'name' => 'Hash',
        'page' => 257,
        'ruleset' => 'core',
    ],
];
