<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Career;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class CareerTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Career ID "unknown" is invalid');
        new Career('unknown');
    }

    public function testToString(): void
    {
        $career = new Career('colonial-marine');
        self::assertSame('Colonial marine', (string)$career);
    }

    public function testAll(): void
    {
        self::assertCount(2, Career::all());
    }
}
