<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Models;

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
