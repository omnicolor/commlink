<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\ValueObjects;

use DomainException;
use Modules\Battletech\ValueObjects\Attribute;
use Modules\Battletech\ValueObjects\Attributes;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('battletech')]
#[Small]
final class AttributesTest extends TestCase
{
    public function testMakeWithMissingAttribute(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes list is incomplete.');
        // @phpstan-ignore argument.type
        Attributes::make([]);
    }

    public function testMake(): void
    {
        $raw_attributes = [
            'strength' => 1,
            'body' => 2,
            'reflexes' => 3,
            'dexterity' => 4,
            'intelligence' => 5,
            'willpower' => 6,
            'charisma' => 7,
            'edge' => 8,
        ];
        $attributes = Attributes::make($raw_attributes);
        self::assertEquals(new Attribute(1), $attributes->strength);
        self::assertSame(2, $attributes->body->value);
        self::assertSame(3, $attributes->reflexes->value);
        self::assertSame(4, $attributes->dexterity->value);
        self::assertSame(5, $attributes->intelligence->value);
        self::assertSame(6, $attributes->willpower->value);
        self::assertSame(7, $attributes->charisma->value);
        self::assertSame(8, $attributes->edge->value);
    }

    public function testToArray(): void
    {
        $attributes = Attributes::make([
            'strength' => 1,
            'body' => 2,
            'reflexes' => 3,
            'dexterity' => 4,
            'intelligence' => 5,
            'willpower' => 6,
            'charisma' => 7,
            'edge' => 8,
        ]);

        self::assertSame(
            [
                'body' => 2,
                'charisma' => 7,
                'dexterity' => 4,
                'edge' => 8,
                'intelligence' => 5,
                'reflexes' => 3,
                'strength' => 1,
                'willpower' => 6,
            ],
            $attributes->toArray(),
        );
    }
}
