<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Featre\Models;

use DomainException;
use Modules\Root\Models\Character;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('root')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testToStringNotSet(): void
    {
        $character = new Character();
        self::assertSame('Unnamed character', (string)$character);
    }

    public function testToString(): void
    {
        $character = new Character(['name' => 'Floppy the Fierce']);
        self::assertSame('Floppy the Fierce', (string)$character);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function attributeProvider(): array
    {
        return [
            ['charm'],
            ['cunning'],
            ['finese'],
            ['luck'],
            ['might'],
        ];
    }

    #[DataProvider('attributeProvider')]
    public function testAttributesCanNotBeTooLow(string $attribute): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be less than -1');
        $character = new Character([$attribute => -2]);
        // @phpstan-ignore property.dynamicName, expr.resultUnused
        $character->$attribute;
    }

    #[DataProvider('attributeProvider')]
    public function testAttributesCanNotBeToohigh(string $attribute): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be greater than 2');
        $character = new Character([$attribute => 3]);
        // @phpstan-ignore property.dynamicName, expr.resultUnused
        $character->$attribute;
    }
}
