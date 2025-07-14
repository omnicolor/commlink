<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\ValueObjects;

use Modules\Dnd5e\ValueObjects\CharacterLevel;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CharacterLevelTest extends TestCase
{
    /**
     * @return array<int, array<int, int>>
     */
    public static function experienceProvider(): array
    {
        return [
            [0, 1],
            [299, 1],
            [300, 2],
            [900, 3],
            [2_701, 4],
            [6_500, 5],
            [14_000, 6],
            [23_999, 7],
            [34_000, 8],
            [48_000, 9],
            [64_000, 10],
            [85_000, 11],
            [100_000, 12],
            [120_000, 13],
            [140_000, 14],
            [165_000, 15],
            [195_000, 16],
            [225_000, 17],
            [265_000, 18],
            [349_999, 19],
            [355_000, 20],
        ];
    }

    #[DataProvider('experienceProvider')]
    public function testLevelFromExperience(int $experience, int $expected_level): void
    {
        $level = new CharacterLevel($experience);
        self::assertSame($expected_level, $level->level);
    }

    public function testExperienceTooLow(): void
    {
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Experience must be a positive integer');
        new CharacterLevel(-1);
    }

    /**
     * @return array<int, array<int, int>>
     */
    public static function levelProvider(): array
    {
        return [
            [0, 1],
            [300, 2],
            [900, 3],
            [2_700, 4],
            [6_500, 5],
            [14_000, 6],
            [23_000, 7],
            [34_000, 8],
            [48_000, 9],
            [64_000, 10],
            [85_000, 11],
            [100_000, 12],
            [120_000, 13],
            [140_000, 14],
            [165_000, 15],
            [195_000, 16],
            [225_000, 17],
            [265_000, 18],
            [305_000, 19],
            [355_000, 20],
        ];
    }

    #[DataProvider('levelProvider')]
    public function testMakeLevel(int $expected_experience, int $level): void
    {
        $level_object = CharacterLevel::make($level);
        self::assertSame($expected_experience, $level_object->experience);
        self::assertSame($level, $level_object->level);
    }

    public function testLevelTooLow(): void
    {
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Level must be between 1 and 20');
        CharacterLevel::make(0);
    }

    public function testLevelTooHigh(): void
    {
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Level must be between 1 and 20');
        CharacterLevel::make(21);
    }

    public function testToString(): void
    {
        $level = CharacterLevel::make(1);
        self::assertSame('1', (string)$level);

        $level = CharacterLevel::make(20);
        self::assertSame('20', (string)$level);
    }
}
