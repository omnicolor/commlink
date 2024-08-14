<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

/**
 * @codeCoverageIgnore
 */
enum Background: string
{
    case Military = 'military';
    case Monastic = 'monastic';
    case Outlaw = 'outlaw';
    case Privileged = 'privileged';
    case Urban = 'urban';
    case Wilderness = 'wilderness';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function description(): string
    {
        return match ($this) {
            Background::Military => 'You trained to fight as a soldier in a '
                . 'military unit such as a mercenary company, a regional '
                . 'militia, or a state government’s standing army. Are you a '
                . 'soldier, sailor, or spy? Do you still answer to your '
                . 'commanding officer, or have you gone rogue?',
            Background::Monastic => 'monastic',
            Background::Outlaw => 'outlaw',
            Background::Privileged => 'privileged',
            Background::Urban => 'urban',
            Background::Wilderness => 'wilderness',
        };
    }
}
