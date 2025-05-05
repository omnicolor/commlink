<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Enums;

enum FiringMode: string
{
    case SingleShot = 'SS';
    case SemiAutomatic = 'SA';
    case BurstFire = 'BF';
    case FullAuto = 'FA';

    public function name(): string
    {
        return match ($this) {
            self::SingleShot => 'Single-shot',
            self::SemiAutomatic => 'Semi-automatic',
            self::BurstFire => 'Burst fire',
            self::FullAuto => 'Full auto',
        };
    }
}
