<?php

declare(strict_types=1);

namespace App\Models\Dnd5e;

/**
 * @psalm-suppress all
 */
enum DamageType: string
{
    case Bludgeoning = 'bludgeoning';

    public function description(): string
    {
        return match ($this) {
            DamageType::Bludgeoning => 'Blunt force attacks—hammers, falling, '
                . 'constriction, and the like—deal bludgeoning damage.',
        };
    }
}
