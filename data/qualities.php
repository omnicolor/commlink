<?php

/**
 * List of qualities.
 */

declare(strict_types=1);

return [
    /*
    '' => [
        'adeptOnly' => true,
        'changelingOnly' => true,
        'description' => '',
        'effects' => [],
        'id' => '',
        'incompatible-with' => [],
        'karma' => ,
        'level' => ,
        'magicOnly' => true,
        'name' => '',
        'notDoubled' => true,
        'page' => ,
        'requires' => [],
        'ruleset' => '',
        'severity' => '',
        'technomancerOnly' => true,
    ],
     */
    'addiction-mild' => [
        'id' => 'addiction-mild',
        'description' => 'A character with the Addiction quality is hooked on chemical substances, such as street drugs (novacoke, bliss, tempo); technological or magical devices, such as better-than-life (BTL) chips or foci; or potentially addictive activities such as gambling or sex. Physiological Addictions affect the body\'s functions, producing pain, nausea, shakes, and other side effects that can impair the runner, particularly during withdrawal. Some possible effects of psychological addictions include paranoia, anxiety, insomnia, poor concentration, mood disorders, and depression. For specific rules on Addiction Tests, Withdrawal Tests, and staying clean, see p. 414.',
        'effects' => [
            'notoriety' => 1,
        ],
        'karma' => 4,
        'name' => 'Addiction',
        'severity' => 'Mild',
    ],
    'allergy-uncommon-mild' => [
        'id' => 'allergy-uncommon-mild',
        'description' => 'A character with the Allergy quality is allergic to a substance or condition found in their environment. The value of this quality depends on two factors. The first is whether the substance or condition is Uncommon (2 Karma) or Common (7 Karma). Next, determine the severity of the symptoms: Mild (3 Karma), Moderate (8 Karma), Severe (13 Karma), or Extreme (18 Karma). Add the appropriate point values together to find the final value. For example, the value of an Uncommon Moderate Allergy (Silver) is 10 Karma (2+8 Karma). If a character is attacked with a substance to which they are allergic, they lose 1 die from their Resistance Test for each stage of severity of the Allergy (e.g., 1 die for a Mild allergy, 2 dice for a Moderate allergy, etc.).',
        'karma' => 5,
        'name' => 'Allergy',
        'severity' => 'Uncommon Mild',
    ],
    'alpha-junkie' => [
        'description' => 'Some people need to be in charge no matter what. That\'s you, big guy. Maybe you have trust issues. Maybe you have an inferiority complex. If you\'re in the spotlight and leading the show, all is well, but the moment someone else is calling the shots, you fall to pieces, likely arguing the point or sabotaging the plan just to prove yours was better, anyway. When someone makes a successful Leadership test against you or you otherwise believe someone is trying to take charge over you, you must make a Charisma + Willpower (3) Test. If you fail, you attempt to reassert your control, whether by making your own Leadership test, an Intimidate test, or outright attacking.',
        'id' => 'alpha-junkie',
        'incompatible-with' => ['alpha-junkie'],
        'karma' => 12,
        'name' => 'Alpha Junkie',
        'page' => 151,
        'requires' => [],
        'ruleset' => 'cutting-aces',
    ],
    'aptitude-alchemy' => [
        'description' => 'This quality is how you become even better than the best in the world. The standard limit for skills is 12. Every so often, there is a character who can exceed limitations and be truly exceptional in a particular skill. With this particular quality, the character can have one skill rated at 7 at character creation, and may eventually build that skill up to rating 13. Characters may only take the Aptitude quality once.',
        'effects' => ['maximum-alchemy' => 1],
        'id' => 'aptitude-alchemy',
        'incompatible-with' => ['aptitude-alchemy', 'aptitude-arcana', 'aptitude-archery', 'aptitude-aeronautics-mechanic', 'aptitude-animal-handling', 'aptitude-armorer', 'aptitude-artificing', 'aptitude-artisan', 'aptitude-assensing', 'aptitude-astral-combat', 'aptitude-automatics', 'aptitude-automotive-mechanic', 'aptitude-banishing', 'aptitude-binding', 'aptitude-biotechnology', 'aptitude-blades', 'aptitude-chemistry', 'aptitude-clubs', 'aptitude-compiling', 'aptitude-computer', 'aptitude-con', 'aptitude-counterspelling', 'aptitude-cybercombat', 'aptitude-cybertechnology', 'aptitude-decompiling', 'aptitude-demolitions', 'aptitude-disenchanting', 'aptitude-disguise', 'aptitude-diving', 'aptitude-electronic-warfare', 'aptitude-escape-artist', 'aptitude-etiquette', 'aptitude-exotic-melee-weapon', 'aptitude-exotic-ranged-weapon', 'aptitude-first-aid', 'aptitude-forgery', 'aptitude-free-fall', 'aptitude-gunnery', 'aptitude-gymnastics', 'aptitude-hacking', 'aptitude-hardware', 'aptitude-heavy-weapons', 'aptitude-impersonation', 'aptitude-industrial-mechanic', 'aptitude-instruction', 'aptitude-intimidation', 'aptitude-leadership', 'aptitude-locksmith', 'aptitude-longarms', 'aptitude-medicine', 'aptitude-nautical-mechanic', 'aptitude-navigation', 'aptitude-negotiation', 'aptitude-palming', 'aptitude-perception', 'aptitude-performance', 'aptitude-pilot-aerospace', 'aptitude-pilot-aircraft', 'aptitude-pilot-exotic-vehicle', 'aptitude-pilot-ground-craft', 'aptitude-pilot-walker', 'aptitude-pilot-watercraft', 'aptitude-pistols', 'aptitude-registering', 'aptitude-ritual-spellcasting', 'aptitude-running', 'aptitude-software', 'aptitude-sneaking', 'aptitude-spellcasting', 'aptitude-summoning', 'aptitude-survival', 'aptitude-swimming', 'aptitude-throwing-weapons', 'aptitude-tracking', 'aptitude-unarmed-combat'],
        'karma' => -14,
        'name' => 'Aptitude',
        'page' => 72,
        'ruleset' => 'core',
        'skill' => 'Alchemy',
    ],
    'exceptional-attribute-body' => [
        'id' => 'exceptional-attribute-body',
        'attribute' => 'Body',
        'effects' => [
            'maximum-body' => 1,
        ],
        'incompatible-with' => [
            'exceptional-attribute-agility',
            'exceptional-attribute-body',
            'exceptional-attribute-charisma',
            'exceptional-attribute-intuition',
            'exceptional-attribute-logic',
            'exceptional-attribute-magic',
            'exceptional-attribute-reaction',
            'exceptional-attribute-resonance',
            'exceptional-attribute-strength',
            'exceptional-attribute-willpower',
        ],
        'name' => 'Exceptional Attribute',
        'karma' => -14,
        'description' => 'The Exceptional Atribute quality is how you get to be the charismatic troll, or the agile dwarf. It allows you to possess one attribute at a level one point above the metatype maximum limit. For example, an ork character with the Exceptional Attribute quality for Strength could take his Strength attribute up to 10 before augmentations are applied, instead of the normal limit of 9. Exceptional Attribute also applies toward Special Attributes such as Magic and Resonance. Edge cannot affected by the Exceptional Attribute (Edge is raised by another quality called Lucky). A character may only take Exceptional Attribute once, and only with the gamemaster\'s approval.',
    ],
    'fame-local' => [
        'id' => 'fame-local',
        'incompatible-with' => [
            'fame-local',
            'fame-global',
            'fame-megacorporate',
            'fame-national',
        ],
        'name' => 'Fame',
        'karma' => -4,
        'requires' => [
            [
                'ids' => [
                    'sinner-national',
                    'sinner-criminal',
                    'sinner-corporate-limited',
                    'sinner-corporate',
                ],
                'name' => 'Sinner',
                'type' => 'qualities',
            ],
        ],
        'ruleset' => 'run-faster',
        'severity' => 'local',
        'description' => 'Now if you can just get fortune and money you\'ll have the trifecta. The problem is you\'ve got the element that is not particularly cherished in the shadows. Whether you\'re a former trid star, a local politician, a retired or injured sports star, or the latest up-and-coming rocker, your face is well known. It may be just the local community, a single nation or corp, or possibly the world that knows your ugly mug. No matter who they are or how many folks know your profile, being so recognizable has its pros and cons.||First, on the pro side, Fame offers benefits within certain social circles and additional income if the character also chooses the Day Job quality.||That may sound nice, but being well known is not a great way to get work in the shadows, and therefore causes problems on the darker side of life. Characters with this quality are more likely to be recognized by passers-by or others who see them during a run, which can be a problem. And remember that Fame often involves strings; one of the biggest is having a SIN. Characters who choose this quality must have the SINner quality or a Rating 3 fake SIN.',
    ],
    'indomitable-2' => [
        'id' => 'indomitable-2',
        'name' => 'Indomitable',
        'karma' => -16,
        'level' => 2,
        'effects' => [
            'mental-limit' => 0,
            'physical-limit' => 0,
            'social-limit' => 0,
        ],
        'incompatible-with' => [
            'indomitable-1',
            'indomitable-2',
            'indomitable-3',
        ],
        'description' => 'Bodies and minds have limits, but some people have the will to push right through those boundaries. For each level of Indomitable, a character receives a +1 increase to an Inherent limit of his choice (Mental, Physical, or Social). He can take up to three levels and can apply them in any way he chooses (+3 to one Inherent Limit, for example; or +2 to one Inherent and +1 to another; or +1 to all three).',
    ],
    'lucky' => [
        'id' => 'lucky',
        'description' => 'The dice roll and the coin flips this character\'s way more often than not, giving her the chance to drop jaws in amazement at her good fortune. Lucky allows a character to possess an Edge attribute one point higher than his metatype maximum (for example, a human character could raise her Edge to 8). Note that taking this quality does not actually increase the character\'s current Edge rating, it just allows her the opportunity to do so; the Karma cost for gaining the extra point must still be paid. This quality may only be taken once and must be approved by the gamemaster. The Lucky quality cannot be combined with Exceptional Attribute.',
        'effects' => [
            'maximum-edge' => 7,
            'notoriety' => -1,
        ],
        'incompatible-with' => [
            'lucky',
        ],
        'karma' => -12,
        'name' => 'Lucky',
    ],
];
