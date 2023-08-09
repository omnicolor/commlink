<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\AltMode;
use Tests\TestCase;

/**
 * @group models
 * @group transformers
 * @small
 */
final class AltModeTest extends TestCase
{
    /**
     * @return array<string, array<string, int|null|string>>
     */
    public static function altModeStatisticProvider(): array
    {
        return [
            'vehicle' => [
                'mode' => AltMode::TYPE_VEHICLE,
                'strength' => -2,
                'intelligence' => 2,
                'speed' => 4,
                'endurance' => -2,
                'rank' => 0,
                'courage' => 1,
                'firepower' => null,
                'skill' => 0,
            ],
            'machine' => [
                'mode' => AltMode::TYPE_MACHINE,
                'strength' => 1,
                'intelligence' => 0,
                'speed' => -2,
                'endurance' => 2,
                'rank' => 0,
                'courage' => 0,
                'firepower' => null,
                'skill' => 2,
            ],
            'weapon' => [
                'mode' => AltMode::TYPE_WEAPON,
                'strength' => null,
                'intelligence' => 0,
                'speed' => 0,
                'endurance' => -2,
                'rank' => 0,
                'courage' => 2,
                'firepower' => 1,
                'skill' => 2,
            ],
            'primitive' => [
                'mode' => AltMode::TYPE_PRIMITIVE,
                'strength' => 3,
                'intelligence' => 0,
                'speed' => 2,
                'endurance' => 1,
                'rank' => 0,
                'courage' => 0,
                'firepower' => null,
                'skill' => -3,
            ],
        ];
    }

    /**
     * @dataProvider altModeStatisticProvider
     */
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
}
