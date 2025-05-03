<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\ValueObjects;

use DomainException;
use Modules\Battletech\ValueObjects\Attribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('battletech')]
#[Small]
final class AttributeTest extends TestCase
{
    public function testAttributeTooLow(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be less than 1.');
        new Attribute(-1);
    }

    public function testAttributeAsString(): void
    {
        $attribute = new Attribute(3);
        self::assertSame('3', (string)$attribute);
    }
}
