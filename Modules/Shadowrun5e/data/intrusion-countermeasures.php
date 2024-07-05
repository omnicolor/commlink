<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'defense' => '',
        'description' => '',
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
     */
    'acid' => [
        'defense' => 'Willpower + Firewall',
        'description' => 'Acid IC targets and overwrites your protective software. When it gets 1 or more net hits on its attack, it reduces your Firewall by 1. If your Firewall has been reduced to 0 already, it causes 1 DV Matrix damage per net hit on the attack. The reduction is cumulative and lasts until you reboot the targeted device.',
        'name' => 'Acid',
        'page' => 248,
        'ruleset' => 'core',
    ],
    'blue-goo' => [
        'defense' => 'Logic + Firewall',
        'description' => 'If an avatar successfully deals Matrix damage to this IC, it deactivates by exploding. Make a Host Rating x 2 [Attack] v. Logic + Firewall Opposed Test. If this attack hits, the avatar is link-locked by the host that launched the Blue Goo. The avatar must resist (Attack) DV, and it is marked by the host. The avatar must successfully Jack Out of the host to end the link-lock.',
        'name' => 'Blue goo',
        'page' => 68,
        'ruleset' => 'kill-code',
    ],
];
