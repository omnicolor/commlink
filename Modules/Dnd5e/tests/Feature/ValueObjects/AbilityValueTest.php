<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\ValueObjects;

use Modules\Dnd5e\ValueObjects\AbilityValue;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('dnd5e')]
#[Small]
final class AbilityValueTest extends TestCase
{
    public function testValueTooLow(): void
    {
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Attribute value is out of range');
        new AbilityValue(0);
    }

    public function testValueTooHigh(): void
    {
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Attribute value is out of range');
        new AbilityValue(31);
    }

    public function testToString(): void
    {
        $strength = new AbilityValue(10);
        self::assertSame('10', (string)$strength);
    }

    public function testGet(): void
    {
        $ability = new AbilityValue(5);
        self::assertSame(5, $ability->value);
        self::assertSame(-3, $ability->modifier);
    }

    public function testGetUknown(): void
    {
        $ability = new AbilityValue(5);
        // @phpstan-ignore property.notFound
        self::assertNull($ability->foo);
    }
}
