<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\ValueObjects;

use Modules\Dnd5e\ValueObjects\ClassLevel;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('dnd5e')]
#[Small]
final class ClassLevelTest extends TestCase
{
    public function testTooLow(): void
    {
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Level must be between 1 and 20');
        new ClassLevel(0);
    }

    public function testTooHigh(): void
    {
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Level must be between 1 and 20');
        new ClassLevel(21);
    }

    public function testToString(): void
    {
        $level = new ClassLevel(10);
        self::assertSame('10', (string)$level);
    }
}
