<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Character;
use App\Models\Capers\GearArray;
use App\Models\Capers\Identity;
use App\Models\Capers\Power;
use App\Models\Capers\PowerArray;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Capers characters.
 * @group capers
 */
#[Small]
final class CharacterTest extends TestCase
{
    /**
     * Test loading from data store.
     */
    public function testNewFromBuilder(): void
    {
        $character = new Character([
            'name' => 'Test Capers character',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $character->save();

        $loaded = Character::find($character->id);
        // @phpstan-ignore-next-line
        self::assertSame('Test Capers character', $loaded->name);
        $character->delete();
    }

    /**
     * Test displaying the character as a string if they haven't set their name.
     */
    public function testToStringNoName(): void
    {
        self::assertSame('Unnamed Character', (string)(new Character()));
    }

    /**
     * Test casting a character to a string if they have a name.
     */
    public function testToString(): void
    {
        $character = new Character(['name' => 'Phil']);
        self::assertSame('Phil', (string)$character);
    }

    /**
     * Test getting a character's computed body attribute.
     */
    public function testBody(): void
    {
        $character = new Character(['agility' => 3]);
        self::assertSame('10', $character->body);
    }

    /**
     * Test getting an identity for a character that never set one.
     */
    public function testUnsetIdentity(): void
    {
        $character = new Character();
        self::assertNull($character->identity);
    }

    /**
     * Test getting an identity for a character with an invalid value.
     */
    public function testIdentityInvalid(): void
    {
        $character = new Character([
            'identity' => 'invalid',
            'name' => 'Test Capers unsaved character',
        ]);
        self::assertNull($character->identity);
    }

    /**
     * Test getting an identity for a character.
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
     */
    public function testGetMind(): void
    {
        $character = new Character(['perception' => 1]);
        self::assertSame('8', $character->mind);
    }

    /**
     * Test getting a character's perks if they have none.
     */
    public function testGetPerksNone(): void
    {
        $character = new Character();
        self::assertCount(0, $character->getPerks());
    }

    /**
     * Test getting a character's perks.
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
     */
    public function testGetPowers(): void
    {
        $character = new Character([
            'powers' => [['id' => 'acid-stream', 4, ['acrid-cloud-boost']]],
        ]);
        self::assertCount(1, $character->powers);
    }

    public function testSettingPowersArray(): void
    {
        $character = new Character();
        $character->powers = [
            [
                'id' => 'acid-stream',
                'rank' => 4,
                'boosts' => ['acrid-cloud-boost'],
            ],
        ];
        self::assertCount(1, $character->powers);
    }

    public function testSettingPowersPowerArray(): void
    {
        $character = new Character();
        $powers = new PowerArray();
        $powers[] = new Power(
            id: 'acid-stream',
            rank: 4,
            boosts: ['acrid-cloud-boost']
        );
        $character->powers = $powers;
        self::assertCount(1, $character->powers);
    }

    /**
     * Test getting a character's skills if they only have an invalid one.
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
     */
    public function testGetSpeed(): void
    {
        self::assertSame(30, (new Character())->speed);
    }

    /**
     * Test getting a character's speed if they have the Fleet of Foot perk.
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
     */
    public function testGetInvalidVice(): void
    {
        self::assertNull((new Character())->vice);
    }

    /**
     * Test getting a character's vice.
     */
    public function testGetVice(): void
    {
        $character = new Character(['vice' => 'drugs']);
        self::assertSame('Drugs', (string)$character->vice);
    }

    /**
     * Test getting a character's virtue if the have an invalid value.
     */
    public function testGetVirtue(): void
    {
        self::assertNull((new Character())->virtue);
    }

    /**
     * Test trying to get trait defense for an invalid attribute.
     */
    public function testGetTraitDefenseInvalidAttribute(): void
    {
        $character = new Character();
        self::assertSame('?', $character->getTraitDefense('your-mom'));
    }

    /**
     * Test trying to get trait defense for an attribute that is out of range.
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
     */
    public function testGetGearEmpty(): void
    {
        $character = new Character();
        self::assertInstanceOf(GearArray::class, $character->gear);
        self::assertEmpty($character->gear);
    }

    /**
     * Test getting a character's gear if they've got some things.
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
