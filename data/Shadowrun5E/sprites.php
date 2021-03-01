<?php

/**
 * List of sprites.
 */

declare(strict_types=1);

return [
    /*
    '' => [
        'attack' => 'L',
        'data-processing' => 'L',
        'description' => '',
        'firewall' => 'L',
        'id' => '',
        'initiative' => 'L',
        'name' => '',
        'page' => ,
        'powers' => [
        ],
        'resonance' => 'L',
        'ruleset' => '',
        'skills' => [
        ],
        'sleaze' => 'L',
    ],
     */
    'courier' => [
        'attack' => 'L',
        'data-processing' => 'L+1',
        'description' => 'Description goes here.',
        'firewall' => 'L+2',
        'id' => 'courier',
        'initiative' => '(L*2)+1',
        'name' => 'Courier',
        'page' => 258,
        'powers' => ['cookie', 'hash'],
        'resonance' => 'L',
        'ruleset' => 'core',
        'skills' => ['computer', 'hacking'],
        'sleaze' => 'L+3',
    ],
];
