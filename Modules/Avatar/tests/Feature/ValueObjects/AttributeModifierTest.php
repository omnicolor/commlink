<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\ValueObjects;

use DomainException;
use Modules\Avatar\ValueObjects\AttributeModifier;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class AttributeModifierTest extends TestCase
{
    public function testToString(): void
    {
        self::assertSame('0', (string)(new AttributeModifier(0)));
        self::assertSame('1', (string)(new AttributeModifier(1)));
    }

    public function testTooLow(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attribute modifiers can not be less than -2');
        new AttributeModifier(-3);
    }

    public function testTooHigh(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attribute modifiers can not be greater than 1');
        new AttributeModifier(2);
    }
}
