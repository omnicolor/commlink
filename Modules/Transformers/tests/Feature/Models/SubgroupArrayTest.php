<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Models;

use Modules\Transformers\Models\Subgroup;
use Modules\Transformers\Models\SubgroupArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;

#[Group('transformers')]
#[Small]
final class SubgroupArrayTest extends TestCase
{
    public function testStoreInvalidType(): void
    {
        $groups = new SubgroupArray();
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $groups[] = 'test';
    }

    public function testStoreValidType(): void
    {
        $groups = new SubgroupArray();
        self::assertCount(0, $groups);
        $groups[] = new Subgroup('actionmaster');
        self::assertCount(1, $groups);
    }
}
