<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Iterator;
use Modules\Shadowrun5e\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

use function sprintf;

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
     * @return Iterator<int, array<int, (int | PartialCharacter | string)>>
     */
    public static function maximumAttributeDataProvider(): Iterator
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
        yield [$human, 'body', 6];
        yield [$human, 'agility', 6];
        yield [$human, 'reaction', 6];
        yield [$human, 'strength', 6];
        yield [$human, 'willpower', 6];
        yield [$human, 'logic', 6];
        yield [$human, 'intuition', 6];
        yield [$human, 'charisma', 6];
        yield [$human, 'edge', 7];
        yield [$elf, 'body', 6];
        yield [$elf, 'agility', 7];
        yield [$elf, 'reaction', 6];
        yield [$elf, 'strength', 6];
        yield [$elf, 'willpower', 6];
        yield [$elf, 'logic', 6];
        yield [$elf, 'intuition', 6];
        yield [$elf, 'charisma', 8];
        yield [$elf, 'edge', 6];
        yield [$oldHuman, 'body', 4];
        yield [$oldHuman, 'agility', 4];
        yield [$oldHuman, 'reaction', 4];
        yield [$oldHuman, 'strength', 4];
        yield [$exceptionalHuman, 'body', 7];
        yield [$luckyHuman, 'edge', 8];
        yield [$unchanged, 'body', 6];
        yield [$unchanged, 'agility', 6];
        yield [$unchanged, 'reaction', 6];
        yield [$unchanged, 'strength', 6];
        yield [$unchanged, 'willpower', 6];
        yield [$unchanged, 'logic', 6];
        yield [$unchanged, 'intuition', 6];
        yield [$unchanged, 'charisma', 6];
        yield [$unchanged, 'edge', 7];
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
     * @return array<int, array<int, int|string>>
     */
    public static function unspentSumToTenAttributesProvider(): array
    {
        return [
            ['A', 'B', 'C', 'D', 'E', 16],
            ['B', 'C', 'D', 'E', 'A', 14],
            ['C', 'D', 'E', 'A', 'B', 12],
            ['D', 'E', 'A', 'B', 'C', 24],
            ['E', 'A', 'B', 'C', 'D', 20],
        ];
    }

    #[DataProvider('unspentSumToTenAttributesProvider')]
    public function testValidateAttributesUnspentSumToTen(
        string $metatype,
        string $magic,
        string $attribute,
        string $skill,
        string $resource,
        int $remaining,
    ): void {
        $character = new PartialCharacter([
            'priorities' => [
                'metatypePriority' => $metatype,
                'magicPriority' => $magic,
                'attributePriority' => $attribute,
                'skillPriority' => $skill,
                'resourcePriority' => $resource,
                'metatype' => 'elf',
            ],
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ],
        ]);
        $character->validate();
        self::assertSame(
            [sprintf('You have %d unspent attribute points', $remaining)],
            $character->errors,
        );
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    public static function unspentStandardAttributesProvider(): array
    {
        return [
            ['attributes', 'skills', 'magic', 'metatype', 'resources', 24],
            ['skills', 'magic', 'metatype', 'resources', 'attributes', 12],
            ['magic', 'metatype', 'resources', 'attributes', 'skills', 14],
            ['metatype', 'resources', 'attributes', 'skills', 'magic', 16],
            ['resources', 'attributes', 'skills', 'magic', 'metatype', 20],
        ];
    }

    #[DataProvider('unspentStandardAttributesProvider')]
    public function testValidateAttributesUnspentStandard(
        string $a,
        string $b,
        string $c,
        string $d,
        string $e,
        int $remaining,
    ): void {
        $character = new PartialCharacter([
            'priorities' => [
                'a' => $a,
                'b' => $b,
                'c' => $c,
                'd' => $d,
                'e' => $e,
                'metatype' => 'elf',
            ],
            'knowledgeSkills' => [
                ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ],
        ]);
        $character->validate();
        self::assertSame(
            [sprintf('You have %d unspent attribute points', $remaining)],
            $character->errors,
        );
    }
}
