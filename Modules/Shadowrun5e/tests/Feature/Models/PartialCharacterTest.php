<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class PartialCharacterTest extends TestCase
{
    public function testNewFromBuilder(): void
    {
        $character = new PartialCharacter([
            'handle' => 'Test SR5E character',
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);
        $character->save();

        $loaded = PartialCharacter::find($character->id);
        self::assertInstanceOf(PartialCharacter::class, $loaded);
        self::assertSame('Test SR5E character', $loaded->handle);
        $character->delete();
    }

    /**
     * Test seeing if a newly created partial character is awakened.
     */
    public function testAwakenedOnNewCharacter(): void
    {
        $character = new PartialCharacter();
        self::assertFalse($character->isMagicallyActive());
        self::assertFalse($character->isTechnomancer());
    }

    /**
     * Test awakened checks on a technomancer.
     */
    public function testTechnomancer(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'magic' => 'technomancer',
            ],
        ]);
        self::assertFalse($character->isMagicallyActive());
        self::assertTrue($character->isTechnomancer());
    }

    /**
     * Test awakened checks on a mage.
     */
    public function testMagician(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'magic' => 'magician',
            ],
        ]);
        self::assertTrue($character->isMagicallyActive());
        self::assertFalse($character->isTechnomancer());
    }

    /**
     * Test awakened checks on an adept.
     */
    public function testAdept(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'magic' => 'adept',
            ],
        ]);
        self::assertTrue($character->isMagicallyActive());
        self::assertFalse($character->isTechnomancer());
    }

    /**
     * Data provider for testing maximum attributes.
     * @return array<int, array<int, PartialCharacter|int|string>>
     */
    public static function maximumAttributeDataProvider(): array
    {
        $human = new PartialCharacter(['priorities' => ['metatype' => 'human']]);
        $elf = new PartialCharacter(['priorities' => ['metatype' => 'elf']]);
        $oldHuman = new PartialCharacter([
            'priorities' => ['metatype' => 'human'],
            'qualities' => [['id' => 'aged-2']],
        ]);
        $exceptionalHuman = new PartialCharacter([
            'priorities' => ['metatype' => 'human'],
            'qualities' => [['id' => 'exceptional-attribute-body']],
        ]);
        $luckyHuman = new PartialCharacter([
            'priorities' => ['metatype' => 'human'],
            'qualities' => [['id' => 'lucky']],
        ]);
        // And one with a quality that doesn't change anything.
        $unchanged = new PartialCharacter([
            'priorities' => ['metatype' => 'human'],
            'qualities' => [['id' => 'albinism-2']],
        ]);
        return [
            [$human, 'body', 6],
            [$human, 'agility', 6],
            [$human, 'reaction', 6],
            [$human, 'strength', 6],
            [$human, 'willpower', 6],
            [$human, 'logic', 6],
            [$human, 'intuition', 6],
            [$human, 'charisma', 6],
            [$human, 'edge', 7],
            [$elf, 'body', 6],
            [$elf, 'agility', 7],
            [$elf, 'reaction', 6],
            [$elf, 'strength', 6],
            [$elf, 'willpower', 6],
            [$elf, 'logic', 6],
            [$elf, 'intuition', 6],
            [$elf, 'charisma', 8],
            [$elf, 'edge', 6],
            [$oldHuman, 'body', 4],
            [$oldHuman, 'agility', 4],
            [$oldHuman, 'reaction', 4],
            [$oldHuman, 'strength', 4],
            [$exceptionalHuman, 'body', 7],
            [$luckyHuman, 'edge', 8],
            [$unchanged, 'body', 6],
            [$unchanged, 'agility', 6],
            [$unchanged, 'reaction', 6],
            [$unchanged, 'strength', 6],
            [$unchanged, 'willpower', 6],
            [$unchanged, 'logic', 6],
            [$unchanged, 'intuition', 6],
            [$unchanged, 'charisma', 6],
            [$unchanged, 'edge', 7],
        ];
    }

    /**
     * Test getting a character's maximum attributes.
     */
    #[DataProvider('maximumAttributeDataProvider')]
    public function testGetMaximumAttributes(
        PartialCharacter $character,
        string $attribute,
        int $maximum
    ): void {
        self::assertSame(
            $maximum,
            $character->getStartingMaximumAttribute($attribute)
        );
    }

    /**
     * Test validate on an empty partial character.
     * @test
     */
    public function testValidateEmpty(): void
    {
        $character = new PartialCharacter();
        $character->validate();
        self::assertSame(
            [
                'You must choose <a href="/characters/shadowrun5e/create/priorities">priorities</a>.',
                'You must choose a native language',
            ],
            $character->errors
        );
    }

    /**
     * Test validate on a partial character with no metatype.
     * @test
     */
    public function testValidateNoMetatype(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'a' => 'metatype',
            ],
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ],
        ]);
        $character->validate();
        self::assertSame(
            [
                'You must choose a <a href="/characters/shadowrun5e/create/priorities">metatype</a>.',
                'You must allocate all <a href="/characters/shadowrun5e/create/priorities">priorities</a>.',
            ],
            $character->errors
        );
    }

    /**
     * Test a normal priority character with too many native languages.
     * @test
     */
    public function testValidateTooManyNativeLanguages(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'a' => 'metatype',
                'b' => 'resources',
                'c' => 'magic',
                'd' => 'attributes',
                'e' => 'skills',
                'metatype' => 'human',
            ],
            'agility' => 6,
            'body' => 6,
            'charisma' => 6,
            'reaction' => 1,
            'strength' => 1,
            'willpower' => 1,
            'logic' => 1,
            'intuition' => 2,
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
                ['name' => 'Spanish', 'category' => 'language', 'level' => 'N'],
                ['name' => 'Orkish', 'category' => 'language', 'level' => 2],
            ],
        ]);
        $character->validate();
        self::assertSame(
            [
                'You can only have one native language',
            ],
            $character->errors
        );
    }

    /**
     * Test validating native languages with the bilingual quality.
     * @test
     */
    public function testValidateBilingual(): void
    {
        $character = new PartialCharacter([
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
                ['name' => 'Spanish', 'category' => 'language', 'level' => 'N'],
                ['name' => 'Orkish', 'category' => 'language', 'level' => 2],
            ],
            'priorities' => [
                'a' => 'metatype',
                'b' => 'resources',
                'c' => 'magic',
                'd' => 'attributes',
                'e' => 'skills',
                'metatype' => 'human',
            ],
            'agility' => 6,
            'body' => 6,
            'charisma' => 6,
            'reaction' => 1,
            'strength' => 1,
            'willpower' => 1,
            'logic' => 1,
            'intuition' => 2,
            'qualities' => [
                ['id' => 'bilingual'],
            ],
        ]);
        $character->validate();
        self::assertEmpty($character->errors);
    }

    /**
     * Test validating the bilingual quality without enough native languages.
     * @test
     */
    public function testValidateBilingualNotEnoughLanguages(): void
    {
        $character = new PartialCharacter([
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
                ['name' => 'Bars', 'category' => 'street', 'level' => 2],
            ],
            'priorities' => [
                'a' => 'metatype',
                'b' => 'resources',
                'c' => 'magic',
                'd' => 'attributes',
                'e' => 'skills',
                'metatype' => 'human',
            ],
            'agility' => 6,
            'body' => 6,
            'charisma' => 6,
            'reaction' => 1,
            'strength' => 1,
            'willpower' => 1,
            'logic' => 1,
            'intuition' => 2,
            'qualities' => [
                ['id' => 'bilingual'],
            ],
        ]);
        $character->validate();
        self::assertSame(
            ['You haven\'t chosen two native languages for your bilingual quality'],
            $character->errors
        );
    }

    /**
     * Test validating a sum-to-ten character that hasn't assigned all
     * priorities.
     * @test
     */
    public function testValidateSumToTenMissing(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'metatypePriority' => 'frank',
            ],
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ],
        ]);
        $character->validate();
        self::assertSame(
            [
                'You must choose a <a '
                    . 'href="/characters/shadowrun5e/create/priorities">metatype</a>.',
                'You must allocate the magic priority on the <a '
                    . 'href="/characters/shadowrun5e/create/priorities">priorities page</a>.',
                'You must allocate the attribute priority on the <a '
                    . 'href="/characters/shadowrun5e/create/priorities">priorities page</a>.',
                'You must allocate the skill priority on the <a '
                    . 'href="/characters/shadowrun5e/create/priorities">priorities page</a>.',
                'You must allocate the resource priority on the <a '
                    . 'href="/characters/shadowrun5e/create/priorities">priorities page</a>.',
                'You haven\'t allocated all sum-to-ten priority points.',
            ],
            $character->errors
        );
    }

    /**
     * Test validating a sum-to-ten character that has overspent.
     * @test
     */
    public function testValidateSumToTenOverspent(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'metatypePriority' => 'A',
                'magicPriority' => 'A',
                'attributePriority' => 'A',
                'skillPriority' => 'A',
                'resourcePriority' => 'A',
                'metatype' => 'elf',
            ],
            'agility' => 6,
            'body' => 6,
            'charisma' => 6,
            'reaction' => 1,
            'strength' => 1,
            'willpower' => 1,
            'logic' => 1,
            'intuition' => 2,
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ],
        ]);
        $character->validate();
        self::assertSame(
            [
                'You have allocated too many sum-to-ten priority points.',
            ],
            $character->errors
        );
    }

    /**
     * Test validating a sum-to-ten character that chose attributes correctly.
     * @test
     */
    public function testValidateSumToTen(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'metatypePriority' => 'A',
                'magicPriority' => 'B',
                'attributePriority' => 'C',
                'skillPriority' => 'D',
                'resourcePriority' => 'E',
                'metatype' => 'elf',
            ],
            'agility' => 6,
            'body' => 6,
            'charisma' => 6,
            'reaction' => 1,
            'strength' => 1,
            'willpower' => 1,
            'logic' => 1,
            'intuition' => 2,
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ],
        ]);
        $character->validate();
        self::assertEmpty($character->errors);
    }

    /**
     * Test validating a character with unspent attribute points.
     * @test
     */
    public function testValidateAttributesUnspent(): void
    {
        $character = new PartialCharacter([
            'priorities' => [
                'metatypePriority' => 'A',
                'magicPriority' => 'B',
                'attributePriority' => 'C',
                'skillPriority' => 'D',
                'resourcePriority' => 'E',
                'metatype' => 'elf',
            ],
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ],
        ]);
        $character->validate();
        self::assertSame(
            ['You have 16 unspent attribute points'],
            $character->errors,
        );
    }
}
