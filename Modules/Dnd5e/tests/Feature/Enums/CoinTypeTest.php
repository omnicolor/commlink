<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\Enums;

use Modules\Dnd5e\Enums\CoinType;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('dnd5e')]
#[Small]
final class CoinTypeTest extends TestCase
{
    public function testConvertCopper(): void
    {
        self::assertSame(
            1.0,
            CoinType::convert(1000, CoinType::Copper, CoinType::Platinum),
        );
        self::assertSame(
            10.0,
            CoinType::convert(1000, CoinType::Copper, CoinType::Gold),
        );
        self::assertSame(
            20.0,
            CoinType::convert(1000, CoinType::Copper, CoinType::Electrum),
        );
        self::assertSame(
            100.0,
            CoinType::convert(1000, CoinType::Copper, CoinType::Silver),
        );
        self::assertSame(
            1000.0,
            CoinType::convert(1000, CoinType::Copper, CoinType::Copper),
        );
    }

    public function testConvertSilver(): void
    {
        self::assertSame(
            10.0,
            CoinType::convert(1000, CoinType::Silver, CoinType::Platinum),
        );
        self::assertSame(
            100.0,
            CoinType::convert(1000, CoinType::Silver, CoinType::Gold),
        );
        self::assertSame(
            200.0,
            CoinType::convert(1000, CoinType::Silver, CoinType::Electrum),
        );
        self::assertSame(
            1000.0,
            CoinType::convert(1000, CoinType::Silver, CoinType::Silver),
        );
        self::assertSame(
            10000.0,
            CoinType::convert(1000, CoinType::Silver, CoinType::Copper),
        );
    }

    public function testConvertElectrum(): void
    {
        self::assertSame(
            50.0,
            CoinType::convert(1000, CoinType::Electrum, CoinType::Platinum),
        );
        self::assertSame(
            500.0,
            CoinType::convert(1000, CoinType::Electrum, CoinType::Gold),
        );
        self::assertSame(
            1000.0,
            CoinType::convert(1000, CoinType::Electrum, CoinType::Electrum),
        );
        self::assertSame(
            5000.0,
            CoinType::convert(1000, CoinType::Electrum, CoinType::Silver),
        );
        self::assertSame(
            50000.0,
            CoinType::convert(1000, CoinType::Electrum, CoinType::Copper),
        );
    }

    public function testConvertGold(): void
    {
        self::assertSame(
            100.0,
            CoinType::convert(1000, CoinType::Gold, CoinType::Platinum),
        );
        self::assertSame(
            1000.0,
            CoinType::convert(1000, CoinType::Gold, CoinType::Gold),
        );
        self::assertSame(
            2000.0,
            CoinType::convert(1000, CoinType::Gold, CoinType::Electrum),
        );
        self::assertSame(
            10000.0,
            CoinType::convert(1000, CoinType::Gold, CoinType::Silver),
        );
        self::assertSame(
            100000.0,
            CoinType::convert(1000, CoinType::Gold, CoinType::Copper),
        );
    }

    public function testConvertPlatinum(): void
    {
        self::assertSame(
            1000.0,
            CoinType::convert(1000, CoinType::Platinum, CoinType::Platinum),
        );
        self::assertSame(
            10000.0,
            CoinType::convert(1000, CoinType::Platinum, CoinType::Gold),
        );
        self::assertSame(
            20000.0,
            CoinType::convert(1000, CoinType::Platinum, CoinType::Electrum),
        );
        self::assertSame(
            100000.0,
            CoinType::convert(1000, CoinType::Platinum, CoinType::Silver),
        );
        self::assertSame(
            1000000.0,
            CoinType::convert(1000, CoinType::Platinum, CoinType::Copper),
        );
    }
}
