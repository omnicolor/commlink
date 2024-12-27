<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Feature\ValueObjects;

use DomainException;
use Modules\Root\ValueObjects\Attribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('root')]
#[Small]
final class AttributeTest extends TestCase
{
    public function testTooLowThrowsException(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be less than -1');
        new Attribute(-2);
    }

    public function testTooHighThrowsException(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be greater than 2');
        new Attribute(3);
    }

    public function testValidAttribute(): void
    {
        $attribute = new Attribute(0);
        self::assertSame(0, $attribute->value);
    }

    public function testToString(): void
    {
        $attribute = new Attribute(1);
        self::assertSame('1', (string)$attribute);
    }

    public function testHighAttributeWithMove(): void
    {
        $attribute = new Attribute(value: 3, improved_by_move: true);
        self::assertSame(3, $attribute->value);
    }

    public function testTooHighAttributeEvenWithMove(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be greater than 3 including a move');
        new Attribute(4, true);
    }
}
