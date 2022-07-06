<?php

declare(strict_types=1);

/**
 * List of weaknesses a critter can have.
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
    'allergy' => [
        'description' => 'Like characters, many critters suffer allergies to particular substances or conditions. Treat this weakness as the Allergy negative quality (p. 78).',
        'name' => 'Allergy',
        'page' => 401,
        'ruleset' => 'core',
    ],
    'dietary-requirement' => [
        'description' => 'Everybody’s got to eat. Critters with this weakness have to include something strange or exotic in their diets. Typical examples include toxic waste, petroleum, gold, or metahuman flesh. Unless specified in the critter’s description, it needs at least one meal per day, appropriate to its size and metabolism. Unless it gets the specified requirement in its diet, the critter will eventually grow sick and die.',
        'name' => 'Dietary requirement',
        'page' => 401,
        'ruleset' => 'core',
    ],
    'essence-loss' => [
        'description' => 'Certain critters, most notably the Infected, have no actual Essence of their own; they exist by stealing Essence from other beings. Not only do they not have their own Essence, they slowly lose any Essence they’ve stolen, at the rate of 1 point of Essence every lunar month. Losing Essence also means that a critter’s Magic might be affected (see p. 278).||If the critter is reduced to an Essence score of 0, it’s living on borrowed time. It will die a very unpleasant death in Body + Willpower days unless it feeds and replenishes its Essence. Such a critter is a starving predator in search of prey for sustenance, and as such is extraordinarily dangerous.||Certain powers of the HMHVV Infected accelerate Essence Loss. Any power that is not automatic (meaning it requires an action to use) is Essence-intensive for the Infected. Each use of these powers accelerates the loss of Essence by one week.',
        'name' => 'Essence loss',
        'page' => 401,
        'ruleset' => 'core',
    ],
    'induced-dormancy' => [
        'description' => 'Some condition or substance can force some critters into a coma-like state of suspended animation. The condition can be a lack of air, for example, or a certain rare substance such as orichalcum. The length of exposure needed to cause the critter to become dormant varies and is mentioned in the critter’s description. The critter will awaken quickly, usually within a minute, once the condition or substance is removed.',
        'name' => 'Induced dormancy',
        'page' => 401,
        'ruleset' => 'core',
    ],
    'reduced-senses' => [
        'description' => 'Any or all of the critter’s five basic senses may be limited in effectiveness. Typically, reduced senses function at half the normal range or effectiveness, but they might be reduced even further, possibly to the point of the complete loss of that sense.',
        'name' => 'Reduced senses',
        'page' => 401,
        'ruleset' => 'core',
    ],
    'uneducated' => [
        'description' => 'Treat this weakness as the Uneducated Negative quality (p. 87).',
        'name' => 'Uneducated',
        'page' => 401,
        'ruleset' => 'core',
    ],
    'vulnerability' => [
        'description' => 'Some critters have an Achilles’ heel, something that hurts them more than other things, or against which they have no defense. Sometimes it’s a substance. This substance, be it wood or gold or ferrocrete, causes additional damage when used as a weapon against the critter. Increase the Damage Value of all attacks with the substance by 3. Weapons made of something the critter is vulnerable to bypass any Immunities it might have. Damage taken from the substance to which a critter is vulnerable can’t be healed by Regeneration or healing magic, only by natural healing.||Some Vulnerabilities are conditions, not substances. For instance, a basilisk is vulnerable to its own Petrification power. In such cases, a –3 dice pool modifier is applied to the critter’s Resistance Test to avoid the condition.',
        'name' => 'Vulnerability',
        'page' => 401,
        'ruleset' => 'core',
    ],
];
