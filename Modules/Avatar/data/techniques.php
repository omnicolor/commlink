<?php

declare(strict_types=1);

use Modules\Avatar\Enums\TechniqueClass;
use Modules\Avatar\Enums\TechniqueType;

/**
 * @return array<int, array{
 *   class: TechniqueClass,
 *   description: string,
 *   id: string,
 *   name: string,
 *   page: int,
 *   rare: bool,
 *   ruleset: string,
 *   specialization: string|null,
 *   type: TechniqueType,
 * }>
 */
return [
    /*
    [
        'class' => TechniqueClass::,
        'description' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'rare' => false,
        'ruleset' => 'core',
        'specialization' => null,
        'type' => TechniqueType::,
    ],
     */
    [
        'class' => TechniqueClass::AdvanceAndAttack,
        'description' => 'Strike an enemy at a weak point where they’ve already been injured. Mark fatigue to target an engaged, Impaired enemy in reach; they suffer fatigue equal to however many conditions they already have marked.',
        'id' => 'attack-weakness',
        'name' => 'Attack Weakness',
        'page' => 280,
        'rare' => false,
        'ruleset' => 'core',
        'specialization' => null,
        'type' => TechniqueType::Universal,
    ],
    [
        'class' => TechniqueClass::EvadeAndObserve,
        'description' => 'A leading voice in the group takes a moment to organize it effectively. The group clears Impaired, becomes Inspired, and inflicts an additional 1-fatigue on all attacks made next exchange.',
        'id' => 'attend-to-commands',
        'name' => 'Attend to Commands',
        'page' => 281,
        'rare' => false,
        'ruleset' => 'core',
        'specialization' => null,
        'type' => TechniqueType::Group,
    ],
    [
        'class' => TechniqueClass::AdvanceAndAttack,
        'description' => 'Use bloodbending to move and twist a foe’s body in painful ways. You must be Empowered to use this technique. Inflict a condition on your foe. If they are already Impaired, Trapped, or Doomed, inflict an additional condition. If this is your first, second, or third time ever using this technique, mark a condition.',
        'id' => 'blood-twisting',
        'name' => 'Blood Twisting',
        'page' => 282,
        'rare' => true,
        'ruleset' => 'core',
        'specialization' => 'blood',
        'type' => TechniqueType::Waterbending,
    ],
    [
        'class' => TechniqueClass::DefendAndManeuver,
        'description' => 'Use seismic sense to detect the instant an enemy is about to move against you. Become Prepared, and at any time during this exchange, you may lose your Prepared status and mark fatigue to interrupt an enemy as they use a technique; they must mark an additional 3-fatigue or you disrupt their attempt to act, canceling the technique.',
        'id' => 'detect-the-heavy-step',
        'name' => 'Detect the Heavy Step',
        'page' => 283,
        'rare' => true,
        'ruleset' => 'core',
        'specialization' => 'seismic sense',
        'type' => TechniqueType::Earthbending,
    ],
    [
        'class' => TechniqueClass::EvadeAndObserve,
        'description' => 'Unleash your emotions into the flames around you. Mark 1-fatigue to hold 1 for each condition you have marked. Spend your hold 1-for-1 in the next exchange to pay the costs of techniques as if it was fatigue, to inflict Doomed on a foe you target with firebending, or to use Seize a Position—no matter what approach you used—in addition to your other techniques.',
        'id' => 'a-single-spark',
        'name' => 'A Single Spark',
        'page' => 284,
        'rare' => false,
        'ruleset' => 'core',
        'specialization' => null,
        'type' => TechniqueType::Firebending,
    ],
];
