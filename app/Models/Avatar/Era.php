<?php

// phpcs:ignoreFile

declare(strict_types=1);

namespace App\Models\Avatar;

enum Era: string
{
    case Aang = 'aang';
    case HundredYearWar = 'hundred-year-war';
    case Korra = 'korra';
    case Kyoshi = 'kyoshi';
    case Roku = 'roku';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
