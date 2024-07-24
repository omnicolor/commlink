<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Talent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class TalentTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Talent ID "unknown" is invalid');
        new Talent('unknown');
    }

    public function testToString(): void
    {
        $talent = new Talent('banter');
        self::assertSame('Banter', (string)$talent);
    }

    public function testAll(): void
    {
        self::assertCount(2, Talent::all());
    }
}
