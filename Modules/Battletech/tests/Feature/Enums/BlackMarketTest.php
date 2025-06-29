<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Enums;

use Modules\Battletech\Enums\AvailabilityRating;
use Modules\Battletech\Enums\BlackMarket;
use Modules\Battletech\Enums\LegalityRating;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Group('battletech')]
#[Small]
final class BlackMarketTest extends TestCase
{
    /**
     * @return array<int, array<int, float|BlackMarket|LegalityRating|AvailabilityRating>>
     */
    public static function costMultiplierProvider(): array
    {
        return [
            [BlackMarket::Normal, LegalityRating::Unrestricted, AvailabilityRating::VeryCommon, 0.50],
            [BlackMarket::Normal, LegalityRating::Monitored, AvailabilityRating::VeryCommon, 1.00],
            [BlackMarket::Normal, LegalityRating::Licensed, AvailabilityRating::VeryCommon, 2.00],
            [BlackMarket::Normal, LegalityRating::Controlled, AvailabilityRating::VeryCommon, 3.00],
            [BlackMarket::Normal, LegalityRating::Restricted, AvailabilityRating::VeryCommon, 5.00],
            [BlackMarket::Normal, LegalityRating::HighlyRestricted, AvailabilityRating::VeryCommon, 7.00],

            [BlackMarket::Normal, LegalityRating::Unrestricted, AvailabilityRating::Common, 1.00],
            [BlackMarket::Normal, LegalityRating::Monitored, AvailabilityRating::Common, 2.00],
            [BlackMarket::Normal, LegalityRating::Licensed, AvailabilityRating::Common, 3.00],
            [BlackMarket::Normal, LegalityRating::Controlled, AvailabilityRating::Common, 4.00],
            [BlackMarket::Normal, LegalityRating::Restricted, AvailabilityRating::Common, 6.00],
            [BlackMarket::Normal, LegalityRating::HighlyRestricted, AvailabilityRating::Common, 9.00],

            [BlackMarket::Normal, LegalityRating::Unrestricted, AvailabilityRating::Uncommon, 1.25],
            [BlackMarket::Normal, LegalityRating::Monitored, AvailabilityRating::Uncommon, 2.50],
            [BlackMarket::Normal, LegalityRating::Licensed, AvailabilityRating::Uncommon, 4.00],
            [BlackMarket::Normal, LegalityRating::Controlled, AvailabilityRating::Uncommon, 5.00],
            [BlackMarket::Normal, LegalityRating::Restricted, AvailabilityRating::Uncommon, 7.00],
            [BlackMarket::Normal, LegalityRating::HighlyRestricted, AvailabilityRating::Uncommon, 11.00],

            [BlackMarket::Normal, LegalityRating::Unrestricted, AvailabilityRating::Rare, 1.50],
            [BlackMarket::Normal, LegalityRating::Monitored, AvailabilityRating::Rare, 2.00],
            [BlackMarket::Normal, LegalityRating::Licensed, AvailabilityRating::Rare, 3.00],
            [BlackMarket::Normal, LegalityRating::Controlled, AvailabilityRating::Rare, 6.00],
            [BlackMarket::Normal, LegalityRating::Restricted, AvailabilityRating::Rare, 10.00],
            [BlackMarket::Normal, LegalityRating::HighlyRestricted, AvailabilityRating::Rare, 13.00],

            [BlackMarket::Normal, LegalityRating::Unrestricted, AvailabilityRating::VeryRare, 2.00],
            [BlackMarket::Normal, LegalityRating::Monitored, AvailabilityRating::VeryRare, 3.00],
            [BlackMarket::Normal, LegalityRating::Licensed, AvailabilityRating::VeryRare, 4.00],
            [BlackMarket::Normal, LegalityRating::Controlled, AvailabilityRating::VeryRare, 8.00],
            [BlackMarket::Normal, LegalityRating::Restricted, AvailabilityRating::VeryRare, 15.00],
            [BlackMarket::Normal, LegalityRating::HighlyRestricted, AvailabilityRating::VeryRare, 20.00],

            [BlackMarket::Normal, LegalityRating::Unrestricted, AvailabilityRating::Unique, 4.00],
            [BlackMarket::Normal, LegalityRating::Monitored, AvailabilityRating::Unique, 6.00],
            [BlackMarket::Normal, LegalityRating::Licensed, AvailabilityRating::Unique, 9.00],
            [BlackMarket::Normal, LegalityRating::Controlled, AvailabilityRating::Unique, 14.00],
            [BlackMarket::Normal, LegalityRating::Restricted, AvailabilityRating::Unique, 21.00],
            [BlackMarket::Normal, LegalityRating::HighlyRestricted, AvailabilityRating::Unique, 30.00],

            [BlackMarket::ClanBorder, LegalityRating::Unrestricted, AvailabilityRating::Common, 1.10],
            [BlackMarket::HouseBorder, LegalityRating::Unrestricted, AvailabilityRating::Common, 0.97],
            [BlackMarket::PeripheryBorder, LegalityRating::Unrestricted, AvailabilityRating::Common, 1.05],
            [BlackMarket::MajorWorld, LegalityRating::Unrestricted, AvailabilityRating::Common, 0.98],
            [BlackMarket::Shattered, LegalityRating::Unrestricted, AvailabilityRating::Common, 2.00],

            [BlackMarket::Shattered, LegalityRating::HighlyRestricted, AvailabilityRating::Unique, 60.00],
            [BlackMarket::HouseBorder, LegalityRating::Unrestricted, AvailabilityRating::VeryCommon, 0.49],
        ];
    }

    #[DataProvider('costMultiplierProvider')]
    public function testCostMultiplier(
        BlackMarket $market,
        LegalityRating $legality,
        AvailabilityRating $availability,
        float $expected,
    ): void {
        self::assertSame(
            $expected,
            BlackMarket::costMultiplier($market, $legality, $availability),
        );
    }

    /**
     * @return array<int, array<int, BlackMarket|LegalityRating>>
     */
    public static function nonExistentProvider(): array
    {
        return [
            [BlackMarket::Normal, LegalityRating::Unrestricted],
            [BlackMarket::Normal, LegalityRating::Monitored],
            [BlackMarket::Normal, LegalityRating::Licensed],
            [BlackMarket::Normal, LegalityRating::Controlled],
            [BlackMarket::Normal, LegalityRating::Restricted],
            [BlackMarket::Normal, LegalityRating::HighlyRestricted],
        ];
    }

    #[DataProvider('nonExistentProvider')]
    public function testCostMultiplierForNonExistentItem(
        BlackMarket $market,
        LegalityRating $legality,
    ): void {
        self::expectException(RuntimeException::class);
        BlackMarket::costMultiplier(
            $market,
            $legality,
            AvailabilityRating::NonExistent,
        );
    }
}
