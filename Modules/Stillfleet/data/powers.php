<?php

declare(strict_types=1);

use Modules\Stillfleet\Models\Power;

return [
    /*
    '' => [
        'advanced-list' => '',
        'description' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'ruleset' => 'core',
        'type' => Power::,
    ],
     */
    'ally' => [
        'advanced-list' => 'communications',
        'description' => 'You can convert code-based intelligences to your cause. Burn 4 GRT and roll a REA and a CHA check. The difficulty of the REA check is 1 + the complexity level of the code-based intelligence (generally at least a 3). The CHA check is a standard difficulty of 6. If both checks are successful, you convince the code-based intelligence to ally with you and accomplish some goal. This is a permanent alliance, unless you double-cross the AI. If one check succeeds, the AI is not angry, simply unconvinced—perhaps curious about the terms of the alliance. If both checks fail, you have angered the AI and should probably flee…',
        'id' => 'ally',
        'name' => 'Ally',
        'page' => 97,
        'ruleset' => 'core',
        'type' => Power::TYPE_ADVANCED,
    ],
    'astrogate' => [
        'description' => 'You can, with effort, actually pilot spacefaring vessels. Cost to command a vessel is based purely on its class (a combined 1–6 rating based on its size, complexity, and condition), with each point of vessel class costing 6 GRT. Burning this GRT grants you the ability to pilot or otherwise command the ship, within reason. (If the hyperdrive’s broke, it ain’t jumpin; mining vessels may not sport armament, etc.) While piloting, you are essentially stuck and unable to otherwise venture. You must pay the astrogation cost each time you chart a course into or out of a system, or each time the ship is damaged or upgraded. Doing anything with the ship other than moving from point A to B (firing weapons, scanning a mysterious craft, remotely salvaging damaged life support systems, etc.) requires relevant checks, typically REA or, in a dogfight, COM and MOV',
        'id' => 'astrogate',
        'name' => 'Astrogate',
        'page' => 45,
        'ruleset' => 'core',
        'type' => Power::TYPE_CLASS,
    ],
    'control-hell-science' => [
        'description' => 'Hell science description.',
        'id' => 'control-hell-science',
        'name' => 'Control hell science',
        'page' => 65,
        'ruleset' => 'core',
        'type' => Power::TYPE_MARQUEE,
    ],
    'dive' => [
        'description' => 'The ship is on fire—you have to get your party through the stuck bulkhead before the O2 combusts! (Or whatever!) This is your big getaway power. Burn 6 GRT and make a MOV check: if you succeed, you and some or all of your party make it out safely. You can bring 1 +your level humanoid-sized beings along with you to safety, or burn an additional 6 GRT to bring one additional being beyond your limit. Small sapient beings (0.5-M tall/long) count for half a slot; large sapes (3-M tall/long) count double; small pets (cat/beagle-sized) are free. If you fail your MOV check, you alone get away but injure yourself for 2d6 HEA damage.',
        'id' => 'dive',
        'name' => 'Dive',
        'page' => 45,
        'ruleset' => 'core',
        'type' => Power::TYPE_CLASS,
    ],
    'interface' => [
        'description' => 'Burn d10 GRT to tap into any comm system (any code-stratum archaetech), or to communicate with any AI or otherwise informatic being. Using this power requires no roll, and it obviates the need to spend copious GRT on jack.',
        'id' => 'interface',
        'name' => 'Interface',
        'page' => 45,
        'ruleset' => 'core',
        'type' => Power::TYPE_CLASS,
    ],
    'jack' => [
        'description' => 'Use, alter, or repair tech. Burn XY GRT, where X is the stratum (type) of tech (ranked 1–7, clank [medieval], bio [contemporary on-Spin], code [Ancient, Early Tephnian], force [High Tephnian], nano [Late Tephnian, Snakeman], bug [cryptocerid, heechee, mantid, mi-go], Escheresque) and Y is the level of complexity (ranked 1–6, simple, standard, complex, corporate, military, godlike). Thus, a force artifact of standard complexity costs 4 × 2 = 8 GRT to interact with. Once this cost has been paid, the artifact functions as designed, within reason. A laser (standard force) keeps firing until it runs out of juice, which is up to the GM to determine (e.g., d20 charges). Repowering requires access to the appropriate resources and an additional use of this power. See “Wires” for more on tech.',
        'id' => 'jack',
        'name' => 'Jack',
        'page' => 45,
        'ruleset' => 'core',
        'type' => Power::TYPE_CLASS,
    ],
    'power-up' => [
        'description' => 'You use tools to recharge a technical object or being. Burn X GRT; a technical entity gains X GRT. This GRT can stack beyond the entity’s normal limit. You can also use this power to add charges to energy weapons. The cost to do so depends on their type: burn 2X GRT to add X charges to a personal weapon; 3X GRT to add X charges to an Ancient or High Tephnian naval weapon; or 5X GRT to add X charges to an alien or Late Tephnian naval weapon.',
        'id' => 'power-up',
        'name' => 'Power up',
        'page' => 45,
        'ruleset' => 'core',
        'type' => Power::TYPE_CLASS,
    ],
    'reposition' => [
        'description' => 'You expertly case the chaos around you. Burn 6 GRT: you gain an extra d12 to use on any roll, going forward. This can be a dodge check, an attack, a damage roll— anything. You can use this d12 once, and it will go away; or you can instead use a d6 and, later, another d6; or a d4 and a d8; or three d4s, etc. The bonus rolls are entirely up to you. You can reposition, use the extra die, and immediately reposition again (paying another 6 GRT), even in the same round (if you are trying to dodge multiple lethal attacks, for example). But you can never hold more than one d12-worth of bonus dice at once.',
        'id' => 'reposition',
        'name' => 'Reposition',
        'page' => 45,
        'ruleset' => 'core',
        'type' => Power::TYPE_CLASS,
    ],
    'tack' => [
        'description' => 'Open, close, or change the destination of a stiffworks. Costs and mechanics vary by the works. Open stiffworks remain open for 10 minutes per tack… usually. Some stiffworks may be retacked (i.e., their destinations changed), although this varies by type.',
        'id' => 'tack',
        'name' => 'Tack',
        'page' => 43,
        'ruleset' => 'core',
        'type' => Power::TYPE_MARQUEE,
    ],
];
