<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\Classification;
use App\Models\Transformers\Subgroup;
use RuntimeException;
use Tests\TestCase;

/**
 * @group models
 * @group transformers
 * @small
 */
final class SubgroupTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Subgroup ID "invalid" is invalid');
        new Subgroup('invalid');
    }

    public function testSubgroup(): void
    {
        $group = new Subgroup('actionmaster');
        self::assertSame('Actionmaster', (string)$group);
        self::assertSame(Classification::Standard, $group->class);
        self::assertSame(2, $group->cost);
        self::assertCount(0, $group->requirements);
    }
}