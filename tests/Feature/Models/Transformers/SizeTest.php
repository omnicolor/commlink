<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\Size;
use Tests\TestCase;

/**
 * Unit tests for transformer's sizes.
 * @group models
 * @group transformers
 * @small
 */
final class SizeTest extends TestCase
{
    /**
     * @return array<int, array<int, int|null|string>>
     */
    public static function actionProvider(): array
    {
        return [
            [0, null],
            [1, 'avoid'],
            [2, 'avoid'],
            [3, null],
            [4, 'size-dmg'],
            [5, 'size-dmg'],
            [6, 'size-dmg'],
            [7, 'size-dmg'],
            [8, 'size-dmg'],
        ];
    }

    /**
     * @dataProvider actionProvider
     */
    public function testActions(int $sizeValue, ?string $effect): void
    {
        $size = Size::from($sizeValue);
        self::assertSame($effect, $size->action());
    }

    /**
     * @return array<int, array<int, int>>
     */
    public static function energonProvider(): array
    {
        return [
            [0, 4],
            [1, 3],
            [2, 1],
            [3, 0],
            [4, -1],
            [5, -3],
            [6, -5],
            [7, -8],
            [8, -10],
        ];
    }

    /**
     * @dataProvider energonProvider
     */
    public function testEnergon(int $sizeValue, int $energonModifier): void
    {
        $size = Size::from($sizeValue);
        self::assertSame($energonModifier, $size->energon());
    }

    /**
     * @return array<int, array<int, int>>
     */
    public static function hpProvider(): array
    {
        return [
            [0, -4],
            [1, -3],
            [2, -1],
            [3, 0],
            [4, 1],
            [5, 2],
            [6, 5],
            [7, 8],
            [8, 30],
        ];
    }

    /**
     * @dataProvider hpProvider
     */
    public function testHp(int $sizeValue, int $hpModifier): void
    {
        $size = Size::from($sizeValue);
        self::assertSame($hpModifier, $size->hp());
    }
}