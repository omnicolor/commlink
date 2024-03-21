<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Lineage;
use RuntimeException;
use Tests\TestCase;

/**
 * @group subversion
 * @small
 */
final class LineageTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Lineage "not-found" not found');
        new Lineage('not-found');
    }

    public function testConstructor(): void
    {
        $lineage = new Lineage('dwarven', 'small');
        self::assertSame('Dwarven', (string)$lineage);
        self::assertSame('Small', (string)$lineage->option);
    }

    public function testAll(): void
    {
        $lineages = Lineage::all();
        self::assertCount(1, $lineages);
    }
}
