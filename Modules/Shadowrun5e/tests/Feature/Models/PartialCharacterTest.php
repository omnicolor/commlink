<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Iterator;
use Modules\Shadowrun5e\Models\ActiveSkill;
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
     * @return array<int, array<int, int|string|null>>
     */
    public static function skillSortProvider(): array
    {
        return [
            // At low levels, the level is more important.
            [1, null, 2, null, 1],
            [2, null, 1, null, -1],
            [1, null, 1, null, 0],
            [1, 'test', 2, null, 1],
            [2, null, 1, 'test', -1],
            // At higher levels the specialization matters.
            [3, 'test', 3, null, 1],
            [3, null, 3, 'test', -1],
            [3, 'test', 3, 'test', 0],
            [3, 'test', 4, null, 1],
            [4, 'test', 3, 'test', -1],
        ];
    }

    #[DataProvider('skillSortProvider')]
    public function testSortSkills(
        int $a,
        string|null $a_specialization,
        int $b,
        string|null $b_specialization,
        int $expected,
    ): void {
        $a = new ActiveSkill('automatics', $a, $a_specialization);
        $b = new ActiveSkill('computer', $b, $b_specialization);
        self::assertSame($expected, PartialCharacter::sortSkills($a, $b));
    }
}
