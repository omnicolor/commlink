<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\RelationAspect;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class RelationAspectTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Relation aspect "not-found" not found');
        new RelationAspect('not-found');
    }

    public function testConstructor(): void
    {
        $aspect = new RelationAspect('adversarial');
        self::assertSame('Adversarial', (string)$aspect);
    }

    public function testAll(): void
    {
        $aspects = RelationAspect::all();
        self::assertCount(9, $aspects);
    }
}
