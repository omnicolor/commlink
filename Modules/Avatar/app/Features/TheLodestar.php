<?php

declare(strict_types=1);

namespace Modules\Avatar\Features;

use Override;
use Stringable;

use function sprintf;

class TheLodestar extends Feature implements Stringable
{
    public readonly string $description;
    public readonly string $lodestar;

    /**
     * @param array{lodestar?: string} $options
     */
    public function __construct(array $options)
    {
        $this->lodestar = $options['lodestar'] ?? 'Unknown';
        $this->description = <<<'DESC'
            There’s only one person you often let past your emotional walls.

            **Name your lodestar:** %s

            You can shift your lodestar to someone new when they **guide and
            comfort** you and you open up to them, or when you **guide and
            comfort** them and they open up to you. If you do choose to shift
            your lodestar, clear a condition.

            When you **shut down someone vulnerable to harsh words or icy
            silence,** shift your balance toward Results and roll with Results.
            On a hit, they mark a condition and you may clear the same
            condition. On a 10+, they also cannot shift your balance or **call
            you out** for the rest of the scene. On a miss, they have exactly
            the right retort; mark a condition and they shift your balance. You
            cannot use this on your lodestar.

            When your lodestar **shifts your balance** or **calls you out**,
            you cannot resist it. Treat an NPC lodestar calling you out as if
            you rolled a 10+, and a PC lodestar calling you out as if they
            rolled a 10+.

            When you **consult your lodestar for advice on a problem** (or
            permission to use your preferred solution), roll with Restraint. On
            a 10+ take all three; on a 7–9 they choose two:
            - You see the wisdom of their advice. They shift your balance;
            follow their advice and they shift your balance again.
            - The conversation bolsters you. Clear a condition or 2-fatigue.
            - They feel at ease offering their opinion. They clear a condition
            or 2-fatigue.

            On a miss, something about their advice infuriates you. Mark a
            condition or have the GM shift your balance twice.
            DESC;
    }

    #[Override]
    public function __toString(): string
    {
        return 'The Lodestar';
    }

    public function description(): string
    {
        return sprintf($this->description, $this->lodestar);
    }
}
