<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Enums;

use Iterator;
use Modules\Battletech\Enums\CurrencyType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CurrencyTypeTest extends TestCase
{
    /**
     * @return Iterator<int, array<int, (int | CurrencyType)>>
     */
    public static function currencyDataProvider(): Iterator
    {
        // Basic examples, C-bill to all currency types.
        yield [100, CurrencyType::C_Bill, CurrencyType::C_Bill, 100];
        yield [100, CurrencyType::C_Bill, CurrencyType::Yuan, 200];
        yield [100, CurrencyType::C_Bill, CurrencyType::Kroner, 118];
        yield [100, CurrencyType::C_Bill, CurrencyType::Pound, 120];
        yield [100, CurrencyType::C_Bill, CurrencyType::Ryu, 132];
        yield [100, CurrencyType::C_Bill, CurrencyType::Eagle, 113];
        yield [100, CurrencyType::C_Bill, CurrencyType::Krona, 167];
        yield [100, CurrencyType::C_Bill, CurrencyType::Kerensky, 25];
        yield [100, CurrencyType::C_Bill, CurrencyType::Taurian_Bull, 400];
        yield [100, CurrencyType::C_Bill, CurrencyType::Calderon_Bull, 500];
        yield [100, CurrencyType::C_Bill, CurrencyType::Canopian_Dollar, 400];
        yield [100, CurrencyType::C_Bill, CurrencyType::Fronc_Dollar, 1000];
        yield [100, CurrencyType::C_Bill, CurrencyType::Escudo, 667];
        yield [100, CurrencyType::C_Bill, CurrencyType::Talent, 769];
        yield [100, CurrencyType::C_Bill, CurrencyType::Skull, 2000];
        // Examples from the rulebook, page 256.
        yield [3304, CurrencyType::Kroner, CurrencyType::Ryu, 3696];
        yield [2800, CurrencyType::C_Bill, CurrencyType::Kroner, 3304];
    }

    #[DataProvider('currencyDataProvider')]
    public function testConvert(
        int $amount,
        CurrencyType $from,
        CurrencyType $to,
        int $expected,
    ): void {
        self::assertSame(
            $expected,
            CurrencyType::convert($amount, $from, $to),
        );
    }
}
