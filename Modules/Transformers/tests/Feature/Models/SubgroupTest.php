<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Models;

use Modules\Transformers\Models\Classification;
use Modules\Transformers\Models\Subgroup;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('transformers')]
#[Small]
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
