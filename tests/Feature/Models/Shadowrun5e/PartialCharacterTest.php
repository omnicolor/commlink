<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\PartialCharacter;
use Tests\TestCase;

/**
 * @group shadowrun
 * @group shadowrun5e
 * @group models
 * @small
 */
final class PartialCharacterTest extends TestCase
{
    /**
     * @test
     */
    public function testNewFromBuilder(): void
    {
        $character = new PartialCharacter(['handle' => 'Test SR5E character']);
        $character->save();

        $loaded = PartialCharacter::find($character->id);
        // @phpstan-ignore-next-line
        self::assertSame('Test SR5E character', $loaded->handle);
        $character->delete();
    }

    /**
     * Test seeing if a newly created partial character is awakened.
     * @test
     */
    public function testAwakenedOnNewCharacter(): void
    {
        $character = new PartialCharacter();
        self::assertFalse($character->isMagicallyActive());
        self::assertFalse($character->isTechnomancer());
    }

    /**
     * Test awakened checks on a technomancer.
     * @test
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
     * @test
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
     * @test
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
    public function maximumAttributeDataProvider(): array
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
     * @dataProvider maximumAttributeDataProvider
     * @param PartialCharacter $character
     * @param string $attribute
     * @param int $maximum
     * @test
     */
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
}
