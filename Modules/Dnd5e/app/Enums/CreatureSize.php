<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Enums;

enum CreatureSize: string
{
    case Tiny = 'tiny';
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';
    case Huge = 'huge';
    case Gargantuan = 'gargantuan';

    /**
     * Get a side of the square amount of space a character of this size
     * occupies.
     */
    public function space(): float
    {
        return match ($this) {
            self::Tiny => 2.5,
            self::Small, self::Medium => 5.0,
            self::Large => 10.0,
            self::Huge => 15.0,
            self::Gargantuan => 20.0,
        };
    }
}
