<?php

declare(strict_types=1);

/**
 * Collection of powers for Capers.
 */
return [
    /*
    '' => [
        'activation' => '',
        'boosts' => [
            '' => [
                'description' => '',
                'name' => '',
            ],
        ],
        'description' => '',
        'duration' => '',
        'effect' => '',
        'maxRank' => ,
        'name' => '',
        'range' => '',
        'target' => '',
        'type' => '',
    ],
     */
    'acid-stream' => [
        'activation' => 'Power Check vs. target’s Body',
        'boosts' => [
            'acrid-cloud-boost' => [
                'description' => 'Instead of standard effect, you create a 10’ radius sphere composed of stinging, airborne acid. Anyone entering or beginning their turn in the field takes Color Hits damage (acid). You can maintain this Boost out to this Power’s range.',
                'name' => 'Acrid cloud boost',
            ],
            'blast-boost' => [
                'description' => 'Instead of standard effect, you deal Suit Hits damage (acid) to all targets in a 5’ radius area within range. Use each target’s individual Body scores as Target Scores for one Power Check. It’s possible to hit some targets with the Power Check and not others.',
                'name' => 'Blast boost',
            ],
            'control-boost' => [
                'description' => 'You deal only as much damage as you want. Choose your normal Power Check damage or any lower amount, minimum 1 Hit.',
                'name' => 'Control boost',
            ],
            'cutting-boost' => [
                'description' => 'You can cut through up to 8” of concrete or a thin metal plate.',
                'name' => 'Cutting boost',
            ],
            'damage-boost' => [
                'description' => 'Damage from this Power increases by +2 Hits damage (acid).',
                'name' => 'Damage boost',
            ],
            'immunity-boost' => [
                'description' => 'You are immune to acid damage until the beginning of your next turn.',
                'name' => 'Immunity boost',
            ],
            'liquefy-boost' => [
                'description' => 'Instead of standard effect, you liquefy a single small, non-stone, non-metal object.',
                'name' => 'Liquefy Boost',
            ],
            'persistence-boost' => [
                'name' => 'Persistence boost',
                'description' => 'The target takes Color damage on each of your turns until the acid is neutralized. The target can neutralize the acid by spending all of their movement doing so or by suffering disadvantage on checks during the turn spent clearing away the acid.',
            ],
            'range-boost' => [
                'name' => 'Range Boost',
                'description' => 'The range of this Power increases to 100’ until the beginning of your next turn.',
            ],
        ],
        'description' => 'You create a stream of corrosive acid.',
        'duration' => 'Instantaneous',
        'effect' => 'You deal Suit Hits damage (acid) to one target. If you hit the target with a Boon, you also deal 1 Hits damage (acid) to one target that is adjacent to the initial target. Alternatively, you corrode a small object.',
        'maxRank' => 5,
        'name' => 'Acid stream',
        'range' => '30’',
        'target' => 'Anything',
        'type' => 'Minor',
    ],
    'alter-form' => [
        'activation' => 'Free action',
        'boosts' => [
            'density-decrease-boost' => [
                'description' => 'Instead of standard effect, until the beginning of your next turn, you become lighter as your body’s density decreases. You can climb and swim at half your Speed without making Trait Checks.',
                'name' => 'Density decrease boost',
            ],
            'density-increase-boost' => [
                'name' => 'Density increase boost',
                'description' => 'Instead of standard effect, until the beginning of your next turn, you become heavier as your body’s density increases. You can’t be knocked over. You deal +1 Hits damage with all Fisticuffs and Melee Weapon attacks.',
            ],
            'gaseous-form-boost' => [
                'name' => 'Gaseous form boost',
                'description' => 'Instead of standard effect, you transform into a barely visible, gaseous cloud the same size as your normal body until the beginning of your next turn. While in this form, you are invulnerable to all attacks except fire, cold, and force attacks, and those that target your Mind score. You can’t affect anything physically or speak, but you can fly at Speed 30. You can squeeze through any narrow space. You can’t use any of your other Powers.',
            ],
            'immovability-boost' => [
                'name' => 'Immovability boost',
                'description' => 'Instead of standard effect, until the beginning of your next turn, you can’t be moved from where you are by any outside force.',
            ],
            'liquid-form-boost' => [
                'name' => 'Liquid form boost',
                'description' => 'Instead of standard effect, you transform into a pool of liquid the same general size as your normal body until the beginning of your next turn. While in this form, you are invulnerable to all attacks except energy attacks and those that target your Mind score. You can’t speak, but you can manipulate things physically and can move at Speed 30. You can squeeze through any narrow space and can move up inclined and vertical surfaces at Speed 15. You can’t use any of your other Powers.',
            ],
        ],
        'description' => 'You change the fundamental nature of your physical form.',
        'duration' => 'Continuous once activated',
        'effect' => 'When you select this Power, choose one Boost to use as the standard effect for this Power. It is no longer considered a Boost; it counts as the standard effect. This does not count as one of the three Boosts you get when you take rank 1.',
        'maxRank' => 1,
        'name' => 'Alter form',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Minor',
    ],
    'animal-affinity' => [
        'activation' => 'Free action',
        'boosts' => [
            'cat-eyes-boost' => [
                'description' => 'You can see in darkness until the beginning of your next turn.',
                'name' => 'Cat eyes boost',
            ],
            'claw-or-bite-boost' => [
                'description' => 'You grow claws or fangs that deal Suit Hits damage and count as Fisticuffs until the beginning of your next turn.',
                'name' => 'Claw or bite boost',
            ],
            'fins-and-gills-boost' => [
                'description' => 'Until the beginning of your next turn, you sprout gills and can breathe underwater. Your hands and feet become webbed and you swim at full speed without making checks.',
                'name' => 'Fins and gills boost',
            ],
            'greater-animal-form-boost' => [
                'description' => 'When you take this Boost, choose a level 3 or 4 animal. You transform into that animal while you maintain this Boost. You take on all aspects of that animal except you keep your normal Charisma, Expertise, Perception, and Mind scores as well as all of your Skills. You cannot use any other Animal Affinity Boosts when in this form. You cannot use any Powers that require an ability that humans have and animals don’t, such as speaking a language.',
                'name' => 'Greater animal form boost',
            ],
            'lesser-animal-form-boost' => [
                'description' => 'When you take this Boost, choose a level 1 or level 2 animal. You transform into that animal while you maintain this Boost. You take on all aspects of that animal except you keep your normal Charisma, Expertise, Perception, and Mind scores as well as all of your Skills. You cannot use any other Animal Affinity Boosts when in this form. You cannot use any Powers that require an ability that humans have and animals don’t, such as speaking a language.',
                'name' => 'Lesser animal form boost',
            ],
            'owl-ears-boost' => [
                'description' => 'You can hear perfectly across 1/4 mile until the beginning of your next turn.',
                'name' => 'Owl ears boost',
            ],
            'scent-boost' => [
                'description' => 'You can track by scent like a dog until the beginning of your next turn.',
                'name' => 'Scent boost',
            ],
            'wings-boost' => [
                'description' => 'Until the beginning of your next turn, you sprout wings and can fly at Speed 30.',
                'name' => 'Wings boost',
            ],
        ],
        'description' => 'You take on aspects of various animals and even transform into animals.',
        'duration' => 'Continuous once activated',
        'effect' => 'When you select this Power, choose one Boost other than Lesser Animal Form or Greater Animal Form to use as the standard effect for this Power. It is no longer considered a Boost; it counts as the standard effect. This does not count as one of the three Boosts you get when you take rank 1.||At rank 1, you can use Boosts to take on aspects of certain animals. At rank 2, you can select Lesser Animal Form Boost. At rank 3, you can select Greater Animal Form Boost.|| You must take the Animal Form Boosts multiple times to gain multiple animal forms. Your clothing and held objects meld into your body when you transform. If you lose the form’s Hits score in damage, you revert to your normal form with those Hits lost. You can’t transform again until you regain those lost Hits.',
        'maxRank' => 3,
        'name' => 'Animal affinity',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'body-armor' => [
        'activation' => 'NA',
        'boosts' => [
            'body-boost' => [
                'description' => 'Increase your Body by 1 until the beginning of your next turn.',
                'name' => 'Body boost',
            ],
            'energy-boost' => [
                'description' => ' Choose an energy type (acid, cold, electricity, fire). You take half damage from that energy type until the beginning of your next turn.',
                'name' => 'Energy boost',
            ],
            'fall-boost' => [
                'description' => 'Until the beginning of your next turn, you can fall from any height without taking damage.',
                'name' => 'Fall boost',
            ],
            'healing-boost' => [
                'description' => 'Until the beginning of your next turn, each time you spend Moxie to prevent Hits damage, you prevent 2 additional Hits damage.',
                'name' => 'Healing boost',
            ],
            'imbue-boost' => [
                'description' => 'One person gains your Power bonus to their Body score until the beginning of your next turn. The two of you must be within 30’ of each other for this to function.',
                'name' => 'Imbue boost',
            ],
            'rebound-boost' => [
                'description' => 'One time before the beginning of your next turn, when a projectile misses you, it instead strikes the attacker, dealing damage based on the attacker’s card flip to make the attack.',
                'name' => 'Rebound boost',
            ],
        ],
        'description' => 'Your skin is tough or sheathed in a hard material.',
        'duration' => 'Continuous',
        'effect' => 'At rank 1, your Body increases by 1. At rank 2, your Body instead increases by 2.',
        'maxRank' => 2,
        'name' => 'Body armor',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Minor',
    ],
    'bone-organ-shifting' => [
        'activation' => 'Free Action unless noted otherwise',
        'boosts' => [
            'bone-armor-boost' => [
                'description' => 'Instead of standard effect, until the beginning of your next turn, your Body increases by 1.',
                'name' => 'Bone armor boost',
            ],
            'bone-spurs-boost' => [
                'description' => 'Instead of standard effect, bone spurs grow from your hands. You can attack with them as Fisticuffs, dealing Suit Hits damage. You can retract or extend them as Free Actions.',
                'name' => 'Bone spurs boost',
            ],
            'bone-throw-boost' => [
                'description' => 'Instead of standard effect, you can fling little bones from your hands as an Agility/Ranged Weapon attack that deals Suit Hits damage at a range of 30/100. Your hand bones regrow immediately.',
                'name' => 'Bone throw boost',
            ],
            'organ-shift-boost' => [
                'description' => 'Instead of standard effect, one time before the beginning of your next turn, when a physical attack hits you, the attack misses instead.',
                'name' => 'Organ shift boost',
            ],
            'slither-boost' => [
                'description' => 'Instead of standard effect, you can liquefy yourself as a Free Action, allowing you to pass through areas as small as one square inch. While in this form, you can’t do anything except move and reform yourself, which requires a Free Action.',
                'name' => 'Slither boost',
            ],
        ],
        'description' => 'You manipulate your bones and organs to create a variety of effects.',
        'duration' => 'Continuous',
        'effect' => 'When you select this Power, choose one Boost to use as the standard effect for this Power. It is no longer considered a Boost; it counts as the standard effect. This does not count as one of the three Boosts you get when you take rank 1.',
        'maxRank' => 1,
        'name' => 'Bone/organ shifting',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Minor',
    ],
    'cold-beam' => [
        'activation' => 'Power Check vs target\'s Body',
        'boosts' => [
            'blast-boost' => [
                'description' => 'Instead of standard effect, you deal Suit Hits damage (cold) to all targets in a 5’ radius area within range. Use each target’s individual Body scores as Target Scores for one Power Check. It’s possible to hit some targets with the Power Check and not others.',
                'name' => 'Blast boost',
            ],
            'damage-boost' => [
                'description' => 'Damage from this Power increases by +2 Hits damage (cold).',
                'name' => 'Damage boost',
            ],
            'encasement-boost' => [
                'description' => 'The target is partially encased in ice. If you encase a hand/arm, the target can’t use that hand/arm until it is freed. If you encase a foot, the target can’t move from that spot until freed. Breaking free requires a Strength/Athletics check at TS 10 and uses up the target’s movement for the turn.',
                'name' => 'Encasement boost',
            ],
            'fog-boost' => [
                'description' => 'Instead of standard effect, you create a 20’ radius sphere of fog within range. Actions requiring sight within or through the fog suffer disadvantage. The fog persists until it dissipates naturally.',
                'name' => 'Fog boost',
            ],
            'ice-shape-boost' => [
                'description' => 'Instead of standard effect, you create a simple object made of ice. It can have no moving parts and must fit inside a cube with sides equal to 2 times your rank in Cold Beam in feet. If you make a melee weapon, it deals Suit damage (cold). You can give this item to another person while maintaining this Boost.',
                'name' => 'Ice shape boost',
            ],
            'immunity-boost' => [
                'description' => 'You are immune to cold damage until the beginning of your next turn.',
                'name' => 'Immunity boost',
            ],
            'ramp-boost' => [
                'description' => 'You create an ice ramp that you ride up to 50’ as your movement. The ramp can rise up to 30’. It lasts until it melts.',
                'name' => 'Ramp boost',
            ],
            'range-boost' => [
                'description' => 'The range of this Power increases to 100’ until the beginning of your next turn.',
                'name' => 'Range boost',
            ],
            'slick-boost' => [
                'description' => 'Instead of standard effect, you make a 10’ radius area within range slippery until the ice melts. Moving across the ice requires an Agility/Acrobatics Check at TS 8 + your rating in this Power or fall prone.',
                'name' => 'Slick boost',
            ],
        ],
        'description' => 'You create a freezing blast of arctic energy.',
        'duration' => 'Instantaneous',
        'effect' => 'You deal Suit Hits damage (cold) to one target. Alternatively, you cool or freeze a small amount of material.',
        'maxRank' => 5,
        'name' => 'Cold beam',
        'range' => '30’',
        'target' => 'Anything',
        'type' => 'Minor',
    ],
    'dimension-step' => [
        'activation' => 'Give up half your movement for the turn',
        'boosts' => [
            'chaperone-boost' => [
                'description' => 'You can bring one willing person with you when you go through the portal.',
                'name' => 'Chaperone boost',
            ],
            'force-blast-boost' => [
                'description' => 'Choose one target adjacent to the distant portal. The target takes Color Hits damage (force) when you step through.',
                'name' => 'Force-blast boost',
            ],
            'range-boost' => [
                'description' => 'The range of this Power increases to 100’ until the beginning of your next turn.',
                'name' => 'Range boost',
            ],
            'redirect-boost' => [
                'description' => 'One time before the beginning of your next turn, when a projectile attack hits you, it misses you instead as a small pair of portals redirect the projectile.',
                'name' => 'Redirect boost',
            ],
            'size-boost' => [
                'description' => 'Both portals are only 2’ in diameter.',
                'name' => 'Size boost',
            ],
            'slingshot-boost' => [
                'description' => 'Give up all your movement for the turn to use this Boost. When you step through the portal, you appear at the other one, take the rest of your turn, and then are drawn back through the portal to your original location. Your turn then ends. If you take someone with you using Chaperone Boost, you can leave them there or return with them to your original location. The portals close after you return.',
                'name' => 'Slingshot boost',
            ],
        ],
        'description' => 'You can instantly travel across space.',
        'duration' => 'Until the portal closes',
        'effect' => 'At rank 1, you create a 6’ diameter portal next to you and another one at a location you can see within range. You can look into your portal to see out of the other. You can fire projectiles or energy powers through the portals. If you step through your portal, you exit the other one and both portals close. At rank 2, the second portal can be anywhere within range, even if you can’t see the location. If a solid object exists where the other portal opens, you exit the portal adjacent to the object. Regardless of rank, both portals close at the end of your turn if they’re still active.',
        'maxRank' => 2,
        'name' => 'Dimension step',
        'range' => '30’s',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'elasticity' => [
        'activation' => 'Free action',
        'boosts' => [
            'far-punch-boost' => [
                'description' => 'You elongate one limb up to 30’ and make one Fisticuffs or Melee attack where your hand or foot now is, then immediately retract the limb.',
                'name' => 'Far punch boost',
            ],
            'fling-boost' => [
                'description' => 'You fling an object weighing less than thirty pounds up to 100’. If you make an attack in this way, it is a Strength/Ranged Weapons check.',
                'name' => 'Fling boost',
            ],
            'ladder-boost' => [
                'description' => 'You elongate your legs and arms up to 20’ each. Other characters can climb up or down you at full speed without making any Trait Checks.',
                'name' => 'Ladder boost',
            ],
            'missed-me-boost' => [
                'description' => 'One time before the beginning of your next turn, when a physical attack hits you, the attack misses instead.',
                'name' => 'Missed me boost',
            ],
            'stride-boost' => [
                'description' => 'When you elongate your legs, your foot Speed increases by 30’.',
                'name' => 'Stride boost',
            ],
            'tie-up-boost' => [
                'description' => 'You elongate two limbs and wrap them around a target that you are adjacent to. The target can escape using normal rules for escaping a grapple, but the TS is increased by this Power’s rating.',
                'name' => 'Tie up boost',
            ],
        ],
        'description' => 'You can stretch your limbs, torso, and neck.',
        'duration' => 'Continuous',
        'effect' => 'At rank 1, you can elongate up to two limbs (including your neck) up to 15’ each as a Free Action. You can reduce back to normal as a Free Action, but can’t elongate and reduce on the same turn. At rank 2, you can elongate all four limbs and your neck up to 15’ each. You can reduce back to normal as a Free Action, but can’t elongate and reduce on the same turn.',
        'maxRank' => 2,
        'name' => 'Elasticity',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'goo-generation' => [
        'activation' => 'Free action',
        'boosts' => [
            'ball-boost' => [
                'description' => 'You fire a single ball of goo at any target within range 30’. Make an Agility/Ranged Weapons check to hit the target.',
                'name' => 'Ball boost',
            ],
            'ceiling-boost' => [
                'description' => 'Until the beginning of your next turn, you can climb on the underside of horizontal surfaces at your normal Speed without making Trait Checks.',
                'name' => 'Ceiling boost',
            ],
            'non-sticky-boost' => [
                'description' => 'Any goo you create this round is not sticky.',
                'name' => 'Non-sticky boost',
            ],
            'range-boost' => [
                'description' => 'The range of this Power increases to 100’ until the beginning of your next turn.',
                'name' => 'Range boost',
            ],
            'rope-boost' => [
                'description' => 'You shoot a rope of goo from your hand to an object or surface with a range of 30’. You can climb the rope or swing on it. You can snap it backward to pull a small object into your hand. If you want to pull a held object to your hand, make a Strength/Athletics check against the target’s Strength Defense.',
                'name' => 'Rope boost',
            ],
            'web-boost' => [
                'description' => 'You create a 10’ diameter web between two or more surfaces/objects.',
                'name' => 'Web boost',
            ],
        ],
        'description' => 'You secrete viscous, sticky goo from your hands.',
        'duration' => 'Continuous once activated',
        'effect' => 'You create sticky goo that covers your hands. You cannot be disarmed. You can climb vertical surfaces at your normal Speed without making Trait Checks.',
        'maxRank' => 1,
        'name' => 'Goo generation',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'illusions' => [
        'activation' => 'Trait action',
        'boosts' => [
            'adornment-boost' => [
                'description' => 'Instead of standard effect, you create a small illusion (must fit in a 3’ cube) that must be placed on your person. You can only have one illusion of this nature in existence at any one time. It cannot be used to change your body’s physical shape or size.',
                'name' => 'Adornment boost',
            ],
            'extend-boost' => [
                'description' => 'The illusion persists until you stop using this Boost. You can alter the illusion on your turn as a Free Action.',
                'name' => 'Extend boost',
            ],
            'movement-boost' => [
                'description' => 'When you create the illusion, you can program it to repeat certain movements and sounds for its duration.',
                'name' => 'Movement boost',
            ],
            'physicality-boost' => [
                'description' => 'The illusion can interact with (and even attack) one creature or other physical object of your choosing until the beginning of your next turn. The illusion attacks using your Trait/Skill, as appropriate to the attack type. If you hit, the attack deals Color+1 Hits damage. It does not deal damage based on what the attack looks like.',
                'name' => 'Physicality boost',
            ],
            'range-boost' => [
                'description' => 'The range of this Power increases to 100’ until the beginning of your next turn.',
                'name' => 'Range boost',
            ],
            'size-boost' => [
                'description' => 'Increase the size of the cube by 5’ on each side.',
                'name' => 'Size boost',
            ],
        ],
        'description' => 'You create a realistic illusion from nothing.',
        'duration' => '1 round',
        'effect' => 'At rank 1, you can create a visual and auditory illusion that fits inside a 10’ cube. At rank 2, you can create a visual and auditory illusion that fits in a 15’ cube. In both cases, the illusion remains static once created.',
        'maxRank' => 2,
        'name' => 'Illusions',
        'range' => '30’',
        'target' => 'NA',
        'type' => 'Major',
    ],
    'influence-emotions' => [
        'activation' => 'Charisma/Willpower vs Target’s Mind',
        'boosts' => [
            'amnesia-boost' => [
                'description' => 'The target does not remember having their emotions manipulated.',
                'name' => 'Amnesia boost',
            ],
            'negative-emotion-boost' => [
                'description' => 'You can imbue more complex, negative emotions into the target. Each time you use this Boost, choose disgust, shame, pity, envy, pride, or confusion. You must have rank 2 in Influence Emotions to select this Boost.',
                'name' => 'Negative emotion boost',
            ],
            'positive-emotion-boost' => [
                'description' => 'You can imbue more complex, positive emotions into the target. Each time you use this Boost, choose trust, anticipation, surprise, friendship, kindness, love, courage, or hope. You must have rank 2 in Influence Emotions to use this Boost.',
                'name' => 'Positive emotion boost',
            ],
            'range-boost' => [
                'description' => 'The range of this Power increases to 100’ until the beginning of your next turn.',
                'name' => 'Range boost',
            ],
            'soothing-boost' => [
                'description' => 'Immediately calm a target within range, removing the effects of an over-active emotion.',
                'name' => 'Soothing boost',
            ],
            'twin-boost' => [
                'description' => 'Apply the standard effect to two targets. Use each target’s individual Mind scores as Target Scores for one Trait Check. It’s possible to affect one target and not the other.',
                'name' => 'Twin boost',
            ],
        ],
        'description' => 'You can manipulate others peoples’ emotions.',
        'duration' => '1 round',
        'effect' => 'At rank 1, you impose a simple emotion on the target. Choose joy or lust (positive emotions) OR choose anger, sadness, or fear (negative emotions). The target feels the emotion and makes choices according to how it influences them. At rank 2, you can maintain a simple emotion in the target by using Positive Emotion Boost or Negative Emotion Boost, as appropriate to the type of emotion created with this effect. You can also create more complex emotions using Positive Emotion Boost and Negative Emotion Boost and maintain them as well.||Resisting: If you maintain the Power using a Boost, the target can attempt to resist by making a Perception/ Willpower Check vs. your Mind. This can be attempted once at the end of each of the target’s turns. Success indicates the target shrugs off the effect. Success with a Boon results in the target being immune to this Power for 24 hours.',
        'maxRank' => 3,
        'name' => 'Influence emotions',
        'range' => '30’',
        'target' => 'Any intelligent creature',
        'type' => 'Major',
    ],
    'invisibility' => [
        'activation' => 'Free action',
        'boosts' => [
            'attack-boost' => [
                'description' => 'You don’t become visible when you attack.',
                'name' => 'Attack boost',
            ],
            'imbue-boost' => [
                'description' => 'One other person becomes invisible when you do until the beginning of your next turn. The two of you must be in physical contact with each other to maintain the other person’s invisibility.',
                'name' => 'Imbue boost',
            ],
            'move-boost' => [
                'description' => 'Until the beginning of your next turn, you remain invisible while moving up to your normal Speed during the round.',
                'name' => 'Move boost',
            ],
            'run-boost' => [
                'description' => 'You must have used the Move Boost this turn to use the Run Boost. Until the beginning of your next turn, you remain invisible while moving more than your normal Speed.',
                'name' => 'Run boost',
            ],
        ],
        'description' => 'You can disappear from view.',
        'duration' => 'Continuous once activated',
        'effect' => 'You become invisible but cannot move more than 2’ per round. You can only turn yourself invisible once per round. Invisibility includes your clothing and gear carried. If you attack, you become visible. Any item you let go of or drop immediately becomes visible.',
        'maxRank' => 1,
        'name' => 'Invisibility',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'mimic' => [
        'activation' => 'Trait action',
        'boosts' => [
            'garb-boost' => [
                'description' => 'You change the appearance of your clothing along with your physical appearance.',
                'name' => 'Garb boost',
            ],
            'height-boost' => [
                'description' => 'You are not limited by height when changing appearance.',
                'name' => 'Height boost',
            ],
            'language-boost' => [
                'description' => 'Until the beginning of your next turn, you can speak one language the person you’re mimicking can speak while mimicking them. You cannot read and write this language.',
                'name' => 'Language boost',
            ],
            'quicken-boost' => [
                'description' => 'You change form as a Free Action.',
                'name' => 'Quicken boost',
            ],
            'read-write-boost' => [
                'description' => 'Until the beginning of your next turn, you can read and write one language the person you’re mimicking can read and write.',
                'name' => 'Read/write boost',
            ],
        ],
        'description' => 'You can change your appearance to look like other people.',
        'duration' => 'Continuous once activated',
        'effect' => 'At rank 1, you can take on the appearance of any other person within one foot of your height, but you can’t mimic their voice. At rank 2, you can take on the appearance of any other person and mimic that person’s voice if you have heard them speak. Changing forms requires a Trait Action. Your clothing does not change. If you fall unconscious or die, you revert to your normal form.',
        'maxRank' => 2,
        'name' => 'Mimic',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'speedster' => [
        'activation' => 'NA',
        'boosts' => [
            'chaperone-boost' => [
                'description' => 'You carry one willing person with you at your increased speed.',
                'name' => 'Chaperone boost',
            ],
            'damage-boost' => [
                'description' => 'If you run at your target first, a successful Melee Weapon or Fisticuffs attack against that target deals 2 additional Hits damage.',
                'name' => 'Damage boost',
            ],
            'lightning-boost' => [
                'description' => 'Choose one target that you run past during the turn. The target takes Color Hits damage (electricity).',
                'name' => 'Lightning boost',
            ],
            'speed-boost' => [
                'description' => 'Until the beginning of your next turn, increase your Speed rating by 30’.',
                'name' => 'Speed boost',
            ],
            'water-walk-boost' => [
                'description' => 'You can run across water until the beginning of your next turn. If you stop on the water, you sink.',
                'name' => 'Water walk boost',
            ],
            'whirlwind-boost' => [
                'description' => 'Use all your movement to run quickly in a 10’ radius circle. If there is dirt or debris in the circle, you kick it into a whirlwind, blocking sight into, out of, and through the cloud.',
                'name' => 'Whirlwind boost',
            ],
        ],
        'description' => 'You run far faster than any normal human being.',
        'duration' => 'Continuous',
        'effect' => 'At rank 1, your Speed is 60’. At rank 2, your Speed is 90’. You cannot carry anyone with you at these increased speeds.',
        'maxRank' => 2,
        'name' => 'Speedster',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'super-agility' => [
        'activation' => 'NA',
        'boosts' => [
            'balance-boost' => [
                'description' => 'You maintain your balance on anything tightrope size or larger until the beginning of your next turn.',
                'name' => 'Balance boost',
            ],
            'dodge-boost' => [
                'description' => 'Your Body increases by 1 until the beginning of your next turn.',
                'name' => 'Dodge boost',
            ],
            'escape-boost' => [
                'description' => 'Automatically escape a grapple or escape from bonds/handcuffs.',
                'name' => 'Escape boost',
            ],
            'focus-boost' => [
                'description' => 'You can move at full speed while sneaking or balancing until the beginning of your next turn.',
                'name' => 'Focus boost',
            ],
            'precision-boost' => [
                'description' => 'Your Guns and Ranged Weapon attacks deal +2 Hits damage until the beginning of your next turn.',
                'name' => 'Precision boost',
            ],
            'squeeze-boost' => [
                'description' => 'You can squeeze through any space wider than one foot square until the beginning of your next turn.',
                'name' => 'Squeeze boost',
            ],
        ],
        'description' => 'You are far more agile than any normal human.',
        'duration' => 'Continuous',
        'effect' => 'Your Agility must be a 3 before taking this Power. At rank 1, your Agility increases to 4. At rank 2, your Agility increases to 5.',
        'maxRank' => 2,
        'name' => 'Super agility',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'super-charisma' => [
        'activation' => 'NA',
        'boosts' => [
            'damage-prevention-boost' => [
                'description' => 'Until the beginning of your next turn, when you spend Moxie to prevent damage just taken, you prevent two additional points of damage.',
                'name' => 'Damage prevention boost',
            ],
            'exhaustion-boost' => [
                'description' => 'Immediately shrug off any effect that makes you exhausted, tired, or otherwise unfocused mentally.',
                'name' => 'Exhaustion boost',
            ],
            'hits-boost' => [
                'description' => 'Gain 2 Hits.',
                'name' => 'Hits boost',
            ],
            'intimidating-boost' => [
                'description' => 'Automatically intimidate an NPC with a Perception of 2 or less.',
                'name' => 'Intimidating boost',
            ],
            'liar-boost' => [
                'description' => 'All NPCs automatically believe the next lie you tell as long as it is within the bounds of reason.',
                'name' => 'Liar boost',
            ],
            'minion-boost' => [
                'description' => 'Automatically bring an NPC with a Perception of 2 or less under your sway, socially. The NPC will do whatever you ask of them as long as it won’t endanger their life.',
                'name' => 'Minion boost',
            ],
        ],
        'description' => 'You are far more charming and willful than most people.',
        'duration' => 'Continuous',
        'effect' => 'Your Charisma must be a 3 before taking this Power. At rank 1, your Charisma increases to 4. At rank 2, your Charisma increases to 5. You treat Botches on Charisma Checks as normal failures.',
        'maxRank' => 2,
        'name' => 'Super charisma',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'super-expertise' => [
        'activation' => 'NA',
        'boosts' => [
            'aid-boost' => [
                'description' => 'As a Trait Action, you describe how to do something to someone else. That person gains advantage on their next Trait Check to perform that action.',
                'name' => 'Aid boost',
            ],
            'craft-repair-boost' => [
                'description' => 'You craft or repair an item in half the time normally required. You must maintain this Boost while crafting or repairing.',
                'name' => 'Craft/repair boost',
            ],
            'disable-boost' => [
                'description' => 'You disable an item in half the time normally required. You must maintain this Boost while disabling. If disabling the item would have taken one Trait Action, you disable it as a Free Action.',
                'name' => 'Disable boost',
            ],
            'flaw-boost' => [
                'description' => 'After studying something for five minutes, you notice the biggest flaw in the situation or system.',
                'name' => 'Flaw boost',
            ],
            'memory-boost' => [
                'description' => 'You recall information heard or seen previously with great clarity, to the point that you can write down or draw it perfectly. You must maintain this Boost while re-creating the information.',
                'name' => 'Memory boost',
            ],
        ],
        'description' => 'Your intelligence outstrips that of most people.',
        'duration' => 'Continuous',
        'effect' => 'Your Expertise must be a 3 before taking this Power. At rank 1, your Expertise increases to 4. At rank 2, your Expertise increases to 5. Each time you increase your Expertise by 1, you gain a Skill. You treat Botches on Expertise Checks as normal failures.',
        'maxRank' => 2,
        'name' => 'Super expertise',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'super-perception' => [
        'activation' => 'NA',
        'boosts' => [
            'emotion-sense-boost' => [
                'description' => 'Choose one target you can see within 30’. You sense their emotions.',
                'name' => 'Emotion sense boost',
            ],
            'health-sense-boost' => [
                'description' => 'Choose one target you can see within 30’. You know how many Hits the target has remaining.',
                'name' => 'Health sense boost',
            ],
            'lie-detector-boost' => [
                'description' => 'Choose one target you can see within 30’. Until the beginning of your next turn, you always know if the target is lying.',
                'name' => 'Lie detector boost',
            ],
            'nightvision-boost' => [
                'description' => 'You see perfectly in all but total darkness.',
                'name' => 'Nightvision boost',
            ],
            'reactive-boost' => [
                'description' => 'Your Mind increases by 1 until the beginning of your next.',
                'name' => 'Reactive boost',
            ],
            'taster-boost' => [
                'description' => 'You detect the presence of poisons and toxins with but a taste of a substance.',
                'name' => 'Taster boost',
            ],
        ],
        'description' => 'Your perception and common sense outshine others.',
        'duration' => 'Continuous',
        'effect' => 'Your Perception must be a 3 before taking this Power. At rank 1, your Perception increases to 4. At rank 2, your Perception increases to 5. You treat Botches on Perception Checks as normal failures.',
        'maxRank' => 2,
        'name' => 'Super perception',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'super-resilience' => [
        'activation' => 'NA',
        'boosts' => [
            'cure-boost' => [
                'description' => 'Immediately cure yourself of any disease you’ve contracted.',
                'name' => 'Cure boost',
            ],
            'damage-prevention-boost' => [
                'description' => 'Until the beginning of your next turn, when you spend Moxie to prevent damage just taken, you prevent two additional points of damage.',
                'name' => 'Damage prevention boost',
            ],
            'detoxifying-boost' => [
                'description' => 'Immediately shrug off the effects of all toxins, alcohol, and other drugs.',
                'name' => 'Detoxifying boost',
            ],
            'exhaustion-boost' => [
                'description' => 'Immediately shrug off any effect that makes you exhausted, tired, or otherwise unfocused mentally.',
                'name' => 'Exhaustion boost',
            ],
            'hits-boost' => [
                'description' => 'Gain 2 Hits.',
                'name' => 'Hits boost',
            ],
        ],
        'description' => 'You are healthier than the average person.',
        'duration' => 'Continuous',
        'effect' => 'Your Resilience must be a 3 before taking this Power. At rank 1, your Resilience increases to 4. At rank 2, your Resilience increases to 5. You require only four hours of sleep per night. You heal Hits at twice the normal rate.',
        'maxRank' => 2,
        'name' => 'Super resilience',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
    'super-strength' => [
        'activation' => 'NA',
        'boosts' => [
            'damage-boost' => [
                'description' => 'Your Fisticuffs and Melee Weapon (including thrown weapon) attacks deal +2 Hits damage until the beginning of your next turn.',
                'name' => 'Damage boost',
            ],
            'jump-boost' => [
                'description' => 'Jump up to 30’ as part of your movement.',
                'name' => 'Jump boost',
            ],
            'knockdown-boost' => [
                'description' => 'If you hit the target, you also knock it prone.',
                'name' => 'Knockdown boost',
            ],
            'penetration-boost' => [
                'description' => 'You can punch or throw through up to 8” of concrete or a thin steel plate to hit something on the other side.',
                'name' => 'Penetration boost',
            ],
            'push-boost' => [
                'description' => 'If you hit the target, you push it 10’.',
                'name' => 'Push boost',
            ],
            'range-boost' => [
                'description' => 'Ranges for your thrown weapons double until the beginning of your next turn.',
                'name' => 'Range boost',
            ],
        ],
        'description' => 'You are far stronger than any normal human.',
        'duration' => 'Continuous',
        'effect' => 'Your Strength must be a 3 before taking this Power. At rank 1, your Strength increases to 4. At rank 2, your Strength increases to 5. Your Fisticuffs and Melee Weapon (including thrown weapon) attacks deal the better of Suit Hits damage or their normal damage.',
        'maxRank' => 2,
        'name' => 'Super strength',
        'range' => 'NA',
        'target' => 'Self',
        'type' => 'Major',
    ],
];
