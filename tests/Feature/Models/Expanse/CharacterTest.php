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
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Expanse characters.
 * @group expanse
 */
#[Small]
final class CharacterTest extends TestCase
{
    /**
     * Test displaying the character as a string just shows their name.
     */
    public function testToString(): void
    {
        $character = new Character(['name' => 'James Holden']);
        self::assertSame('James Holden', (string)$character);
    }

    /**
     * Test getting a character's attribute.
     */
    public function testGetAccuracy(): void
    {
        $character = new Character(['accuracy' => 1]);
        self::assertSame(1, $character->accuracy);
    }

    /**
     * Test getting a character's background.
     */
    public function testGetBackground(): void
    {
        $character = new Character(['background' => 'trade']);
        self::assertInstanceOf(Background::class, $character->background);
        self::assertSame('Trade', $character->background->name);
    }

    /**
     * Test getting a character's background if it's invalid.
     */
    public function testGetBackgroundInvalid(): void
    {
        $character = new Character(['background' => 'invalid']);
        self::expectException(RuntimeException::class);
        $unused = $character->background;
    }

    /**
     * Test getting a character's focuses if they have none.
     */
    public function testGetFocusesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getFocuses());
    }

    /**
     * Test getting a character's focuses if they only have invalid ones.
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
     */
    public function testGetOriginInvalid(): void
    {
        $character = new Character(['origin' => 'Jovian']);
        self::expectException(RuntimeException::class);
        $unused = $character->origin;
    }

    /**
     * Test getting the character's social class.
     */
    public function testGetSocialClass(): void
    {
        $character = new Character(['socialClass' => 'Middle']);
        self::assertInstanceOf(SocialClass::class, $character->social_class);
        self::assertSame('Middle Class', $character->social_class->name);
    }

    /**
     * Test getting the character's social class if it's invalid.
     */
    public function testGetSocialClassInvalid(): void
    {
        $character = new Character(['socialClass' => 'invalid']);
        self::expectException(RuntimeException::class);
        $unused = $character->social_class;
    }

    /**
     * Test getting the character's talents if they have none.
     */
    public function testGetTalentsNone(): void
    {
        $character = new Character();
        self::assertInstanceOf(TalentArray::class, $character->getTalents());
        self::assertEmpty($character->getTalents());
    }

    /**
     * Test getting the character's talents if they only have an invalid one.
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
     */
    public function testHasFocusDoesnt(): void
    {
        self::assertFalse((new Character())->hasFocus(new Focus('crafting')));
    }

    /**
     * Test hasFocus() if the character has it.
     */
    public function testHasFocus(): void
    {
        $character = new Character(['focuses' => [['id' => 'crafting']]]);
        self::assertTrue($character->hasFocus(new Focus('crafting')));
    }
}
