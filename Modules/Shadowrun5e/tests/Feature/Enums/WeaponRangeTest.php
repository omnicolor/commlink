<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Enums;

use Modules\Shadowrun5e\Enums\WeaponRange;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RangeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class WeaponRangeTest extends TestCase
{
    public function testRange(): void
    {
        $range = WeaponRange::Cannon;
        self::assertSame('50/300/750/1200', $range->range());
    }

    public function testModifierForRange(): void
    {
        $range = WeaponRange::Cannon;
        self::assertSame(0, $range->modifierForRange(0, 1));
        self::assertSame(-1, $range->modifierForRange(51, 1));
        self::assertSame(-3, $range->modifierForRange(309, 1));
        self::assertSame(-6, $range->modifierForRange(1200, 1));
    }

    public function testModifierOutOfRange(): void
    {
        $range = WeaponRange::Cannon;
        self::expectException(RangeException::class);
        $range->modifierForRange(1201, 1);
    }

    public function testModifierForRangeWithMinimum(): void
    {
        $range = WeaponRange::GrenadeLauncher;
        self::expectException(RangeException::class);
        $range->modifierForRange(1, 1);
    }

    public function testModifierForStrengthBasedRange(): void
    {
        $range = WeaponRange::Bow;
        self::assertSame(0, $range->modifierForRange(1, 1));
        self::assertSame(-1, $range->modifierForRange(9, 1));
        self::assertSame(-3, $range->modifierForRange(29, 1));
        self::assertSame(-6, $range->modifierForRange(59, 1));

        self::assertSame(0, $range->modifierForRange(1, 2));
        self::assertSame(-1, $range->modifierForRange(19, 2));
        self::assertSame(-3, $range->modifierForRange(59, 2));
        self::assertSame(-6, $range->modifierForRange(119, 2));
    }

    public function testOutOfRangeForStrengthBasedRange(): void
    {
        $range = WeaponRange::Bow;
        self::expectException(RangeException::class);
        $range->modifierForRange(721, 12);
    }
}
