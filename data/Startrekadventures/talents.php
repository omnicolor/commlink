<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'description' => '',
        'incompatible-with' => [],
        'name' => '',
        'page' => ,
        'requirements' => [],
        'ruleset' => '',
    ],
    */
    'bold-command' => [
        'description' => 'You must choose a single Discipline when you select '
            . 'this Talent. Whenever you attempt a Task with that Discipline, '
            . 'and you buy one or more d20s by adding to Threat, you may '
            . 're-roll a single d20. You may select this Talent multiple '
            . 'times, once for each Discipline. You may not select this Talent '
            . 'for any Discipline for which you already have the Cautious '
            . 'Talent.',
        'incompatible-with' => [
            'bold-command',
            'cautious-command',
        ],
        'name' => 'Bold - Command',
        'page' => 135,
        'ruleset' => 'core',
    ],
    'cautious-command' => [
        'description' => 'You must choose a single Discipline when you select '
            . 'this Talent. Whenever you attempt a Task with that Discipline, '
            . 'and you buy one or more d20s by spending Momentum, you may '
            . 're-roll a single d20. You may select this Talent multiple '
            . 'times, once for each Discipline. You may not select this Talent '
            . 'for any Discipline for which you already have the Bold Talent.',
        'incompatible-with' => [
            'bold-command',
            'cautious-command',
        ],
        'name' => 'Cautious - Command',
        'page' => 136,
        'ruleset' => 'core',
    ],
];
