<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\RelationArchetype;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class RelationArchetypeTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Relation archetype "not-found" not found');
        new RelationArchetype('not-found');
    }

    public function testConstructor(): void
    {
        $archetype = new RelationArchetype('care');
        self::assertSame('Care', (string)$archetype);
    }

    public function testAll(): void
    {
        $archetypes = RelationArchetype::all();
        self::assertCount(2, $archetypes);
    }
}
