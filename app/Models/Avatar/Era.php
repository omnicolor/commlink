<?php

declare(strict_types=1);

namespace App\Models\Avatar;

enum Era: string
{
    case Aang = 'aang';
    case HundredYearWar = 'hundred-year-war';
    case Korra = 'korra';
    case Kyoshi = 'kyoshi';
    case Roku = 'roku';

    /**
     * Return the values for the enumeratioin.
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
