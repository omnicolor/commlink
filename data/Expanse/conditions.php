<?php

declare(strict_types=1);

/**
 * Expanse RPG conditions.
 */
return [
    'deafened' => [
        'description' => 'The character cannot hear and automatically fails ability tests reliant on hearing, such as Perception (Hearing).',
        'name' => 'Deafened',
        'page' => 21,
    ],
    'dying' => [
        'description' => 'The character is in danger of perishing. A dying character loses 1 point of Constitution score each round on the start of the character’s turn. When the character’s Constitution score reaches –3, the character dies. Successful first aid applied to a dying character stabilizes their condition, making them helpless, unconscious, and wounded instead. They must recover from these conditions normally (see Interludes in Chapter 5).',
        'name' => 'Dying',
        'page' => 21,
    ],
    'exhausted' => [
        'description' => 'The character is severely fatigued. The character’s Speed is halved and they cannot take the Charge or Run actions. An exhausted character who receives an additional fatigued or exhausted condition becomes helpless.',
        'name' => 'Exhausted',
        'page' => 21,
    ],
    'fatigued' => [
        'description' => 'The character is tired and cannot take the Charge or Run actions. A fatigued character who receives an additional fatigued condition becomes exhausted.',
        'name' => 'Fatigued',
        'page' => 22,
    ],
    'free-falling' => [
        'description' => 'The character is effectively weightless in a microgravity or free-fall environment. The character can only move with access to hand-holds, a surface to push off from, or some type of thrust (like from a thruster pack) and, once moving, continues to move with the same speed and trajectory unless acted upon to stop or change their movement.',
        'name' => 'Free Falling',
        'page' => 22,
    ],
    'helpless' => [
        'description' => 'The character is incapable of doing anything. The character cannot take any actions.',
        'name' => 'Helpless',
        'page' => 22,
    ],
    'hindered' => [
        'description' => 'The character’s Speed is halved (round down) and they cannot take the Charge or Run actions.',
        'name' => 'Hindered',
        'page' => 22,
    ],
    'injured' => [
        'description' => 'The character is hurt. The character has a –1 penalty to all tests and is fatigued, unable to take the Charge or Run actions. An injured character who receives an additional injured condition becomes wounded.',
        'name' => 'Injured',
        'page' => 22,
    ],
    'prone' => [
        'description' => 'The character is lying on the ground. The character cannot take the Charge or Run actions, as they can only move by crawling, and standing up from prone requires a Move action using half the character’s Speed. Melee attacks have a +1 bonus against prone characters, while ranged attacks have a –1 penalty.',
        'name' => 'Prone',
        'page' => 22,
    ],
    'restrained' => [
        'description' => 'The character’s Speed becomes 0 and they effectively cannot move. A restrained condition may prevent a character from taking certain other actions as well, defined by the nature of the restraint.',
        'name' => 'Restrained',
        'page' => 22,
    ],
    'unconscious' => [
        'description' => 'The character is unaware of their surroundings or the passage of time. The character falls prone and is helpless, unable to take any actions.',
        'name' => 'Unconscious',
        'page' => 22,
    ],
    'wounded' => [
        'description' => 'The character is severely injured. The character has a –2 penalty to all tests and is exhausted, their Speed halved and unable to take the Charge or Run actions. A wounded character who receives an additional injured or wounded condition becomes dying.',
        'name' => 'Wounded',
        'page' => 22,
    ],
];
