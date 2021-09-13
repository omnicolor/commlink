<?php

declare(strict_types=1);

/**
 * List of Shadowrun 5th edition rulebooks.
 */
return [
    /*
    '' => [
        'default' => false, // Whether the ruleset should be checked by default.
        'description' => '',
        'name' => '',
        'reqired' => true, // Whether the ruleset is required to play the game.
    ],
     */
    'core' => [
        'default' => true,
        'description' => 'EVERYTHING HAS A PRICE||There are cracks in the world. They’re slender, dark, and often cold, but they are the only things that keep you hidden. Keep you alive. They are the shadows of the world, and they are where you live.||You are a shadowrunner, thriving in the margins, doing the jobs no one else can. You have no office, no permanent home, no background to check. You are whatever you make yourself. Will you seek justice? Sow seeds of chaos? Sell out to the highest bidder? It’s up to you, but this much is certain: If you do nothing, the streets will eat you alive.||You can survive, even flourish, as long as you do what it takes. Sacrifice part of your soul for bleeding-edge gear. Push the limits of your will learning new and dangerous magic. Wire yourself into the Matrix, making your mind one with screaming streams of data. It’ll cost you something—everything does—but you can make it worth the price.||Shadowrun, Fifth Edition is the newest version of one of the most popular and successful role-playing worlds of all time, a fusion of man, magic, and machine in a dystopian near-future. With rules for character creation, magic, combat, Matrix hacking, rigging, and more, you have everything you need to face the challenges of the Sixth World.',
        'name' => 'Core 5th Edition',
        'required' => true,
    ],
    'forbidden-arcana' => [
        'default' => false,
        'description' => 'Magic is wild. Magic is undisciplined. You can try to impose order and understanding on it, but that’s just surface. Underneath is chaos, an erratic heart beating to a staggering rhythm. You don’t control it, any more than a surfer controls twenty-meter-tall wave; you don’t direct the wave, you ride it, capture a piece of its power, and hope to survive. If you do it right, though, you catch a portion of unimaginable power—power those who control the Sixth World don’t want you to have. All the more reason to push past their boundaries and grab it.||Forbidden Arcana offers dozens of different ways for Awakened characters in Shadowrun to harness that power and make themselves a mana-slinger like no other. From new ways to distinguish spellcasters of different traditions to more chaotic methods for summoning spirits to options for Awakened characters who have mastered their craft, Forbidden Arcana shows players how to break out of conventional molds, use mana in new ways, and become true street legends riding the growing wave of Sixth World mana.',
        'name' => 'Forbidden Arcana',
        'required' => false,
    ],
];
