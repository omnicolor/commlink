<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\ValueObjects;

use Modules\Shadowrun6e\Models\Character;
use Modules\Shadowrun6e\ValueObjects\Attribute;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class AttributeTest extends TestCase
{
    public function testAttributeTooLow(): void
    {
        self::expectException(OutOfRangeException::class);
        new Attribute(-1, new Character(), 'body');
    }

    public function testAttributeTooHigh(): void
    {
        self::expectException(OutOfRangeException::class);
        new Attribute(11, new Character(), 'body');
    }

    public function testToString(): void
    {
        $attribute = new Attribute(2, new Character(), 'body');
        self::assertSame('2', (string)$attribute);
    }

    public function testBaseValue(): void
    {
        $attribute = new Attribute(3, new Character(), 'body');
        self::assertSame(3, $attribute->base_value);
    }

    public function testValue(): void
    {
        $attribute = new Attribute(4, new Character(), 'body');
        self::assertSame(4, $attribute->value);
    }

    public function testUnknownProperty(): void
    {
        $attribute = new Attribute(5, new Character(), 'body');
        // @phpstan-ignore property.notFound
        self::assertNull($attribute->unknown);
    }

    public function testSet(): void
    {
        $attribute = new Attribute(2, new Character(), 'body');
        self::expectException(RuntimeException::class);
        // @phpstan-ignore property.notFound
        $attribute->foo = 10;
    }

    public function testIsset(): void
    {
        $attribute = new Attribute(2, new Character(), 'body');
        self::assertTrue(isset($attribute->value));
        // @phpstan-ignore property.notFound
        self::assertFalse(isset($attribute->unknown));
    }

    public function testAffectedByQuality(): void
    {
        $character = new Character([
            'qualities' => [['id' => 'super-strong']],
            'strength' => 5,
        ]);
        self::assertSame(6, $character->strength->value);
    }
}
