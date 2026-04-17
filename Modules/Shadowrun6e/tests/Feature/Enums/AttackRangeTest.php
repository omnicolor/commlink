<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Enums;

use Iterator;
use Modules\Shadowrun6e\Enums\AttackRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

use const PHP_INT_MAX;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class AttackRangeTest extends TestCase
{
    /**
     * @return Iterator<int, array<int, (int | AttackRange)>>
     */
    public static function rangeProvider(): Iterator
    {
        yield [0, AttackRange::Close];
        yield [1, AttackRange::Close];
        yield [3, AttackRange::Close];
        yield [4, AttackRange::Near];
        yield [50, AttackRange::Near];
        yield [51, AttackRange::Medium];
        yield [250, AttackRange::Medium];
        yield [251, AttackRange::Far];
        yield [500, AttackRange::Far];
        yield [501, AttackRange::Extreme];
        yield [PHP_INT_MAX, AttackRange::Extreme];
    }

    #[DataProvider('rangeProvider')]
    public function testFromMeters(int $range_in_meters, AttackRange $range): void
    {
        self::assertEquals($range, $range->fromMeters($range_in_meters));
    }
}
