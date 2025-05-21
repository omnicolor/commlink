<?php

declare(strict_types=1);

return [
    /*
    [
        'id' => '',
        'cost' => ,
        'description' => '',
        'name' => '',
        'opposes' => '[]',
        'page' => ,
        'quote' => '',
        'ruleset' => 'core',
        'types' => '["multiple","negative","opposed","positive",]',
    ],
     */
    [
        'id' => 'ambidextrous',
        'cost' => 2,
        'description' => 'A character with the Ambidextrous Trait can use both hands equally well (a character without this Trait must select a primary and an “off” hand during character generation). Ambidextrous characters ignore the off-hand modifier in game play and in combat. Though this Trait allows the character to carry and use a weapon equally well with either hand in combat, it does not confer the ability to exceed the normal number of actions allowed in a combat turn.',
        'name' => 'Ambidextrous',
        'opposes' => '[]',
        'page' => 108,
        'quote' => 'I have some bad news for you, my friend: I’m not left-handed either.',
        'ruleset' => 'core',
        'types' => '["positive"]',
    ],
    [
        'id' => 'animal-antipathy',
        'cost' => -1,
        'description' => 'A character with the Animal Antipathy Trait manages to bring out the worst in all creatures, great and small. Whether or not the character feels the same way, something about the character makes animals react far more negatively to his presence than they would otherwise. In game play, Animal Antipathy imposes a –2 modifier to all Skill Checks involving animals, such as Animal Handling, Riding and so forth. This effect also doubles the modifiers for a creature’s Shy or Aggressive Traits whenever said creature must make a Fight or Flight Check in the character’s presence (see Creatures, p. 238). Any creature with the Aggressive Trait will also behave as if it has the Blood Rage Trait while in the character’s presence. If combat ensues, a creature so enraged will attack the nearest character with this Trait first.',
        'name' => 'Animal Antipathy',
        'opposes' => '["animal-empathy"]',
        'page' => 108,
        'quote' => 'Blake’s Blood, Roger! Did you lose a fight with your girlfriend’s kitten again?',
        'ruleset' => 'core',
        'types' => '["negative","opposed"]',
    ],
    [
        'id' => 'animal-empathy',
        'cost' => 1,
        'description' => 'A character with the Animal Empathy Trait has a natural gift with creatures, even those he may ordinarily despise. In game play, Animal Empathy imposes a +2 modifier to all Skill Checks involving animals, such as Animal Handling, Riding and so forth. This effect also halves the modifiers (rounding down) for a creature’s Shy or Aggressive Traits whenever said creature must make a Fight or Flight Check in the character’s presence (see Creatures, p. 238). Creatures without the Aggressive Trait behave as if they have the Shy Trait while in the character’s presence, while creatures with the Shy Trait behave as if Tamed instead. If combat ensues, creatures will attack characters with this Trait last—unless the animal empathic character directly attacks the creature first.',
        'name' => 'Animal Empathy',
        'opposes' => '["animal-antipathy"]',
        'page' => 108,
        'quote' => 'Oh, yeah, I know the Kountze arctic terror looks pretty frightening, but if you know your way around them, they’re really not that hard to handle.',
        'ruleset' => 'core',
        'types' => '["opposed","positive"]',
    ],
    [
        'id' => 'fast-learner',
        'cost' => 3,
        'description' => 'Fast learner description.',
        'name' => 'Fast Learner',
        'opposes' => '["slow-learner"]',
        'page' => 117,
        'quote' => 'Training manual? I don’t need no malfing training manual!',
        'ruleset' => 'core',
        'types' => '["opposed","positive"]',
    ],
    [
        'id' => 'slow-learner',
        'cost' => -3,
        'description' => 'Slow learner description.',
        'name' => 'Slow Learner',
        'opposes' => '["fast-learner"]',
        'page' => 125,
        'quote' => 'Can you go over that whole procedure one more time? I almost have it now…',
        'ruleset' => 'core',
        'types' => '["negative","opposed"]',
    ],
];
