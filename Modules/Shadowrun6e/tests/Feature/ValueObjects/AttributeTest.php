<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\ValueObjects;

use Modules\Shadowrun6e\Models\Character;
use Modules\Shadowrun6e\ValueObjects\Attribute;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class AttributeTest extends TestCase
{
    public function testAttributeTooLow(): void
    {
        self::expectException(OutOfRangeException::class);
        new Attribute(-1, new Character());
    }

    public function testAttributeTooHigh(): void
    {
        self::expectException(OutOfRangeException::class);
        new Attribute(11, new Character());
    }

    public function testToString(): void
    {
        $attribute = new Attribute(2, new Character());
        self::assertSame('2', (string)$attribute);
    }

    public function testBaseValue(): void
    {
        $attribute = new Attribute(3, new Character());
        self::assertSame(3, $attribute->base_value);
    }

    public function testValue(): void
    {
        $attribute = new Attribute(4, new Character());
        self::assertSame(4, $attribute->value);
    }

    public function testUnknownProperty(): void
    {
        $attribute = new Attribute(5, new Character());
        // @phpstan-ignore property.notFound
        self::assertNull($attribute->unknown);
    }

    public function testSet(): void
    {
        $attribute = new Attribute(2, new Character());
        self::expectException(RuntimeException::class);
        // @phpstan-ignore property.notFound
        $attribute->foo = 10;
    }

    public function testIsset(): void
    {
        $attribute = new Attribute(2, new Character());
        self::assertTrue(isset($attribute->value));
        self::assertTrue(isset($attribute->base_value));
        // @phpstan-ignore property.notFound
        self::assertFalse(isset($attribute->unknown));
    }
}
