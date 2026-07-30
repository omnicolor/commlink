<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\ValueObjects;

use Iterator;
use Modules\Dnd5e\ValueObjects\CharacterLevel;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CharacterLevelTest extends TestCase
{
    /**
     * @return Iterator<int, array<int, int>>
     */
    public static function experienceProvider(): Iterator
    {
        yield [0, 1];
        yield [299, 1];
        yield [300, 2];
        yield [900, 3];
        yield [2_701, 4];
        yield [6_500, 5];
        yield [14_000, 6];
        yield [23_999, 7];
        yield [34_000, 8];
        yield [48_000, 9];
        yield [64_000, 10];
        yield [85_000, 11];
        yield [100_000, 12];
        yield [120_000, 13];
        yield [140_000, 14];
        yield [165_000, 15];
        yield [195_000, 16];
        yield [225_000, 17];
        yield [265_000, 18];
        yield [349_999, 19];
        yield [355_000, 20];
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
     * @return Iterator<int, array<int, int>>
     */
    public static function levelProvider(): Iterator
    {
        yield [0, 1];
        yield [300, 2];
        yield [900, 3];
        yield [2_700, 4];
        yield [6_500, 5];
        yield [14_000, 6];
        yield [23_000, 7];
        yield [34_000, 8];
        yield [48_000, 9];
        yield [64_000, 10];
        yield [85_000, 11];
        yield [100_000, 12];
        yield [120_000, 13];
        yield [140_000, 14];
        yield [165_000, 15];
        yield [195_000, 16];
        yield [225_000, 17];
        yield [265_000, 18];
        yield [305_000, 19];
        yield [355_000, 20];
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
