<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\ValueObjects;

use DomainException;
use Modules\Avatar\ValueObjects\Attribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class AttributeTest extends TestCase
{
    public function testToString(): void
    {
        self::assertSame('0', (string)(new Attribute(0)));
        self::assertSame('1', (string)(new Attribute(1)));
    }

    public function testTooLow(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be less than -1');
        new Attribute(-2);
    }

    public function testTooHigh(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be greater than 4');
        new Attribute(5);
    }
}
