<?php

declare(strict_types=1);

namespace Modules\Avatar\Enums;

enum TechniqueClass: string
{
    case AdvanceAndAttack = 'advance';
    case DefendAndManeuver = 'defend';
    case EvadeAndObserve = 'evade';

    public function name(): string
    {
        return match ($this) {
            TechniqueClass::AdvanceAndAttack => 'Advance and Attack',
            TechniqueClass::DefendAndManeuver => 'Defend and Maneuver',
            TechniqueClass::EvadeAndObserve => 'Evade and Observe',
        };
    }
}
