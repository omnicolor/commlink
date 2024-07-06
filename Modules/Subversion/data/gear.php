<?php

declare(strict_types=1);

/**
 * List of Subversion gear.
 */
return [
    /*
    '' => [
        'category' => '',
        'description' => '',
        'fortune' => ,
        'id' => '',
        'name' => '',
        'page' => ,
        'ruleset' => 'core',
    ],
     */
    'auto-intruder' => [
        'category' => 'Espionage and security',
        'description' => 'This device, commonly appearing as a large marker or pen, is a combination toolkit, lockpicks, and automatic breaching device specialized in locks and alarms. Gain Reliable 3 on all Physicality or Cybertech rolls to bypass locks or other similar security if the character can physically touch them with the autointruder. Furthermore, the auto-intruder may be used as a specialized cyberkit, allowing its user to make a tech roll to breach security systems it’s touching (treat this as granting the character a breach damage of 5).',
        'fortune' => 3,
        'id' => 'auto-intruder',
        'name' => 'Auto-intruder',
        'page' => 104,
        'ruleset' => 'core',
    ],
    'paylo' => [
        'category' => 'Electronics and software',
        'description' => 'Low end halos, colloquially known as “paylos” for their dirt cheap prices, can be bought at every store or C-link. Interacting with them relies on low-resolution touch-screens, speakers, and microphones. Sold at a loss, they make it up by steering the user towards buying certain products and selling their information. Notoriously cheap and insecure, they’re nonetheless the mainstay of those who can’t afford better.',
        'firewall' => 0,
        'fortune' => 1,
        'id' => 'paylo',
        'name' => 'Paylo',
        'page' => 99,
        'ruleset' => 'core',
        'security' => 8,
    ],
];
