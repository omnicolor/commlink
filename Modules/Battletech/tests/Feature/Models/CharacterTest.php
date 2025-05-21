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

    public function testSkillsEmpty(): void
    {
        $character = new Character();
        self::assertSame([], $character->skills);
    }

    public function testSkills(): void
    {
        $character = new Character([
            'skills' => [
                [
                    'id' => 'acting',
                    'level' => 1,
                    'specialty' => 'Seduction',
                ],
                ['id' => 'administration'],
            ],
        ]);

        self::assertCount(2, $character->skills);

        self::assertSame('Seduction', $character->skills[0]->specialty);
        self::assertSame('Acting (Seduction)', (string)$character->skills[0]);
        self::assertSame(1, $character->skills[0]->level);

        self::assertNull($character->skills[1]->level);
    }

    public function testSkillsInvalid(): void
    {
        $character = new Character([
            'skills' => [
                ['id' => 'invalid'],
            ],
        ]);
        self::assertCount(0, $character->skills);
    }

    public function testTraitsEmpty(): void
    {
        $character = new Character();
        self::assertSame([], $character->traits);
    }

    public function testTraits(): void
    {
        $character = new Character([
            'traits' => ['ambidextrous', 'animal-empathy'],
        ]);

        self::assertCount(2, $character->traits);

        self::assertSame('Ambidextrous', $character->traits[0]->name);
    }

    public function testTraitsNotFound(): void
    {
        $character = new Character(['traits' => ['invalid']]);
        self::assertCount(0, $character->traits);
    }
}
