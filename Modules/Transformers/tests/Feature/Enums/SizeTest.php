<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Enums;

use Iterator;
use Modules\Transformers\Enums\Size;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('transformers')]
#[Small]
final class SizeTest extends TestCase
{
    /**
     * @return Iterator<int, array<int, (int | string | null)>>
     */
    public static function actionProvider(): Iterator
    {
        yield [0, null];
        yield [1, 'avoid'];
        yield [2, 'avoid'];
        yield [3, null];
        yield [4, 'size-dmg'];
        yield [5, 'size-dmg'];
        yield [6, 'size-dmg'];
        yield [7, 'size-dmg'];
        yield [8, 'size-dmg'];
    }

    #[DataProvider('actionProvider')]
    public function testActions(int $sizeValue, ?string $effect): void
    {
        $size = Size::from($sizeValue);
        self::assertSame($effect, $size->action());
    }

    /**
     * @return Iterator<int, array<int, int>>
     */
    public static function energonProvider(): Iterator
    {
        yield [0, 4];
        yield [1, 3];
        yield [2, 1];
        yield [3, 0];
        yield [4, -1];
        yield [5, -3];
        yield [6, -5];
        yield [7, -8];
        yield [8, -10];
    }

    #[DataProvider('energonProvider')]
    public function testEnergon(int $sizeValue, int $energonModifier): void
    {
        $size = Size::from($sizeValue);
        self::assertSame($energonModifier, $size->energon());
    }

    /**
     * @return Iterator<int, array<int, int>>
     */
    public static function hpProvider(): Iterator
    {
        yield [0, -4];
        yield [1, -3];
        yield [2, -1];
        yield [3, 0];
        yield [4, 1];
        yield [5, 2];
        yield [6, 5];
        yield [7, 8];
        yield [8, 30];
    }

    #[DataProvider('hpProvider')]
    public function testHp(int $sizeValue, int $hpModifier): void
    {
        $size = Size::from($sizeValue);
        self::assertSame($hpModifier, $size->hp());
    }
}
