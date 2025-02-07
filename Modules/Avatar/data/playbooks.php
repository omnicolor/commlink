<?php

declare(strict_types=1);

use Modules\Avatar\Features\TheLodestar;

/**
 * @return array<int, array{
 *   creativity: int,
 *   focus: int,
 *   harmony: int,
 *   passion: int,
 *   advanced_technique: string,
 *   balance_left: string,
 *   balance_right: string,
 *   connections: array<int, string>,
 *   demeanor_options: array<int, string>,
 *   description: string,
 *   feature: class-string,
 *   growth_question: string,
 *   history: array<int, string>,
 *   moment_of_balance: string,
 *   moves: array<int, string>,
 *   name: string,
 *   page: int,
 *   ruleset: string
 * }>
 */
return [
    /*
    '' => [
        'creativity' => ,
        'focus' => ,
        'harmony' => ,
        'passion' => ,
        'advanced_technique' => '',
        'balance_left' => '',
        'balance_right' => '',
        // Use %s where you want each connection to be added to the string.
        'connections' => [
            '',
            '',
        ],
        'demeanor_options' => [
            '',
        ],
        'description' => '',
        'feature' => Feature::class,
        'growth_question' => '',
        'history' => [
            '',
        ],
        'moment_of_balance' => '',
        'moves' => [
            '',
            '',
            '',
            '',
            '',
        ],
        'name' => '',
        'page' => ,
        'ruleset' => 'core',
    ],
     */
    'the-adamant' => [
        'creativity' => 0,
        'focus' => 1,
        'harmony' => -1,
        'passion' => 1,
        'advanced_technique' => 'pinpoint-aim',
        'balance_left' => 'restraint',
        'balance_right' => 'results',
        'connections' => [
            '%s takes issue with my methods—perhaps they have a point, but I certainly can’t admit that to them!',
            '%s is my lodestar; something about them makes them the one person I let my guard down around.',
        ],
        'demeanor_options' => [
            'above-it-all',
            'perfectionist',
            'chilly',
            'rebellious',
            'flippant',
            'standoffish',
        ],
        'description' => 'A zealous advocate with a heart of gold and a diamond-hard will, ready to do what it takes to fix the world. Their balance principles are Restraint vs Results.',
        'feature' => TheLodestar::class,
        'growth_question' => 'Did you seek support or guidance from others?',
        'history' => [
            'What experience of being deceived or manipulated convinced you to steel yourself against being swayed by other people?',
            'Who was your first lodestar, and why were they an exception? Why aren’t they your lodestar anymore?',
            'Who earned your grudging respect by teaching you pragmatism?',
            'What heirloom or piece of craftsmanship do you carry to remind you to stay true to yourself?',
            'Why are you committed to this group or purpose?',
        ],
        'moment_of_balance' => 'You’ve held true to a core of conviction even while getting your hands dirty to do what you deemed necessary. But balance means appreciating that other people are just as complex as you are, not merely obstacles or pawns. Tell the GM how you solve an intractable problem or calm a terrible conflict by relating to dangerous people on a human level.',
        'moves' => [
            'this-was-a-victory',
            'takes-one-to-know-one',
            'no-time-for-feelings',
            'i-dont-hate-you',
            'driven-by-justice',
        ],
        'name' => 'The Adamant',
        'page' => 166,
        'ruleset' => 'core',
    ],
];
