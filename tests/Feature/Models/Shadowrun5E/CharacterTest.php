<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\ActiveSkill;
use App\Models\Shadowrun5E\Armor;
use App\Models\Shadowrun5E\ArmorArray;
use App\Models\Shadowrun5E\ArmorModification;
use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\MentorSpirit;
use App\Models\Shadowrun5E\Quality;
use App\Models\Shadowrun5E\QualityArray;

/**
 * Tests for Shadowrun 5E characters.
 * @covers \App\Models\Shadowrun5E\Character
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class CharacterTest extends \Tests\TestCase
{
    /**
     * Test displaying the character as a string just shows their handle.
     * @test
     */
    public function testToString(): void
    {
        $character = new Character(['handle' => 'The Smiling Bandit']);
        self::assertSame('The Smiling Bandit', (string)$character);
    }

    /**
     * Test getting the hidden Mongo _id field.
     *
     * It's hidden, but still gettable.
     * @test
     */
    public function testHiddenId(): void
    {
        $character = Character::factory()->create();
        self::assertNotNull($character->_id);
        $character->delete();
    }

    /**
     * Test getting the character's ID.
     * @test
     */
    public function testGetId(): void
    {
        $character = Character::factory()->create();
        self::assertNotNull($character->id);
        self::assertSame($character->_id, $character->id);
        $character->delete();
    }

    /**
     * Test getting a character's adept powers if they don't have any.
     * @test
     */
    public function testGetAdeptPowersEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getAdeptPowers());
    }

    /**
     * Test getting a character's adept powers if they have an invalid power.
     * @test
     */
    public function testGetAdeptPowersInvalid(): void
    {
        $character = new Character([
            'magics' => [
                'powers' => [
                    'not-found',
                ],
            ],
        ]);
        self::assertEmpty($character->getAdeptPowers());
    }

    /**
     * Test getting a character's adept powers.
     * @test
     */
    public function testGetAdeptPowers(): void
    {
        $character = new Character([
            'magics' => [
                'powers' => [
                    'improved-sense-direction-sense',
                ],
            ],
        ]);
        self::assertCount(1, $character->getAdeptPowers());
    }

    /**
     * Test getting a character's armor if they don't have any.
     * @test
     */
    public function testGetArmorEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getArmor());
    }

    /**
     * Test getting a character's armor if they have one that is invalid.
     * @test
     */
    public function testGetArmorInvalid(): void
    {
        $character = new Character(['armor' => [['id' => 'not-found']]]);
        self::assertEmpty($character->getArmor());
    }

    /**
     * Test getting a character's armor.
     * @test
     */
    public function testGetArmor(): void
    {
        $character = new Character([
            'armor' => [
                ['id' => 'armor-jacket'],
                ['id' => 'berwick-suit'],
            ],
        ]);
        self::assertCount(2, $character->getArmor());
    }

    /**
     * Test getting a character's augmentations if they don't have any.
     * @test
     */
    public function testGetAugmentationsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getAugmentations());
    }

    /**
     * Test getting a character's augmentations if they have an invalid one.
     * @test
     */
    public function testGetAugmentationsInvalid(): void
    {
        $character = new Character([
            'augmentations' => [['id' => 'not-found']],
        ]);
        self::assertEmpty($character->getAugmentations());
    }

    /**
     * Test getting a character's augmentations.
     * @test
     */
    public function testGetAugmentations(): void
    {
        $character = new Character([
            'augmentations' => [
                ['id' => 'cyberears-1'],
                ['id' => 'cybereyes-1'],
            ],
        ]);
        self::assertCount(2, $character->getAugmentations());
    }

    /**
     * Test getting a character's complex forms if they have none.
     * @test
     */
    public function testGetComplexFormsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getComplexForms());
    }

    /**
     * Test getting a character's complex forms if they've got an invalid one.
     * @test
     */
    public function testGetComplexFormsInvalid(): void
    {
        $character = new Character(['complexForms' => ['invalid']]);
        self::assertEmpty($character->getComplexForms());
    }

    /**
     * Test getting a character's complex forms.
     * @test
     */
    public function testGetComplexForms(): void
    {
        $character = new Character(['complexForms' => ['cleaner']]);
        self::assertNotEmpty($character->getComplexForms());
    }

    /**
     * Test getting a character's gear if they have none.
     * @test
     */
    public function testGetGearEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getGear());
    }

    /**
     * Test getting a character's gear if they've only got invalid gear.
     * @test
     */
    public function testGetGearInvalid(): void
    {
        $character = new Character(['gear' => [['id' => 'invalid']]]);
        self::assertEmpty($character->getGear());
    }

    /**
     * Test getting a character's gear.
     * @test
     */
    public function testGetGear(): void
    {
        $character = new Character(['gear' => [[
            'id' => 'credstick-gold',
            'quantity' => 1,
        ]]]);
        self::assertNotEmpty($character->getGear());
    }

    /**
     * Test getting a character's knowledge skills if they're dumB.
     * @test
     */
    public function testGetKnowledgeSkillsNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getKnowledgeSkills());
    }

    /**
     * Test getting the character's knowledge skills if they have an invalid
     * skill category.
     * @test
     */
    public function testGetKnowledgeSkillInvalid(): void
    {
        $character = new Character(['knowledgeSkills' => [
            [
                'name' => 'Elven Wines',
                'category' => 'drunken',
                'level' => 4,
            ],
        ]]);
        self::assertEmpty($character->getKnowledgeSkills());
    }

    /**
     * Test getting a character's knowledge skills.
     * @test
     */
    public function testGetKnowledgeSkills(): void
    {
        $character = new Character(['knowledgeSkills' => [
            [
                'name' => 'Elven Wines',
                'category' => 'interests',
                'level' => 4,
            ],
        ]]);
        self::assertNotEmpty($character->getKnowledgeSkills());
    }

    /**
     * Test getting a character's native language knowledge skill.
     * @test
     */
    public function testGetKnowledgeSkillLanguage(): void
    {
        $character = new Character(['knowledgeSkills' => [
            [
                'name' => 'English',
                'category' => 'language',
                'level' => 'N',
                'specialization' => 'Spoken',
            ],
        ]]);
        self::assertNotEmpty($character->getKnowledgeSkills());
    }

    /**
     * Test getting a character's identities if they have none.
     * @test
     */
    public function testGetIdentitiesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getIdentities());
    }

    /**
     * Test getting a character's identities if they've got one.
     * @test
     */
    public function testGetIdentities(): void
    {
        $character = new Character([
            'identities' => [
                [
                    'id' => 0,
                    'name' => 'Altered Ego',
                    'notes' => 'Nothing of note.',
                    'sin' => 4,
                ],
            ],
        ]);
        self::assertNotEmpty($character->getIdentities());
    }

    /**
     * Test getting a character's martial arts styles if they have no style.
     * @test
     */
    public function testGetMartialArtsStyleNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getMartialArtsStyles());
    }

    /**
     * Test getting a character's martial arts styles if they have an invalid
     * style.
     * @test
     */
    public function testGetMartialArtsStyleInvalid(): void
    {
        $character = new Character([
            'martialArts' => [
                'styles' => [
                    'invalid',
                ],
            ],
        ]);
        self::assertEmpty($character->getMartialArtsStyles());
    }

    /**
     * Test getting a character's martial arts style.
     * @test
     */
    public function testGetMartialArtsStyles(): void
    {
        $character = new Character([
            'martialArts' => [
                'styles' => [
                    'aikido',
                ],
            ],
        ]);
        self::assertNotEmpty($character->getMartialArtsStyles());
    }

    /**
     * Test getting a character's martial arts techniques if they have no
     * technique.
     * @test
     */
    public function testGetMartialArtsTechniqueNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getMartialArtsTechniques());
    }

    /**
     * Test getting a character's martial arts techniques if they have an
     * invalid technique.
     * @test
     */
    public function testGetMartialArtsTechniqueInvalid(): void
    {
        $character = new Character([
            'martialArts' => [
                'techniques' => [
                    'invalid',
                ],
            ],
        ]);
        self::assertEmpty($character->getMartialArtsTechniques());
    }

    /**
     * Test getting a character's martial arts techniques.
     * @test
     */
    public function testGetMartialArtsTechniques(): void
    {
        $character = new Character([
            'martialArts' => [
                'techniques' => [
                    'constrictors-crush',
                ],
            ],
        ]);
        self::assertNotEmpty($character->getMartialArtsTechniques());
    }

    /**
     * Test getting the mentor spirit of a mundane character.
     * @test
     */
    public function testGetMentorSpiritMundane(): void
    {
        $character = new Character();
        self::assertNull($character->getMentorSpirit());
    }

    /**
     * Test getting the mentor spirit of an awakened character that has no
     * mentor spirit.
     * @test
     */
    public function testGetMentorSpiritNone(): void
    {
        $character = new Character([
            'magics' => [
                'powers' => [
                    'improved-sense-direction-sense',
                ],
            ],
        ]);
        self::assertNull($character->getMentorSpirit());
    }

    /**
     * Test trying to load the mentor spirit of a character with an invalid
     * spirit.
     * @test
     */
    public function testGetMentorSpiritInvalid(): void
    {
        $character = new Character([
            'magics' => [
                'mentorSpirit' => 'invalid',
            ],
        ]);
        self::assertNull($character->getMentorSpirit());
    }

    /**
     * Test getting the mentor spirit of a character that has one.
     * @test
     */
    public function testGetMentorSpirit(): void
    {
        $character = new Character([
            'magics' => [
                'mentorSpirit' => 'goddess',
            ],
        ]);
        self::assertInstanceOf(
            MentorSpirit::class,
            $character->getMentorSpirit()
        );
    }

    /**
     * Test getting the metatype of a character with no priorities.
     * @test
     */
    public function testGetMetatypeNoPriority(): void
    {
        $character = new Character();
        self::assertSame('unknown', $character->metatype);
    }

    /**
     * Test getting the metatype of a character with incomplete priorities.
     * @test
     */
    public function testGetMetatypeIncompletePriorities(): void
    {
        $character = new Character(['priorities' => []]);
        self::assertSame('unknown', $character->metatype);
    }

    /**
     * Test getting the metatype of a character with standard priorities.
     * @test
     */
    public function testGetMetatypeStandardPriorities(): void
    {
        $character = new Character([
            'priorities' => [
                'a' => 'attributes',
                'b' => 'skills',
                'c' => 'resources',
                'd' => 'metatype',
                'e' => 'magic',
                'metatype' => 'elf',
                'magic' => '',
                'gameplay' => 'established',
            ],
        ]);
        self::assertSame('elf', $character->metatype);
    }

    /**
     * Test getting the metatype of a character with sum-to-ten priorities.
     * @test
     */
    public function testGetMetatypeSumToTenPriorities(): void
    {
        $character = new Character([
            'priorities' => [
                'metatype' => 'human',
                'metatypePriority' => 'E',
                'magicPriority' => 'B',
                'attributePriority' => 'A',
                'skillPriority' => 'B',
                'resourcePriority' => 'E',
                'magic' => 'technomancer',
                'gameplay' => 'established',
            ],
        ]);
        self::assertSame('human', $character->metatype);
    }

    /**
     * Test getting the modified attribute for an invalid attribute.
     * @test
     */
    public function testGetModifiedAttributeInvalid(): void
    {
        $character = new Character();
        self::assertEquals(0, $character->getModifiedAttribute(''));
    }

    /**
     * Test getModifiedAttribute() with an armor mod or two, one of which
     * changes a stat.
     * @test
     */
    public function testGetModifiedAttributeArmorMod(): void
    {
        $armor = new Armor('armor-jacket');
        $armor->active = true;

        // Armor mod that doesn't have any effect.
        $armor->modifications[] = new ArmorModification('auto-injector');

        // Armor mod that also has no effect, but we're going to fake it.
        $mod = new ArmorModification('fire-resistance-2');
        $mod->effects = ['charisma' => 2];

        $armor->modifications[] = $mod;

        $armorArray = new ArmorArray();
        $armorArray[] = $armor;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->setMethods(['getArmor'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getArmor')->willReturn($armorArray);
        // @phpstan-ignore-next-line
        $character->charisma = 6;

        self::assertEquals(8, $character->getModifiedAttribute('charisma'));
    }

    /**
     * Test getModifiedAttribute() with an armor that changes it.
     * @test
     */
    public function testGetModifiedAttributeArmor(): void
    {
        $armor = new Armor('armor-jacket');
        $armor->active = true;
        $armor->effects = ['agility' => -2];
        $armorArray = new ArmorArray();
        $armorArray[] = $armor;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->setMethods(['getArmor'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getArmor')->willReturn($armorArray);
        // @phpstan-ignore-next-line
        $character->agility = 4;

        self::assertEquals(2, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test that inactive armor does not have effects.
     * @test
     */
    public function testGetModifiedAttributeArmorInactive(): void
    {
        $armor = new Armor('armor-jacket');
        $armor->active = false;
        $armor->effects = ['agility' => -2];
        $armorArray = new ArmorArray();
        $armorArray[] = $armor;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->setMethods(['getArmor'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getArmor')->willReturn($armorArray);
        // @phpstan-ignore-next-line
        $character->agility = 4;

        self::assertEquals(4, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test that active armor with effects for different attribute don't change.
     * @test
     */
    public function testGetModifiedAttributeArmorOtherAttribute(): void
    {
        $armor = new Armor('armor-jacket');
        $armor->active = true;
        $armor->effects = ['strength' => -2];

        $mod = new ArmorModification('fire-resistance-2');
        $mod->effects = ['charisma' => 2];
        $armor->modifications[] = $mod;

        $armorArray = new ArmorArray();
        $armorArray[] = $armor;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->setMethods(['getArmor'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getArmor')->willReturn($armorArray);
        // @phpstan-ignore-next-line
        $character->agility = 4;

        self::assertEquals(4, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test getting an attribute modified by a quality.
     * @test
     */
    public function testGetModifiedAttributeQuality(): void
    {
        $qualityDifferentEffect = new Quality('aptitude-alchemy');

        // Modify the hquality to have testable effect.
        $qualityWithEffect = new Quality('lucky');
        $qualityWithEffect->effects = ['agility' => 2];

        $qualities = new QualityArray();
        $qualities[] = $qualityDifferentEffect;
        $qualities[] = $qualityWithEffect;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQualities'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getQualities')->willReturn($qualities);
        // @phpstan-ignore-next-line
        $character->agility = 4;

        self::assertEquals(6, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test getting a character's qualities if they don't have any.
     * @test
     */
    public function testGetQualitiesEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getQualities());
    }

    /**
     * Test getting a character's qualities if they have one that is invalid.
     * @test
     */
    public function testGetQualitiesInvalid(): void
    {
        $character = new Character(['qualities' => [['id' => 'not-found']]]);
        self::assertEmpty($character->getQualities());
    }

    /**
     * Test getting a character's qualities.
     * @test
     */
    public function testGetQualities(): void
    {
        $character = new Character([
            'qualities' => [
                ['id' => 'lucky'],
                ['id' => 'addiction-mild', 'addiction' => 'Alcohol'],
            ],
        ]);
        self::assertCount(2, $character->getQualities());
    }

    /**
     * Test getting a character's skills if they have none.
     * @test
     */
    public function testGetSkillsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSkills());
    }

    /**
     * Test getting a character's skills if they have one that is invalid.
     * @test
     */
    public function testGetSkillsInvalid(): void
    {
        $character = new Character([
            'skills' => [
                ['id' => 'not-found', 'level' => 6],
            ],
        ]);
        self::assertEmpty($character->getSkills());
    }

    /**
     * Test getting a character's skills.
     * @test
     */
    public function testGetSkills(): void
    {
        $character = new Character([
            'skills' => [
                ['id' => 'automatics', 'level' => 6],
                ['id' => 'hacking', 'level' => 5, 'specialization' => 'foo'],
            ],
        ]);
        self::assertCount(2, $character->getSkills());
    }

    /**
     * Test getting a character's skill groups if they have none.
     * @test
     */
    public function testGetSkillGroupsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSkillGroups());
    }

    /**
     * Test getting a character's skill groups with an invalid group.
     * @test
     */
    public function testGetSkillGroupsInvalid(): void
    {
        $character = new Character([
            'skillGroups' => [
                'not-found' => 6,
            ],
        ]);
        self::assertEmpty($character->getSkillGroups());
    }

    /**
     * Test getting a character's skill groups.
     * @test
     */
    public function testGetSkillGroups(): void
    {
        $character = new Character([
            'skillGroups' => [
                'firearms' => 6,
            ],
        ]);
        $groups = $character->getSkillGroups();
        self::assertNotEmpty($groups);
        self::assertInstanceOf(ActiveSkill::class, $groups[0]->skills[0]);
    }

    /**
     * Test getting a character's spells if they are mundane.
     * @test
     */
    public function testGetSpellsMundane(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSpells());
    }

    /**
     * Test getting a character's spells if they're awakened but have no spells.
     * @test
     */
    public function testGetSpellsNone(): void
    {
        $character = new Character([
            'magics' => [],
        ]);
        self::assertEmpty($character->getSpells());
    }

    /**
     * Test getting a character's spells if they have a spell, but it's invalid.
     * @test
     */
    public function testGetSpellsInvalid(): void
    {
        $character = new Character([
            'magics' => [
                'spells' => [
                    'invalid',
                ],
            ],
        ]);
        self::assertEmpty($character->getSpells());
    }

    /**
     * Test getting a character's spells if they have one.
     * @test
     */
    public function testGetSpells(): void
    {
        $character = new Character([
            'magics' => [
                'spells' => [
                    'control-emotions',
                ],
            ],
        ]);
        self::assertNotEmpty($character->getSpells());
    }

    /**
     * Test getting spirits for a mundane character.
     * @test
     */
    public function testGetSpiritsMundane(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSpirits());
    }

    /**
     * Test getting spirits for a magical character without spirits.
     * @test
     */
    public function testGetSpiritsNoSpirits(): void
    {
        $character = new Character(['magics' => []]);
        self::assertEmpty($character->getSpirits());
    }

    /**
     * Test getting spirits for character with an invalid spirit.
     * @test
     */
    public function testGetSpiritsInvalid(): void
    {
        $character = new Character(['magics' => [
            'spirits' => [
                ['id' => 'invalid'],
            ],
        ]]);
        self::assertEmpty($character->getSpirits());
    }

    /**
     * Test getting spirits for a character with a valid spirit.
     * @test
     */
    public function testGetSpirits(): void
    {
        $character = new Character(['magics' => [
            'spirits' => [
                [
                    'id' => 'air',
                    'force' => 6,
                    'services' => 3,
                ],
            ],
        ]]);
        self::assertNotEmpty($character->getSpirits());
    }

    /**
     * Test getting the character's sprites if the have none.
     * @test
     */
    public function testGetSpritesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSprites());
    }

    /**
     * Test getting the character's sprites if they have an invalid one.
     * @test
     */
    public function testGetSpritesInvalid(): void
    {
        $character = new Character(['technomancer' => [
            'sprites' => ['invalid'],
        ]]);
        self::assertEmpty($character->getSprites());
    }

    /**
     * Test getting the character's sprites if they've got one.
     * @test
     */
    public function testGetSprites(): void
    {
        $character = new Character(['technomancer' => [
            'sprites' => ['courier'],
        ]]);
        self::assertNotEmpty($character->getSprites());
    }

    /**
     * Test getting a mundane character's magical tradition.
     * @test
     */
    public function testGetTraditionMundane(): void
    {
        $character = new Character();
        self::assertNull($character->getTradition());
    }

    /**
     * Test getting a character's magical tradition if they have an invalid one.
     * @test
     */
    public function testGetTraditionInvalid(): void
    {
        $character = new Character([
            'magics' => [
                'tradition' => 'invalid',
            ],
        ]);
        self::assertNull($character->getTradition());
    }

    /**
     * Test getting a character's magical tradition.
     * @test
     */
    public function testGetTradition(): void
    {
        $character = new Character([
            'magics' => [
                'tradition' => 'norse',
            ],
        ]);
        self::assertNotNull($character->getTradition());
    }

    /**
     * Test getting vehicles for a character without any.
     * @test
     */
    public function testGetVehiclesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getVehicles());
    }

    /**
     * Test getting vehicles for a character with an invalid ride.
     * @test
     */
    public function testGetVehiclesInvalid(): void
    {
        $character = new Character(['vehicles' => [
            ['id' => 'invalid'],
        ]]);
        self::assertEmpty($character->getVehicles());
    }

    /**
     * Test getting vehicles for a character with a vehicle.
     * @test
     */
    public function testGetVehicles(): void
    {
        $character = new Character(['vehicles' => [
            ['id' => 'dodge-scoot'],
        ]]);
        self::assertNotEmpty($character->getVehicles());
    }

    /**
     * Test getting weapons for an unarmed character.
     * @test
     */
    public function testGetWeaponsUnarmed(): void
    {
        $character = new Character();
        self::assertEmpty($character->getWeapons());
    }

    /**
     * Test getting weapons for a character armed with an invalid weapon.
     * @test
     */
    public function testGetWeaponsInvalid(): void
    {
        $character = new Character(['weapons' => [['id' => 'invalid']]]);
        self::assertEmpty($character->getWeapons());
    }

    /**
     * Test getting weapons for an armed character.
     * @test
     */
    public function testGetWeapons(): void
    {
        $character = new Character(['weapons' => [['id' => 'ak-98']]]);
        self::assertNotEmpty($character->getWeapons());
    }
}
