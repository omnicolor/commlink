<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Enums;

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
     * @return array<int, array<int, AttackRange|int>>
     */
    public static function rangeProvider(): array
    {
        return [
            [0, AttackRange::Close],
            [1, AttackRange::Close],
            [3, AttackRange::Close],
            [4, AttackRange::Near],
            [50, AttackRange::Near],
            [51, AttackRange::Medium],
            [250, AttackRange::Medium],
            [251, AttackRange::Far],
            [500, AttackRange::Far],
            [501, AttackRange::Extreme],
            [PHP_INT_MAX, AttackRange::Extreme],
        ];
    }

    #[DataProvider('rangeProvider')]
    public function testFromMeters(int $range_in_meters, AttackRange $range): void
    {
        self::assertEquals($range, $range->fromMeters($range_in_meters));
    }
}
