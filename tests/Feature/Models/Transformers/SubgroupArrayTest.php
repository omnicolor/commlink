<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\Subgroup;
use App\Models\Transformers\SubgroupArray;
use Tests\TestCase;
use TypeError;

/**
 * @group models
 * @group transformers
 * @small
 */
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