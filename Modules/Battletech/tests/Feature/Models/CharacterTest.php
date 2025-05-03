<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Models;

use DomainException;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Battletech\Models\Appearance;
use Modules\Battletech\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('battletech')]
#[Small]
class CharacterTest extends TestCase
{
    use WithFaker;

    public function testToString(): void
    {
        $name = $this->faker->name;
        $character = new Character(['name' => $name]);
        self::assertSame($name, (string)$character);
    }

    public function testToStringUnnamed(): void
    {
        $character = new Character();
        self::assertSame('Unnamed Mechwarrior', (string)$character);
    }

    public function testAppearanceConstructor(): void
    {
        $character = new Character(['appearance' => ['hair' => 'none']]);
        self::assertSame('none', $character->appearance->hair);
    }

    public function testAppearanceSetterObject(): void
    {
        $character = new Character();
        $character->appearance = Appearance::make(['eyes' => 'grey']);
        self::assertSame('grey', $character->appearance->eyes);
    }

    public function testAppearanceSetterArray(): void
    {
        $character = new Character();
        $character->appearance = ['extra' => 'Lots of tattoos'];
        self::assertSame('Lots of tattoos', $character->appearance->extra);
    }

    public function testAttributesEmpty(): void
    {
        $character = new Character();
        self::assertSame(1, $character->attributes->strength->value);
        self::assertSame(1, $character->attributes->body->value);
        self::assertSame(1, $character->attributes->reflexes->value);
        self::assertSame(1, $character->attributes->dexterity->value);
        self::assertSame(1, $character->attributes->intelligence->value);
        self::assertSame(1, $character->attributes->willpower->value);
        self::assertSame(1, $character->attributes->charisma->value);
        self::assertSame(1, $character->attributes->edge->value);
    }

    public function testAttributesInvalid(): void
    {
        $character = new Character(['attributes' => []]);
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes list is incomplete.');
        // @phpstan-ignore expr.resultUnused
        $character->attributes;
    }
}
