<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Background;
use App\Models\Expanse\Character;
use App\Models\Expanse\Focus;
use App\Models\Expanse\Origin\Belter;
use App\Models\Expanse\Origin\Earther;
use App\Models\Expanse\SocialClass;
use App\Models\Expanse\Talent;
use App\Models\Expanse\TalentArray;

/**
 * Tests for Expanse characters.
 * @covers \App\Models\Expanse\Character
 * @group expanse
 * @group models
 * @small
 */
final class CharacterTest extends \Tests\TestCase
{
    /**
     * Test displaying the character as a string just shows their name.
     * @test
     */
    public function testToString(): void
    {
        $character = new Character(['name' => 'James Holden']);
        self::assertSame('James Holden', (string)$character);
    }

    /**
     * Test getting a character's attribute.
     * @test
     */
    public function testGetAccuracy(): void
    {
        $character = new Character(['accuracy' => 1]);
        self::assertSame(1, $character->accuracy);
    }

    /**
     * Test getting a character's background.
     * @test
     */
    public function testGetBackground(): void
    {
        $character = new Character(['background' => 'trade']);
        self::assertInstanceOf(Background::class, $character->background);
        self::assertSame('Trade', $character->background->name);
    }

    /**
     * Test getting a character's background if it's invalid.
     * @test
     */
    public function testGetBackgroundInvalid(): void
    {
        $character = new Character(['background' => 'invalid']);
        self::expectException(\RuntimeException::class);
        $unused = $character->background;
    }

    /**
     * Test getting a character's focuses if they have none.
     * @test
     */
    public function testGetFocusesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getFocuses());
    }

    /**
     * Test getting a character's focuses if they only have invalid ones.
     * @test
     */
    public function testGetFocusesInvalid(): void
    {
        $character = new Character([
            'focuses' => [
                ['id' => 'invalid'],
            ],
        ]);
        self::assertEmpty($character->getFocuses());
    }

    /**
     * Test getting a character's focuses.
     * @test
     */
    public function testGetFocuses(): void
    {
        $character = new Character([
            'focuses' => [
                ['id' => 'crafting'],
            ],
        ]);
        self::assertCount(1, $character->getFocuses());
        $focus = $character->getFocuses()[0];
        self::assertSame('Crafting', (string)$focus);
        // @phpstan-ignore-next-line
        self::assertSame(1, $focus->level);
    }

    /**
     * Test getting a character's focuses if a level is set.
     * @test
     */
    public function testGetFocusesWithLevel(): void
    {
        $character = new Character([
            'focuses' => [
                ['id' => 'crafting', 'level' => 2],
            ],
        ]);
        self::assertCount(1, $character->getFocuses());
        $focus = $character->getFocuses()[0];
        self::assertSame('Crafting', (string)$focus);
        // @phpstan-ignore-next-line
        self::assertSame(2, $focus->level);
    }

    /**
     * Test getting the character's origin.
     * @test
     */
    public function testGetOrigin(): void
    {
        $character = new Character(['origin' => 'Earther']);
        self::assertInstanceOf(Earther::class, $character->origin);
        $character = new Character(['origin' => 'Belter']);
        self::assertInstanceOf(Belter::class, $character->origin);
    }

    /**
     * Test getting the character's origin if it's invalid.
     * @test
     */
    public function testGetOriginInvalid(): void
    {
        $character = new Character(['origin' => 'Jovian']);
        self::expectException(\RuntimeException::class);
        $unused = $character->origin;
    }

    /**
     * Test getting the character's social class.
     * @test
     */
    public function testGetSocialClass(): void
    {
        $character = new Character(['socialClass' => 'Middle']);
        self::assertInstanceOf(SocialClass::class, $character->social_class);
        self::assertSame('Middle Class', $character->social_class->name);
    }

    /**
     * Test getting the character's social class if it's invalid.
     * @test
     */
    public function testGetSocialClassInvalid(): void
    {
        $character = new Character(['socialClass' => 'invalid']);
        self::expectException(\RuntimeException::class);
        $unused = $character->social_class;
    }

    /**
     * Test getting the character's talents if they have none.
     * @test
     */
    public function testGetTalentsNone(): void
    {
        $character = new Character();
        self::assertInstanceOf(TalentArray::class, $character->getTalents());
        self::assertEmpty($character->getTalents());
    }

    /**
     * Test getting the character's talents if they only have an invalid one.
     * @test
     */
    public function testGetTalentsInvalid(): void
    {
        $character = new Character([
            'talents' => [
                ['name' => 'invalid'],
            ],
        ]);
        self::assertEmpty($character->getTalents());
    }

    /**
     * Test getting the character's talents.
     * @test
     */
    public function testGetTalents(): void
    {
        $character = new Character([
            'talents' => [
                ['name' => 'fringer', 'level' => 3],
            ],
        ]);
        self::assertNotEmpty($character->getTalents());
        /** @var Talent */
        $talent = $character->getTalents()[0];
        self::assertSame('Fringer', $talent->name);
        self::assertSame(Talent::MASTER, $talent->level);
    }

    /**
     * Test hasFocus() if the character has none.
     * @test
     */
    public function testHasFocusDoesnt(): void
    {
        self::assertFalse((new Character())->hasFocus(new Focus('crafting')));
    }

    /**
     * Test hasFocus() if the character has it.
     * @test
     */
    public function testHasFocus(): void
    {
        $character = new Character(['focuses' => [['id' => 'crafting']]]);
        self::assertTrue($character->hasFocus(new Focus('crafting')));
    }
}
