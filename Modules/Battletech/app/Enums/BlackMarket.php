<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

use RuntimeException;

use function round;

enum BlackMarket
{
    case Normal;
    case ClanBorder;
    case HouseBorder;
    case PeripheryBorder;
    case MajorWorld;
    case Shattered;

    public static function costMultiplier(
        self $market,
        LegalityRating $legality,
        AvailabilityRating $availability,
    ): float {
        if (AvailabilityRating::NonExistent === $availability) {
            throw new RuntimeException('Item is not available');
        }
        $multiplier = match ($market) {
            self::Normal => 1.00,
            self::ClanBorder => 1.10,
            self::HouseBorder => 0.97,
            self::PeripheryBorder => 1.05,
            self::MajorWorld => 0.98,
            self::Shattered => 2.00,
        };

        $multiplier *= match ($legality) {
            LegalityRating::Unrestricted => match ($availability) {
                AvailabilityRating::VeryCommon => 0.50,
                AvailabilityRating::Common => 1.00,
                AvailabilityRating::Uncommon => 1.25,
                AvailabilityRating::Rare => 1.50,
                AvailabilityRating::VeryRare => 2.00,
                AvailabilityRating::Unique => 4.00,
            },
            LegalityRating::Monitored => match ($availability) {
                AvailabilityRating::VeryCommon => 1.00,
                AvailabilityRating::Common => 2.00,
                AvailabilityRating::Uncommon => 2.50,
                AvailabilityRating::Rare => 2.00,
                AvailabilityRating::VeryRare => 3.00,
                AvailabilityRating::Unique => 6.00,
            },
            LegalityRating::Licensed => match ($availability) {
                AvailabilityRating::VeryCommon => 2.00,
                AvailabilityRating::Common => 3.00,
                AvailabilityRating::Uncommon => 4.00,
                AvailabilityRating::Rare => 3.00,
                AvailabilityRating::VeryRare => 4.00,
                AvailabilityRating::Unique => 9.00,
            },
            LegalityRating::Controlled => match ($availability) {
                AvailabilityRating::VeryCommon => 3.00,
                AvailabilityRating::Common => 4.00,
                AvailabilityRating::Uncommon => 5.00,
                AvailabilityRating::Rare => 6.00,
                AvailabilityRating::VeryRare => 8.00,
                AvailabilityRating::Unique => 14.00,
            },
            LegalityRating::Restricted => match ($availability) {
                AvailabilityRating::VeryCommon => 5.00,
                AvailabilityRating::Common => 6.00,
                AvailabilityRating::Uncommon => 7.00,
                AvailabilityRating::Rare => 10.00,
                AvailabilityRating::VeryRare => 15.00,
                AvailabilityRating::Unique => 21.00,
            },
            LegalityRating::HighlyRestricted => match ($availability) {
                AvailabilityRating::VeryCommon => 7.00,
                AvailabilityRating::Common => 9.00,
                AvailabilityRating::Uncommon => 11.00,
                AvailabilityRating::Rare => 13.00,
                AvailabilityRating::VeryRare => 20.00,
                AvailabilityRating::Unique => 30.00,
            },
        };

        return round($multiplier, 2);
    }
}
