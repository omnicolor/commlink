<?php

declare(strict_types=1);

/**
 * List of Shadowrun 5th edition rulebooks.
 *
 * Whether a rulebook is default is completely up to the server admin, but
 * below is a somewhat opinionated listing of which books a table is likely to
 * have (and allow). For example, Forbidden Arcana frequently forbidden from
 * play for its game-breaking potential.
 *
 * Whether a book is required on the other hand, is less subjective. Only the
 * core rulebook for Shadowrun 5E is required. Other systems might have more
 * required books.
 */
return [
    /*
    '' => [
        'default' => false, // Whether the ruleset should be checked by default.
        'description' => '',
        'id' => '',
        'name' => '',
        'required' => true, // Whether the ruleset is required to play the game.
    ],
     */
    'aetherology' => [
        'default' => true,
        'description' => 'Description of Aetherology.',
        'id' => 'aetherology',
        'name' => 'Aetherology',
        'required' => false,
    ],
    'assassins-primer' => [
        'default' => true,
        'description' => 'Description of Assassins Primer.',
        'id' => 'assassins-primer',
        'name' => 'Assassins Primer',
        'required' => false,
    ],
    'better-than-bad' => [
        'default' => true,
        'description' => 'Description of Better Than Bad.',
        'id' => 'better-than-bad',
        'name' => 'Better Than Bad',
        'required' => false,
    ],
    'bloody-business' => [
        'default' => true,
        'description' => 'Description of Bloody Business.',
        'id' => 'bloody-business',
        'name' => 'Bloody Business',
        'required' => false,
    ],
    'battle-of-manhattan' => [
        'default' => true,
        'description' => 'Description of Battle of Manhattan.',
        'id' => 'battle-of-manhattan',
        'name' => 'Battle of Manhattan',
        'required' => false,
    ],
    'bullets-and-bandages' => [
        'default' => true,
        'description' => 'Description of Bullets and Bandages.',
        'id' => 'bullets-and-bandages',
        'name' => 'Bullets and Bandages',
        'required' => false,
    ],
    'book-of-the-lost' => [
        'default' => true,
        'description' => 'Description of Book of the Lost.',
        'id' => 'book-of-the-lost',
        'name' => 'Book of the Lost',
        'required' => false,
    ],
    'chrome-flesh' => [
        'default' => true,
        'description' => 'Description of Chrome Flesh.',
        'id' => 'chrome-flesh',
        'name' => 'Chrome Flesh',
        'required' => false,
    ],
    'complete-trog' => [
        'default' => true,
        'description' => 'Description of Complete Trog.',
        'id' => 'complete-trog',
        'name' => 'Complete Trog',
        'required' => false,
    ],
    'core' => [
        'default' => true,
        'description' => 'EVERYTHING HAS A PRICE||There are cracks in the world. They’re slender, dark, and often cold, but they are the only things that keep you hidden. Keep you alive. They are the shadows of the world, and they are where you live.||You are a shadowrunner, thriving in the margins, doing the jobs no one else can. You have no office, no permanent home, no background to check. You are whatever you make yourself. Will you seek justice? Sow seeds of chaos? Sell out to the highest bidder? It’s up to you, but this much is certain: If you do nothing, the streets will eat you alive.||You can survive, even flourish, as long as you do what it takes. Sacrifice part of your soul for bleeding-edge gear. Push the limits of your will learning new and dangerous magic. Wire yourself into the Matrix, making your mind one with screaming streams of data. It’ll cost you something—everything does—but you can make it worth the price.||Shadowrun, Fifth Edition is the newest version of one of the most popular and successful role-playing worlds of all time, a fusion of man, magic, and machine in a dystopian near-future. With rules for character creation, magic, combat, Matrix hacking, rigging, and more, you have everything you need to face the challenges of the Sixth World.',
        'id' => 'core',
        'name' => 'Core 5th Edition',
        'required' => true,
    ],
    'court-of-shadows' => [
        'default' => true,
        'description' => 'Descripton of Court of Shadows.',
        'id' => 'court-of-shadows',
        'name' => 'Court of Shadows',
        'required' => false,
    ],
    'coyotes' => [
        'default' => true,
        'description' => 'Description of Coyotes.',
        'id' => 'coyotes',
        'name' => 'Coyotes',
        'required' => false,
    ],
    'cutting-aces' => [
        'default' => true,
        'description' => 'Description of Cutting Aces.',
        'id' => 'cutting-aces',
        'name' => 'Cutting Aces',
        'required' => false,
    ],
    'dark-terrors' => [
        'default' => true,
        'description' => 'Description of Dark Terrors.',
        'id' => 'dark-terrors',
        'name' => 'Dark Terrors',
        'required' => false,
    ],
    'data-trails' => [
        'default' => true,
        'description' => 'Description of Data Trails.',
        'id' => 'data-trails',
        'name' => 'Data Trails',
        'required' => false,
    ],
    'false-flag' => [
        'default' => false,
        'description' => 'Description of False Flag.',
        'id' => 'false-flag',
        'name' => 'False Flag',
        'required' => false,
    ],
    'firing-line' => [
        'default' => false,
        'description' => 'Description of Firing Line.',
        'id' => 'firing-line',
        'name' => 'Firing Line',
        'required' => false,
    ],
    'forbidden-arcana' => [
        'default' => false,
        'description' => 'Magic is wild. Magic is undisciplined. You can try to impose order and understanding on it, but that’s just surface. Underneath is chaos, an erratic heart beating to a staggering rhythm. You don’t control it, any more than a surfer controls twenty-meter-tall wave; you don’t direct the wave, you ride it, capture a piece of its power, and hope to survive. If you do it right, though, you catch a portion of unimaginable power—power those who control the Sixth World don’t want you to have. All the more reason to push past their boundaries and grab it.||Forbidden Arcana offers dozens of different ways for Awakened characters in Shadowrun to harness that power and make themselves a mana-slinger like no other. From new ways to distinguish spellcasters of different traditions to more chaotic methods for summoning spirits to options for Awakened characters who have mastered their craft, Forbidden Arcana shows players how to break out of conventional molds, use mana in new ways, and become true street legends riding the growing wave of Sixth World mana.',
        'id' => 'forbidden-arcana',
        'name' => 'Forbidden Arcana',
        'required' => false,
    ],
    'gun-heaven-3' => [
        'default' => true,
        'description' => 'Description of Gun H(e)aven 3.',
        'id' => 'gun-heaven-3',
        'name' => 'Gun H(e)aven 3',
        'required' => false,
    ],
    'hard-targets' => [
        'default' => true,
        'description' => 'Description of Hard Targets.',
        'id' => 'hard-targets',
        'name' => 'Hard Targets',
        'required' => false,
    ],
    'howling-shadows' => [
        'default' => true,
        'description' => 'Description of Howling Shadows.',
        'id' => 'howling-shadows',
        'name' => 'Howling Shadows',
        'required' => false,
    ],
    'kill-code' => [
        'default' => true,
        'description' => 'Description of Kill Code.',
        'id' => 'kill-code',
        'name' => 'Kill Code',
        'required' => false,
    ],
    'krime-katalog' => [
        'default' => false,
        'description' => 'Description of Krime Katalog.',
        'id' => 'krime-katalog',
        'name' => 'Krime Katalog',
        'required' => false,
    ],
    'lockdown' => [
        'default' => true,
        'description' => 'Description of Lockdown.',
        'id' => 'lockdown',
        'name' => 'Lockdown',
        'required' => false,
    ],
    'london-falling' => [
        'default' => true,
        'description' => 'Description of London Falling.',
        'id' => 'london-falling',
        'name' => 'London Falling',
        'required' => false,
    ],
    'market-panic' => [
        'default' => true,
        'description' => 'Description of Market Panic.',
        'id' => 'market-panic',
        'name' => 'Market Panic',
        'required' => false,
    ],
    'nothing-personal' => [
        'default' => false,
        'description' => 'Description of Nothing Personal.',
        'id' => 'nothing-personal',
        'name' => 'Nothing Personal',
        'required' => true,
    ],
    'rigger-5' => [
        'default' => true,
        'description' => 'Description of Rigger 5.0.',
        'id' => 'rigger-5',
        'name' => 'Rigger 5.0',
        'required' => false,
    ],
    'run-and-gun' => [
        'default' => true,
        'description' => 'Description of Run and Gun.',
        'id' => 'run-and-gun',
        'name' => 'Run and Gun',
        'required' => false,
    ],
    'run-faster' => [
        'default' => true,
        'description' => 'Description of Run Faster.',
        'id' => 'run-faster',
        'name' => 'Run Faster',
        'required' => false,
    ],
    'serrated-edge' => [
        'default' => true,
        'description' => 'Description of Serrated Edge.',
        'id' => 'serrated-edge',
        'name' => 'Serrated Edge',
        'required' => false,
    ],
    'shadow-spells' => [
        'default' => true,
        'description' => 'Description of Shadow Spells.',
        'id' => 'shadow-spells',
        'name' => 'Shadow Spells',
        'required' => false,
    ],
    'shadows-in-focus-butte' => [
        'default' => false,
        'description' => 'Description of Shadows in Focus: Butte.',
        'id' => 'shadows-in-focus-butte',
        'name' => 'Shadows in Focus: Butte',
        'required' => false,
    ],
    'shadows-in-focus-casablance-rabat' => [
        'default' => false,
        'description' => 'Description of Shadows in Focus: Butte.',
        'id' => 'shadows-in-focus-casablance-rabat',
        'name' => 'Shadows in Focus: Casablance-Rabat',
        'required' => false,
    ],
    'shadows-in-focus-cheyenne' => [
        'default' => false,
        'description' => 'Description of Shadows in Focus: Cheyenne.',
        'id' => 'shadows-in-focus-cheyenne',
        'name' => 'Shadows in Focus: Cheyenne',
        'required' => false,
    ],
    'shadows-in-focus-san-francisco-metroplex' => [
        'default' => false,
        'description' => 'Description of Shadows in Focus: San Francisco.',
        'id' => 'shadows-in-focus-san-francisco-metroplex',
        'name' => 'Shadows in Focus: San Francisco Metroplex',
        'required' => false,
    ],
    'shadows-in-focus-sioux-nation' => [
        'default' => false,
        'description' => 'Description of Shadows in Focus: Sioux Nation.',
        'id' => 'shadows-in-focus-sioux-nation',
        'name' => 'Shadows in Focus: Sioux Nation',
        'required' => false,
    ],
    'splintered-state' => [
        'default' => true,
        'description' => 'Description of Spintered State.',
        'id' => 'splintered-state',
        'name' => 'Splintered State',
        'required' => false,
    ],
    'sprawl-wilds' => [
        'default' => true,
        'description' => 'Description of Sprawl Wilds.',
        'id' => 'sprawl-wilds',
        'name' => 'Sprawl Wilds',
        'required' => false,
    ],
    'stolen-souls' => [
        'default' => true,
        'description' => 'Description of Stolen Souls.',
        'id' => 'stolen-souls',
        'name' => 'Stolen Souls',
        'required' => false,
    ],
    'street-grimoire' => [
        'default' => true,
        'description' => 'Description of Street Grimoire.',
        'id' => 'street-grimoire',
        'name' => 'Street Grimoire',
        'required' => false,
    ],
    'street-lethal' => [
        'default' => true,
        'description' => 'Description of Street Lethal.',
        'id' => 'street-lethal',
        'name' => 'Street Lethal',
        'required' => false,
    ],
    'streetpedia' => [
        'default' => false,
        'description' => 'Description of Neo-Anarchist Streetpedia.',
        'id' => 'streetpedia',
        'name' => 'Neo-Anarchist Streetpedia',
        'required' => false,
    ],
    'ten-terrorists' => [
        'default' => true,
        'description' => 'Description of Ten Terrorists.',
        'id' => 'ten-terrorists',
        'name' => 'Ten Terrorists',
        'required' => false,
    ],
    'toxic-alleys' => [
        'default' => true,
        'description' => 'Description of Toxic Alleys.',
        'id' => 'toxic-alleys',
        'name' => 'Toxic Alleys',
        'required' => false,
    ],
    'unoriginal-sin' => [
        'default' => false,
        'description' => 'https://forums.shadowruntabletop.com/index.php?topic=22282.0||Unoriginal SIN is a rules expansion for SINs, Licenses, & Permits.||The goal was to build upon the SR5 rules and flesh it out while maintaining ease of use. YMMV. It includes SINs, License, and Permits overview (how they work, why one needs fake ones, etc.), new Qualities, and Lifestyle PACKS that incorporate SINs and Licenses.',
        'id' => 'unoriginal-sin',
        'name' => 'Unoriginal SIN',
        'required' => false,
    ],
    'vladivostok-guantlet' => [
        'default' => false,
        'description' => 'Description of Vladivostok Guantlet.',
        'id' => 'vladivostok-guantlet',
        'name' => 'Vladivostok Guantlet',
        'required' => false,
    ],
];
