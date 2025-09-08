<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Iterator;
use Modules\Subversion\Models\RelationLevel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class RelationLevelTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Relation level "not-found" not found');
        new RelationLevel('not-found');
    }

    public function testLoadRelationLevel(): void
    {
        $relation = new RelationLevel('sponsor');
        self::assertSame('Sponsor', (string)$relation);
    }

    public function testLoadAll(): void
    {
        self::assertCount(4, RelationLevel::all());
    }

    /**
     * @return Iterator<int, array<int, (int | string)>>
     */
    public static function sortDataProvider(): Iterator
    {
        yield ['big-shot', 'big-shot', 0];
        yield ['big-shot', 'personal-connection', -1];
        yield ['personal-connection', 'big-shot', 1];
        yield ['big-shot', 'sponsor', -1];
        yield ['personal-connection', 'sponsor', -1];
        yield ['sponsor', 'big-shot', 1];
        yield ['sponsor', 'sponsor', 0];
        yield ['friend', 'friend', 0];
    }

    #[DataProvider('sortDataProvider')]
    public function testSort(string $a, string $b, int $expected): void
    {
        $a = new RelationLevel($a);
        $b = new RelationLevel($b);
        self::assertSame($expected, RelationLevel::sort($a, $b));
    }
}
