<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Origin;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class OriginTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Origin "not-found" not found');
        new Origin('not-found');
    }

    public function testConstructor(): void
    {
        $origin = new Origin('altaipheran');
        self::assertSame('Altaipheran', (string)$origin);
    }

    public function testAll(): void
    {
        $origins = Origin::all();
        self::assertCount(1, $origins);
    }
}
