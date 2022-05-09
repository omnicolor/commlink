<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Character;
use App\Models\Capers\GearArray;
use App\Models\Capers\Identity;
use RuntimeException;

/**
 * Tests for Capers characters.
 * @group capers
 * @small
 */
final class CharacterTest extends \Tests\TestCase
{
    /**
     * Test loading from data store.
     * @test
     */
    public function testNewFromBuilder(): void
    {
        $character = new Character(['name' => 'Test character']);
        $character->save();

        $loaded = Character::find($character->id);
        // @phpstan-ignore-next-line
        self::assertSame('Test character', $loaded->name);
    }

    /**
     * Test displaying the character as a string if they haven't set their name.
     * @test
     */
    public function testToStringNoName(): void
    {
        self::assertSame('Unnamed Character', (string)(new Character()));
    }

    /**
     * Test casting a character to a string if they have a name.
     * @test
     */
    public function testToString(): void
    {
        $character = new Character(['name' => 'Phil']);
        self::assertSame('Phil', (string)$character);
    }

    /**
     * Test getting a character's computed body attribute.
     * @test
     */
    public function testBody(): void
    {
        $character = new Character(['agility' => 3]);
        self::assertSame('10', $character->body);
    }

    /**
     * Test getting an identity for a character that never set one.
     * @test
     */
    public function testUnsetIdentity(): void
    {
        $character = new Character();
        self::assertNull($character->identity);
    }

    /**
     * Test getting an identity for a character with an invalid value.
     * @test
     */
    public function testIdentityInvalid(): void
    {
        $character = new Character([
            'identity' => 'invalid',
            'name' => 'Test character',
        ]);
        self::assertNull($character->identity);
    }

    /**
     * Test getting an identity for a character.
     * @test
     */
    public function testIdentity(): void
    {
        $character = new Character(['identity' => 'rebel']);
        $identity = $character->identity;
        self::assertInstanceOf(Identity::class, $identity);
        self::assertSame('Rebel', (string)$identity);
    }

    /**
     * Test getting a lowly character's maximum hits.
     * @test
     */
    public function testGetMaximumHitsLow(): void
    {
        $character = new Character([
            'charisma' => 1,
            'resilience' => 1,
        ]);
        self::assertSame(8, $character->maximum_hits);
    }

    /**
     * Test getting a character's maximum hits if they're maxed out.
     * @test
     */
    public function testGetMaximumHitsHigh(): void
    {
        $character = new Character([
            'charisma' => 5,
            'resilience' => 5,
        ]);
        self::assertSame(24, $character->maximum_hits);
    }

    /**
     * Test getting a character's mind attribute.
     * @test
     */
    public function testGetMind(): void
    {
        $character = new Character(['perception' => 1]);
        self::assertSame('8', $character->mind);
    }

    /**
     * Test getting a character's perks if they have none.
     * @test
     */
    public function testGetPerksNone(): void
    {
        $character = new Character();
        self::assertCount(0, $character->getPerks());
    }

    /**
     * Test getting a character's perks.
     * @test
     */
    public function testGetPerks(): void
    {
        $character = new Character([
            'perks' => [['id' => 'lucky'], ['id' => 'not-found']],
        ]);
        self::assertCount(1, $character->getPerks());
    }

    /**
     * Test getting a character's powers if they've got only an invalid one.
     * @test
     */
    public function testGetPowersOnlyInvalid(): void
    {
        $character = new Character([
            'powers' => [['id' => 'invalid']],
        ]);
        self::assertEmpty($character->powers);
    }

    /**
     * Test getting a character's powers.
     * @test
     */
    public function testGetPowers(): void
    {
        $character = new Character([
            'powers' => [['id' => 'acid-stream', 4, ['acrid-cloud-boost']]],
        ]);
        self::assertCount(1, $character->powers);
    }

    /**
     * Test getting a character's skills if they only have an invalid one.
     * @test
     */
    public function testGetSkillsOnlyInvalid(): void
    {
        $character = new Character([
            'skills' => ['invalid'],
        ]);
        self::assertEmpty($character->skills);
    }

    /**
     * Test getting a character's speed.
     * @test
     */
    public function testGetSpeed(): void
    {
        self::assertSame(30, (new Character())->speed);
    }

    /**
     * Test getting a character's speed if they have the Fleet of Foot perk.
     * @test
     */
    public function testGetSpeedFleedOfFoot(): void
    {
        $character = new Character([
            'perks' => [['id' => 'fleet-of-foot']],
        ]);
        self::assertSame(40, $character->speed);
    }

    /**
     * Test getting a character's Vice if they have an invalid Vice.
     * @test
     */
    public function testGetInvalidVice(): void
    {
        self::assertNull((new Character())->vice);
    }

    /**
     * Test getting a character's vice.
     * @test
     */
    public function testGetVice(): void
    {
        $character = new Character(['vice' => 'drugs']);
        self::assertSame('Drugs', (string)$character->vice);
    }

    /**
     * Test getting a character's virtue if the have an invalid value.
     * @test
     */
    public function testGetVirtue(): void
    {
        self::assertNull((new Character())->virtue);
    }

    /**
     * Test trying to get trait defense for an invalid attribute.
     * @test
     */
    public function testGetTraitDefenseInvalidAttribute(): void
    {
        $character = new Character();
        self::assertSame('?', $character->getTraitDefense('your-mom'));
    }

    /**
     * Test trying to get trait defense for an attribute that is out of range.
     * @test
     */
    public function testGetTraitDefenseInvalidValue(): void
    {
        $character = new Character(['agility' => 6]);
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid trait value for trait agility: 6');
        $character->getTraitDefense('agility');
    }

    /**
     * Test the different values for trait defense.
     * @test
     */
    public function testGetTraitDefense(): void
    {
        $character = new Character([
            'agility' => 1,
            'charisma' => 2,
            'expertise' => 3,
            'perception' => 4,
            'resilience' => 5,
        ]);
        self::assertSame('8', $character->getTraitDefense('agility'));
        self::assertSame('9', $character->getTraitDefense('charisma'));
        self::assertSame('10', $character->getTraitDefense('expertise'));
        self::assertSame('J', $character->getTraitDefense('perception'));
        self::assertSame('Q', $character->getTraitDefense('resilience'));
    }

    /**
     * Test trying to find which attribute has a certain value.
     * @test
     */
    public function testFindAttributeAt(): void
    {
        $character = new Character([
            'agility' => 1,
            'charisma' => 2,
            'expertise' => 2,
            'perception' => 2,
            'resilience' => 3,
        ]);

        self::assertSame('agility', $character->findAttributeAt(1));
        self::assertSame('resilience', $character->findAttributeAt(3));
        self::assertNull($character->findAttributeAt(9));
    }

    /**
     * Test getting a character's gear if they have none.
     * @test
     */
    public function testGetGearEmpty(): void
    {
        $character = new Character();
        self::assertInstanceOf(GearArray::class, $character->gear);
        self::assertEmpty($character->gear);
    }

    /**
     * Test getting a character's gear if they've got some things.
     * @test
     */
    public function testGetGear(): void
    {
        $character = new Character([
            'gear' => [
                ['id' => 'rifle', 'quantity' => 2],
                ['id' => 'liquor-cheap-bottle', 'quantity' => 2],
                ['id' => 'invalid'],
            ],
        ]);

        self::assertCount(2, $character->gear);
        // @phpstan-ignore-next-line
        self::assertSame(2, $character->gear[1]->quantity);
    }

    /**
     * Test getting a character's strength if they have the super strength
     * power.
     * @test
     */
    public function testGetStrengthWithSuperStrength(): void
    {
        $character = new Character([
            'powers' => [
                'super-strength' => [
                    'boosts' => [
                        'damage-boost',
                    ],
                    'id' => 'super-strength',
                    'rank' => 1,
                ],
            ],
            'strength' => 3,
        ]);

        self::assertSame(4, $character->strength);
    }
}
