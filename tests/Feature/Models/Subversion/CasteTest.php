<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Caste;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class CasteTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Caste "not-found" not found');
        new Caste('not-found');
    }

    public function testConstructor(): void
    {
        $caste = new Caste('lower-middle');
        self::assertSame('Lower-middle caste', (string)$caste);
        self::assertSame(0, $caste->fortune);
    }

    public function testAll(): void
    {
        $castes = Caste::all();
        self::assertCount(6, $castes);
    }
}
