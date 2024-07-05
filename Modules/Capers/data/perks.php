<?php

declare(strict_types=1);

/**
 * Collection of perks for Capers.
 */
return [
    /*
    '' => [
        'description' => '',
        'name' => '',
    ],
     */
    'fleet-of-foot' => [
        'description' => 'Your character’s foot Speed increases to 40’.',
        'name' => 'Fleet of Foot',
    ],
    'hardy-body' => [
        'description' => 'Your character gains +1 to their Body score. You can select this Perk up to two times.',
        'name' => 'Hardy Body',
    ],
    'lucky' => [
        'description' => 'Once per game session, when you Botch, you succeed instead. Treat the success as if you got spades on the card and apply a Boon to the success. If the Botch card was the bad joker, you do not end your turn immediately and continue to resolve your turn.||You can select this Perk up to three times. If you do this, you can use this ability once per game session per number of times you’ve selected it.',
        'name' => 'Lucky',
    ],
    'power-resistant' => [
        'description' => 'Each time your character’s Body or Mind is targeted with a Power, you flip a card. The TS for the Power targeting you is your current Body or Mind (as called for by the Power) or the card you flipped, whichever is higher. If you flip the bad joker, ignore it and flip another card to replace it. If you flip the good joker, the Power automatically fails against you.||Additionally, any time a Power has an effect that you can resist by making a Trait Check, you automatically succeed on that check the first time the Power calls for you to make it.||If you select this Perk, your character can never gain Powers in the future, not even temporarily.',
        'name' => 'Power Resistant',
    ],
    'quick-reflexes' => [
        'description' => 'Whenever you make a Reaction Check (including initiative checks), flip two cards and take the better of the two.',
        'name' => 'Quick Reflexes',
    ],
    'refocus' => [
        'description' => 'Once per game session, you can “re-flip” your entire Trait Check. Ignore the results of your current Trait Check (even if you Botched) and start over from scratch. You can’t use this Perk to "re-flip" after you flip the bad joker.||You can select this Perk up to three times. If you do this, you can use this ability once per game session per number of times you’ve selected it. You can only use this Perk once during a given Trait Check.||If you choose this Perk and your character gains Powers later, you can only use this Perk on Trait Checks. You can never again use it on Power Checks.',
        'name' => 'Refocus',
    ],
    'specialty-skill' => [
        'description' => 'Choose one Skill your character already has. This Skill becomes a Specialty Skill. Instead of increasing your Card Count by 1 when you make a Trait Check involving this Skill, you increase your Card Count by 2.||Additionally, your character never Botches this Skill. If you Botch a Trait Check involving this Skill, treat it like a normal failure. If you get the bad joker, you don’t Botch, but your turn still ends immediately.||You can choose this Perk multiple times, each time choosing a different Skill in which to specialize.||You can denote this on your character sheet with a “2” after the Skill’s name.',
        'name' => 'Specialty Skill',
    ],
    'tough' => [
        'description' => 'Your character’s maximum Hits increases by 4.',
        'name' => 'Tough',
    ],
    'wily-mind' => [
        'description' => 'Your character gains +1 to their Mind score. You can select this Perk up to two times.',
        'name' => 'Wily Mind',
    ],
];
