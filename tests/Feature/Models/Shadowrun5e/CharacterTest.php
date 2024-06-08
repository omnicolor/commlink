<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\ActiveSkill;
use App\Models\Shadowrun5e\Armor;
use App\Models\Shadowrun5e\ArmorArray;
use App\Models\Shadowrun5e\ArmorModification;
use App\Models\Shadowrun5e\Augmentation;
use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\KarmaLogEntry;
use App\Models\Shadowrun5e\KnowledgeSkill;
use App\Models\Shadowrun5e\MentorSpirit;
use App\Models\Shadowrun5e\Quality;
use App\Models\Shadowrun5e\QualityArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class CharacterTest extends TestCase
{
    /**
     * Test displaying the character as a string just shows their handle.
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
     */
    public function testHiddenId(): void
    {
        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::assertNotNull($character->_id);
        $character->delete();
    }

    /**
     * Test getting the character's ID.
     */
    public function testGetId(): void
    {
        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::assertNotNull($character->id);
        self::assertSame($character->_id, $character->id);
        $character->delete();
    }

    /**
     * Test getting a character's adept powers if they don't have any.
     */
    public function testGetAdeptPowersEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getAdeptPowers());
    }

    /**
     * Test getting a character's adept powers if they have an invalid power.
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
     */
    public function testGetArmorEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getArmor());
    }

    /**
     * Test getting a character's armor if they have one that is invalid.
     */
    public function testGetArmorInvalid(): void
    {
        $character = new Character(['armor' => [['id' => 'not-found']]]);
        self::assertEmpty($character->getArmor());
    }

    /**
     * Test getting a character's armor.
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
     * Provide different raw armor settings and expected armor value.
     * @return array<string, array<int, array<int, array<string, array<int, string>|bool|string>>|int>>
     */
    public static function armorValueProvider(): array
    {
        return [
            'Has armor, but not active' => [
                [
                    ['id' => 'armor-jacket'],
                    ['id' => 'berwick-suit'],
                ],
                0,
            ],
            'Has no armor' => [
                [],
                0,
            ],
            'Active armor' => [
                [
                    ['id' => 'armor-jacket', 'active' => true],
                ],
                12,
            ],
            'Active stackable armor' => [
                [
                    ['id' => 'berwick-suit', 'active' => true],
                    ['id' => 'ballistic-mask', 'active' => true],
                    ['id' => 'armor-jacket'],
                ],
                11,
            ],
            'Active armor with modification' => [
                [
                    [
                        'id' => 'berwick-suit',
                        'active' => true,
                        'modifications' => ['gel-packs'],
                    ],
                    ['id' => 'ballistic-mask', 'active' => true],
                ],
                13,
            ],
        ];
    }

    /**
     * Test getting a character's armor value.
     * @param array<int, array<string, array<int, string>|bool|string>> $armor
     */
    #[DataProvider('armorValueProvider')]
    public function testGetArmorValue(array $armor, int $expected): void
    {
        $character = new Character(['armor' => $armor]);
        self::assertSame($expected, $character->getArmorValue());
    }

    /**
     * Test getting the character's astral limit for a mundane character.
     */
    public function testAstralLimitMundane(): void
    {
        $character = new Character();
        $character->magic = 0;
        $character->body = 1;
        $character->agility = 2;
        $character->reaction = 3;
        $character->strength = 4;
        $character->willpower = 5;
        $character->logic = 6;
        $character->intuition = 7;
        $character->charisma = 8;
        self::assertEquals(0, $character->astral_limit);
    }

    /**
     * Test getting the astral limit for an awakened character with a higher
     * social limit than mental limit.
     */
    public function testGetAstralLimitUsesSocialLimit(): void
    {
        $character = new Character();
        $character->magic = 6;
        $character->body = 1;
        $character->agility = 2;
        $character->reaction = 3;
        $character->strength = 4;
        $character->willpower = 5;
        $character->logic = 6;
        $character->intuition = 7;
        $character->charisma = 8;
        // Essence should be 6, added to Willpower 5 and twice Charisma 8 all
        // divided by 3: (6+5+(8*2))/3 = 26/3 = 8.6, rounded up.
        self::assertEquals(9, $character->astral_limit);
    }

    /**
     * Test getting the astral limit for an awakened character with a higher
     * mental limit than social limit.
     */
    public function testGetAstralLimitUsesMentalLimit(): void
    {
        $character = new Character();
        $character->magic = 6;
        $character->body = 1;
        $character->agility = 2;
        $character->reaction = 3;
        $character->strength = 4;
        $character->willpower = 5;
        $character->logic = 6;
        $character->intuition = 7;
        $character->charisma = 1;
        // Should be twice Logic 6 plus Intuition 7 plus Willpower 5 all divided
        // by 3: ((6*2)+7+5)/3 = 24/3 = 8, rounded up
        self::assertEquals(8, $character->astral_limit);
    }

    /**
     * Test getting a character's augmentations if they don't have any.
     */
    public function testGetAugmentationsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getAugmentations());
    }

    /**
     * Test getting a character's augmentations if they have an invalid one.
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
     * Test getting a character's contacts if they have none.
     */
    public function testGetContactsNone(): void
    {
        $character = new Character([]);
        self::assertEmpty($character->getContacts());
    }

    /**
     * Test getting a character's contacts.
     */
    public function testGetContacts(): void
    {
        $character = new Character([
            'contacts' => [
                [
                    'archetype' => 'Fixer',
                    'connection' => 1,
                    'id' => 42,
                    'name' => 'Contact McContactFace',
                    'loyalty' => 2,
                ],
            ],
        ]);
        self::assertNotEmpty($character->getContacts());
    }

    /**
     * Test getting a character's complex forms if they have none.
     */
    public function testGetComplexFormsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getComplexForms());
    }

    /**
     * Test getting a character's complex forms if they've got an invalid one.
     */
    public function testGetComplexFormsInvalid(): void
    {
        $character = new Character(['complexForms' => ['invalid']]);
        self::assertEmpty($character->getComplexForms());
    }

    /**
     * Test getting a character's complex forms.
     */
    public function testGetComplexForms(): void
    {
        $character = new Character(['complexForms' => ['cleaner']]);
        self::assertNotEmpty($character->getComplexForms());
    }

    /**
     * Test getting a character's composure derived attribute.
     */
    public function testGetComposure(): void
    {
        $character = new Character();
        self::assertSame(0, $character->composure);
        $character->body = 1;
        $character->agility = 2;
        $character->reaction = 3;
        $character->strength = 4;
        $character->willpower = 5;
        $character->logic = 6;
        $character->intuition = 7;
        $character->charisma = 8;
        self::assertSame(5 + 8, $character->composure);
    }

    /**
     * Test that essence goes down with augmentations.
     */
    public function testEssenceLoss(): void
    {
        $character = new Character();
        self::assertEquals(6, $character->essence);
        $character->augmentations = [['id' => 'bone-lacing-aluminum']];
        self::assertEquals(5, $character->essence);
    }

    /**
     * Test essence going down with a different grade of augmentation.
     */
    public function testEssenceLossWithGrade(): void
    {
        $character = new Character();
        self::assertEquals(6, $character->essence);
        $character->augmentations = [[
            'id' => 'bone-lacing-aluminum',
            'grade' => Augmentation::GRADE_ALPHA,
        ]];
        self::assertEquals(5.2, $character->essence);
    }

    /**
     * Test essence loss for a character with biocompatibility (cyberware).
     */
    public function testEssenceLossWithBiocompatibilityCyberware(): void
    {
        $character = new Character();
        self::assertEquals(6, $character->essence);

        $character->augmentations = [[
            'id' => 'bone-lacing-aluminum',
        ]];

        self::assertEquals(5, $character->essence);

        $character->qualities = [[
            'id' => 'biocompatibility-cyberware',
        ]];
        self::assertEquals(5.1, $character->essence);
    }

    /**
     * Test essence loss for a character with biocompatibility (bioware).
     */
    public function testEssenceLossWithBiocompatibilityBioware(): void
    {
        $character = new Character();
        self::assertEquals(6, $character->essence);

        $character->augmentations = [[
            'id' => 'bone-density-augmentation-2',
        ]];

        self::assertEquals(5.4, $character->essence);

        $character->qualities = [[
            'id' => 'biocompatibility-bioware',
        ]];
        self::assertEquals(5.46, $character->essence);
    }

    /**
     * Test getting a character's gear if they have none.
     */
    public function testGetGearEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getGear());
    }

    /**
     * Test getting a character's gear if they've only got invalid gear.
     */
    public function testGetGearInvalid(): void
    {
        $character = new Character(['gear' => [['id' => 'invalid']]]);
        self::assertEmpty($character->getGear());
    }

    /**
     * Test getting a character's gear.
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
     * Test getting a character's judge intentions derived attribute.
     */
    public function testGetJudgeIntentions(): void
    {
        $character = new Character();
        self::assertSame(0, $character->judge_intentions);
        $character->body = 1;
        $character->agility = 2;
        $character->reaction = 3;
        $character->strength = 4;
        $character->willpower = 5;
        $character->logic = 6;
        $character->intuition = 7;
        $character->charisma = 8;
        self::assertSame(7 + 8, $character->judge_intentions);
    }

    /**
     * Test getting a character's knowledge skills if they're dumB.
     */
    public function testGetKnowledgeSkillsNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getKnowledgeSkills());
    }

    /**
     * Test getting the character's knowledge skills if they have an invalid
     * skill category.
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
     * Test filtering knowledge skills to just non-languages.
     */
    public function testGetKnowledgeSkillsFilterOnlyKnowledge(): void
    {
        $character = new Character(['knowledgeSkills' => [
            [
                'name' => 'Elven Wines',
                'category' => 'interests',
                'level' => 4,
            ],
        ]]);
        self::assertCount(1, $character->getKnowledgeSkills());
        self::assertEmpty($character->getKnowledgeSkills(onlyLanguages: true));
        self::assertCount(1, $character->getKnowledgeSkills(onlyKnowledges: true));
    }

    /**
     * Test filtering knowledge skills to just languages.
     */
    public function testGetKnowledgesFilterOnlyLanguages(): void
    {
        $character = new Character(['knowledgeSkills' => [
            [
                'name' => 'English',
                'category' => 'language',
                'level' => 'N',
                'specialization' => 'Spoken',
            ],
        ]]);
        self::assertCount(1, $character->getKnowledgeSkills());
        self::assertEmpty($character->getKnowledgeSkills(onlyKnowledges: true));
        self::assertCount(1, $character->getKnowledgeSkills(onlyLanguages: true));
    }

    /**
     * Test filtering knowledge skills.
     */
    public function testGetKnowledgeSkillsFiltered(): void
    {
        $character = new Character(['knowledgeSkills' => [
            [
                'name' => 'Elven Wines',
                'category' => 'interests',
                'level' => 4,
            ],
            [
                'name' => 'English',
                'category' => 'language',
                'level' => 'N',
                'specialization' => 'Spoken',
            ],
        ]]);
        self::assertCount(2, $character->getKnowledgeSkills());
        self::assertCount(1, $character->getKnowledgeSkills(onlyKnowledges: true));
        self::assertCount(1, $character->getKnowledgeSkills(onlyLanguages: true));
    }

    /**
     * Test getting a character's identities if they have none.
     */
    public function testGetIdentitiesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getIdentities());
    }

    /**
     * Test getting a character's identities if they've got one.
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
     * Test getting a character's lift/carry derived stat.
     */
    public function testGetLiftCarry(): void
    {
        $character = new Character();
        self::assertSame(0, $character->composure);
        $character->body = 1;
        $character->agility = 2;
        $character->reaction = 3;
        $character->strength = 4;
        $character->willpower = 5;
        $character->logic = 6;
        $character->intuition = 7;
        $character->charisma = 8;
        self::assertSame(1 + 4, $character->lift_carry);
    }

    /**
     * Test getting limits for skills.
     */
    public function testGetSkillLimit(): void
    {
        $character = new Character();
        $character->magic = 6;
        $character->body = 1;
        $character->agility = 2;
        $character->reaction = 3;
        $character->strength = 4;
        $character->willpower = 5;
        $character->logic = 6;
        $character->intuition = 7;
        $character->charisma = 8;

        $skill = new ActiveSkill('astral-combat', 3);
        // Defaults to weapon.
        self::assertSame('W', $character->getSkillLimit($skill));
        $skill->limit = 'astral';
        self::assertSame('9', $character->getSkillLimit($skill));
        $skill->limit = 'force';
        self::assertSame('F', $character->getSkillLimit($skill));
        $skill->limit = 'handling';
        self::assertSame('H', $character->getSkillLimit($skill));
        $skill->limit = 'level';
        self::assertSame('L', $character->getSkillLimit($skill));
        $skill->limit = 'matrix';
        self::assertSame('M', $character->getSkillLimit($skill));
        $skill->limit = 'mental';
        self::assertSame('8', $character->getSkillLimit($skill));
        $skill->limit = 'physical';
        self::assertSame('4', $character->getSkillLimit($skill));
        $skill->limit = 'social';
        self::assertSame('9', $character->getSkillLimit($skill));
        $skill->limit = 'unknown';
        self::assertSame('?', $character->getSkillLimit($skill));
    }

    /**
     * Test getting the character's karma log if they're new.
     */
    public function testGetKarmaLogNewCharacter(): void
    {
        $character = new Character([
            'priorities' => [
                'a' => 'attributes',
                'b' => 'skills',
                'c' => 'resources',
                'd' => 'metatype',
                'e' => 'magic',
                'magic' => null,
                'metatype' => 'human',
                'gameplay' => 'established',
            ],
            'qualities' => [
                ['id' => 'lucky'],
            ],
        ]);
        $log = $character->getKarmaLog();
        self::assertCount(2, $log);

        /** @var KarmaLogEntry */
        $entry = $log[1];
        self::assertSame('Add quality Lucky', $entry->description);
        self::assertSame(-12, $entry->karma);
        self::assertNull($entry->realDate);
        self::assertNull($entry->gameDate);
    }

    /**
     * Test getting the character's karma log if they've got a log in Mongo.
     */
    public function testGetKarmaLogOldCharacter(): void
    {
        $character = new Character([
            'priorities' => [
                'a' => 'attributes',
                'b' => 'skills',
                'c' => 'resources',
                'd' => 'metatype',
                'e' => 'magic',
                'magic' => null,
                'metatype' => 'human',
                'gameplay' => 'established',
            ],
            'karmaLog' => [
                [
                    'description' => 'Test entry',
                    'karma' => 42,
                    'realDate' => '2020-03-24',
                    'gameDate' => '2080-04-01',
                ],
            ],
            // Red herring to make sure this isn't getting put into the log.
            'qualities' => [
                ['id' => 'lucky'],
            ],
        ]);
        $log = $character->getKarmaLog();
        self::assertCount(1, $log);

        /** @var KarmaLogEntry */
        $log = $log[0];
        self::assertSame('Test entry', $log->description);
        self::assertSame(42, $log->karma);
        // @phpstan-ignore-next-line
        self::assertSame('2020-03-24', $log->realDate->format('Y-m-d'));
        // @phpstan-ignore-next-line
        self::assertSame('2080-04-01', $log->gameDate->format('Y-m-d'));
    }

    /**
     * Test getting limit for a knowledge skill.
     */
    public function testGetKnowledgeSkillLimit(): void
    {
        $character = new Character([
            'intuition' => 4,
            'logic' => 2,
            'willpower' => 4,
        ]);
        $skill = new KnowledgeSkill('astral-combat', 'academic', 3);
        self::assertSame('4', $character->getSkillLimit($skill));
    }

    /**
     * Test getting a limit for a skill that uses a calculatable limit that has
     * a quality that updates it.
     */
    public function testGetSkillLimitUpdated(): void
    {
        $quality = new Quality('lucky');
        $quality->effects = ['limit-astral-combat' => 2];
        $qualities = new QualityArray();
        $qualities[] = $quality;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getQualities'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getQualities')->willReturn($qualities);
        $character->body = 1;
        $character->reaction = 3;
        $character->strength = 4;

        $skill = new ActiveSkill('astral-combat', 3);
        $skill->limit = 'physical';
        self::assertSame('6', $character->getSkillLimit($skill));
    }

    /**
     * Test getting a character's martial arts styles if they have no style.
     */
    public function testGetMartialArtsStyleNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getMartialArtsStyles());
    }

    /**
     * Test getting a character's martial arts styles if they have an invalid
     * style.
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
     */
    public function testGetMartialArtsTechniqueNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getMartialArtsTechniques());
    }

    /**
     * Test getting a character's martial arts techniques if they have an
     * invalid technique.
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
     * Test getting a character's memory derived attribute.
     */
    public function testGetMemory(): void
    {
        $character = new Character();
        self::assertSame(0, $character->memory);
        $character->body = 1;
        $character->agility = 2;
        $character->reaction = 3;
        $character->strength = 4;
        $character->willpower = 5;
        $character->logic = 6;
        $character->intuition = 7;
        $character->charisma = 8;
        self::assertSame(6 + 5, $character->memory);
    }

    /**
     * Test getting the mentor spirit of a mundane character.
     */
    public function testGetMentorSpiritMundane(): void
    {
        $character = new Character();
        self::assertNull($character->getMentorSpirit());
    }

    /**
     * Test getting the mentor spirit of an awakened character that has no
     * mentor spirit.
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
     */
    public function testGetMetatypeNoPriority(): void
    {
        $character = new Character();
        self::assertSame('unknown', $character->metatype);
    }

    /**
     * Test getting the metatype of a character with incomplete priorities.
     */
    public function testGetMetatypeIncompletePriorities(): void
    {
        $character = new Character(['priorities' => []]);
        self::assertSame('unknown', $character->metatype);
    }

    /**
     * Test getting the metatype of a character with standard priorities.
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
     */
    public function testGetModifiedAttributeInvalid(): void
    {
        $character = new Character();
        self::assertEquals(0, $character->getModifiedAttribute(''));
    }

    /**
     * Test getModifiedAttribute() with an armor mod or two, one of which
     * changes a stat.
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
            ->onlyMethods(['getArmor'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getArmor')->willReturn($armorArray);
        $character->charisma = 6;

        self::assertEquals(8, $character->getModifiedAttribute('charisma'));
    }

    /**
     * Test getModifiedAttribute() with an armor that changes it.
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
            ->onlyMethods(['getArmor'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getArmor')->willReturn($armorArray);
        $character->agility = 4;

        self::assertEquals(2, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test that inactive armor does not have effects.
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
            ->onlyMethods(['getArmor'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getArmor')->willReturn($armorArray);
        $character->agility = 4;

        self::assertEquals(4, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test a character's unmodified initiative score.
     */
    public function testInitiativeUnmodified(): void
    {
        $intuition = random_int(1, 8);
        $reaction = random_int(1, 8);

        $character = new Character([
            'intuition' => $intuition,
            'reaction' => $reaction,
        ]);

        self::assertSame($intuition + $reaction, $character->initiative_score);
    }

    /**
     * Test a character's initiative modified by an adept power.
     */
    public function testInitiativeWithPower(): void
    {
        $intuition = random_int(1, 8);
        $reaction = random_int(1, 8);

        $character = new Character([
            'intuition' => $intuition,
            'magics' => [
                'powers' => [
                    'adrenaline-boost-1',
                ],
            ],
            'reaction' => $reaction,
        ]);

        self::assertSame($intuition + $reaction + 2, $character->initiative_score);
    }

    /**
     * Test that an unmodified character gets one initiative die.
     */
    public function testInitiativeDiceUnmodified(): void
    {
        $character = new Character();
        self::assertSame(1, $character->initiative_dice);
    }

    /**
     * Test that a character with a synaptic booster gets an additional
     * initiative die.
     */
    public function testInitiativeDiceModifiedByCyberware(): void
    {
        $character = new Character([
            'augmentations' => [
                ['id' => 'synaptic-booster-1'],
            ],
        ]);
        self::assertSame(2, $character->initiative_dice);
    }

    /**
     * Test that a character with the improved reflexes adept power gets an
     * additional initiative die.
     */
    public function testInitiativeDiceModifiedByPower(): void
    {
        $character = new Character([
            'magics' => [
                'powers' => [
                    'improved-reflexes-3',
                ],
            ],
        ]);
        self::assertSame(4, $character->initiative_dice);
    }

    /**
     * Test that active armor with effects for different attribute don't change.
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
            ->onlyMethods(['getArmor'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getArmor')->willReturn($armorArray);
        $character->agility = 4;

        self::assertEquals(4, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test getting an attribute modified by a quality.
     */
    public function testGetModifiedAttributeQuality(): void
    {
        $qualityDifferentEffect = new Quality('aptitude-alchemy');

        // Create a quality with a testable effect.
        $qualityWithEffect = new Quality('lucky');
        $qualityWithEffect->effects = ['agility' => 2];

        $qualities = new QualityArray();
        $qualities[] = $qualityDifferentEffect;
        $qualities[] = $qualityWithEffect;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getQualities'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getQualities')->willReturn($qualities);
        $character->agility = 4;

        self::assertEquals(6, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test getting an attribute with a broken effect.
     */
    public function testGetModifiedAttributeBrokenEffect(): void
    {
        // Create a quality with a broken effect.
        $quality = new Quality('lucky');
        // @phpstan-ignore-next-line
        $quality->effects = ['distinctive-style'];

        $qualities = new QualityArray();
        $qualities[] = $quality;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getQualities'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getQualities')->willReturn($qualities);
        $character->agility = 4;

        self::assertEquals(4, $character->getModifiedAttribute('agility'));
    }

    /**
     * Test getting the character's physical limit when unaugmented.
     */
    public function testUnaugmentedPhysicalLimit(): void
    {
        $character = new Character([
            'strength' => 4,
            'body' => 4,
            'reaction' => 4,
        ]);
        self::assertEquals(6, $character->physical_limit);
    }

    /**
     * Test getting the character's physical limit with a quality that changes
     * it.
     */
    public function testPhysicalLimitAugmented(): void
    {
        // Create a quality with a testable effect.
        $quality = new Quality('lucky');
        $quality->effects = ['physical-limit' => 2];

        $qualities = new QualityArray();
        $qualities[] = $quality;

        $character = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getQualities'])
            ->setConstructorArgs([['system' => 'shadowrun5e']])
            ->getMock();
        $character->method('getQualities')->willReturn($qualities);
        $character->body = 4;
        $character->reaction = 4;
        $character->strength = 4;

        self::assertEquals(8, $character->physical_limit);
    }

    /**
     * Test getting a character's qualities if they don't have any.
     */
    public function testGetQualitiesEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getQualities());
    }

    /**
     * Test getting a character's qualities if they have one that is invalid.
     */
    public function testGetQualitiesInvalid(): void
    {
        $character = new Character(['qualities' => [['id' => 'not-found']]]);
        self::assertEmpty($character->getQualities());
    }

    /**
     * Test getting a character's qualities.
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
     */
    public function testGetSkillsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSkills());
    }

    /**
     * Test getting a character's skills if they have one that is invalid.
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
     */
    public function testGetSkillGroupsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSkillGroups());
    }

    /**
     * Test getting a character's skill groups with an invalid group.
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
     * Test getting the character's soak pool if they have nothing to change
     * their base value.
     */
    public function testGetBaseSoak(): void
    {
        $character = new Character(['body' => 1]);
        self::assertSame(1, $character->soak);
    }

    /**
     * Test getting a character's soak pool if they have an augmentation that
     * increases their damage resistance.
     */
    public function testGetSoakWithBoneDensityAugmentation(): void
    {
        $character = new Character([
            'augmentations' => [
                // One with damage-resistance...
                ['id' => 'bone-density-augmentation-2'],
                // and one without.
                ['id' => 'damper'],
            ],
            'body' => 2,
        ]);
        self::assertSame(4, $character->soak);
    }

    /**
     * Test getting a character's soak pool if they've got a mentor spirit that
     * toughens them up.
     */
    public function testGetSoakWithBearMentorSpirit(): void
    {
        $character = new Character([
            'body' => 3,
            'magics' => [
                'mentorSpirit' => 'bear',
            ],
        ]);
        self::assertSame(5, $character->soak);
    }

    /**
     * Test getting a character's soak pool if they're a coward hiding behind
     * armor.
     */
    public function testGetSoakWithArmor(): void
    {
        $character = new Character([
            'armor' => [
                [
                    'active' => true,
                    'id' => 'armor-jacket',
                ],
            ],
            'body' => 3,
        ]);
        self::assertSame(15, $character->soak);
    }

    /**
     * Test getting a character's spells if they are mundane.
     */
    public function testGetSpellsMundane(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSpells());
    }

    /**
     * Test getting a character's spells if they're awakened but have no spells.
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
     */
    public function testGetSpiritsMundane(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSpirits());
    }

    /**
     * Test getting spirits for a magical character without spirits.
     */
    public function testGetSpiritsNoSpirits(): void
    {
        $character = new Character(['magics' => []]);
        self::assertEmpty($character->getSpirits());
    }

    /**
     * Test getting spirits for character with an invalid spirit.
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
     */
    public function testGetSpritesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSprites());
    }

    /**
     * Test getting the character's sprites if they have an invalid one.
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
     */
    public function testGetTraditionMundane(): void
    {
        $character = new Character();
        self::assertNull($character->getTradition());
    }

    /**
     * Test getting a character's magical tradition if they have an invalid one.
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
     */
    public function testGetVehiclesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getVehicles());
    }

    /**
     * Test getting vehicles for a character with an invalid ride.
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
     */
    public function testGetWeaponsUnarmed(): void
    {
        $character = new Character();
        self::assertEmpty($character->getWeapons());
    }

    /**
     * Test getting weapons for a character armed with an invalid weapon.
     */
    public function testGetWeaponsInvalid(): void
    {
        $character = new Character(['weapons' => [['id' => 'invalid']]]);
        self::assertEmpty($character->getWeapons());
    }

    /**
     * Test getting weapons for an armed character.
     */
    public function testGetWeapons(): void
    {
        $character = new Character(['weapons' => [['id' => 'ak-98']]]);
        self::assertNotEmpty($character->getWeapons());
    }

    /**
     * Test getting a character's melee defense.
     */
    public function testMeleeDefense(): void
    {
        $character = new Character([
            'augmentations' => [
                ['id' => 'synaptic-booster-1'],
            ],
            'intuition' => 1,
            'reaction' => 2,
        ]);
        self::assertSame(4, $character->melee_defense);
    }

    /**
     * Test getting a character's overflow monitor.
     */
    public function testOverflow(): void
    {
        $character = new Character(['body' => 5]);
        self::assertSame(5, $character->overflow_monitor);
    }

    /**
     * Test getting a character's physical damage monitor.
     */
    public function testPhysical(): void
    {
        $character = new Character(['body' => 3]);
        self::assertSame(10, $character->physical_monitor);
    }

    /**
     * Test getting a character's ranged defense.
     */
    public function testRangedDefense(): void
    {
        $character = new Character([
            'reaction' => 3,
            'intuition' => 4,
        ]);
        self::assertSame(7, $character->ranged_defense);
    }

    /**
     * Test getting a character's stun monitor.
     */
    public function testStun(): void
    {
        $character = new Character(['willpower' => 6]);
        self::assertSame(11, $character->stun_monitor);
    }
}
