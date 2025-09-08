<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Models;

use Iterator;
use Modules\Transformers\Models\AltMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('transformers')]
#[Small]
final class AltModeTest extends TestCase
{
    /**
     * @return Iterator<string, array<int, (int | string | null)>>
     */
    public static function altModeStatisticProvider(): Iterator
    {
        yield 'vehicle' => [
            AltMode::TYPE_VEHICLE,
            -2,
            2,
            4,
            -2,
            0,
            1,
            null,
            0,
        ];
        yield 'machine' => [
            AltMode::TYPE_MACHINE,
            1,
            0,
            -2,
            2,
            0,
            0,
            null,
            2,
        ];
        yield 'weapon' => [
            AltMode::TYPE_WEAPON,
            null,
            0,
            0,
            -2,
            0,
            2,
            1,
            2,
        ];
        yield 'primitive' => [
            AltMode::TYPE_PRIMITIVE,
            3,
            0,
            2,
            1,
            0,
            0,
            null,
            -3,
        ];
    }

    #[DataProvider('altModeStatisticProvider')]
    public function testStatisticModifier(
        string $mode,
        ?int $strength,
        int $intelligence,
        int $speed,
        int $endurance,
        int $rank,
        int $courage,
        ?int $firepower,
        int $skill,
    ): void {
        $mode = new AltMode($mode);
        self::assertSame($strength, $mode->statisticModifier('strength'));
        self::assertSame($intelligence, $mode->statisticModifier('intelligence'));
        self::assertSame($speed, $mode->statisticModifier('speed'));
        self::assertSame($endurance, $mode->statisticModifier('endurance'));
        self::assertSame($rank, $mode->statisticModifier('rank'));
        self::assertSame($courage, $mode->statisticModifier('courage'));
        self::assertSame($firepower, $mode->statisticModifier('firepower'));
        self::assertSame($skill, $mode->statisticModifier('skill'));
    }

    public function testToString(): void
    {
        $mode = new AltMode(AltMode::TYPE_VEHICLE);
        self::assertSame('Vehicle', (string)$mode);
    }

    public function testInvalidAltModeType(): void
    {
        self::expectException(RuntimeException::class);
        new AltMode('invalid');
    }
}
