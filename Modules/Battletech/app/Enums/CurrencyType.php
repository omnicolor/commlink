<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

use function ceil;

enum CurrencyType: string
{
    case C_Bill = 'c-bill';
    case Yuan = 'yuan';
    case Kroner = 'kroner';
    case Pound = 'pound';
    case Ryu = 'ryu';
    case Eagle = 'eagle';
    case Krona = 'krona';
    case Kerensky = 'kerensky';
    case Taurian_Bull = 'taurian-bull';
    case Calderon_Bull = 'calderon-bull';
    case Canopian_Dollar = 'canopian-dollar';
    case Fronc_Dollar = 'fronc-dollar';
    case Escudo = 'escudo';
    case Talent = 'talent';
    case Skull = 'skull';

    public static function convert(int $amount, self $from, self $to): int
    {
        // Move the decimal to account for cents.
        $amount /= 100;

        // Next, convert to C-Bills.
        $amount /= self::getExchangeRate($from);

        // Then multiply by the target currency's rate.
        $amount *= self::getExchangeRate($to);

        // Put the decimal back.
        $amount *= 100;

        // Round up to the nearest cent.
        return (int)ceil($amount);
    }

    public static function getExchangeRate(self $currency): float
    {
        return match ($currency) {
            self::C_Bill => 1.00,
            self::Yuan => 2.00,
            self::Kroner => 1.18,
            self::Ryu => 1.32,
            self::Pound => 1.20,
            self::Eagle => 1.12,
            self::Krona => 1.67,
            self::Kerensky => 0.25,
            self::Taurian_Bull => 4.00,
            self::Calderon_Bull => 5.00,
            self::Canopian_Dollar => 4.00,
            self::Fronc_Dollar => 10.00,
            self::Escudo => 6.67,
            self::Talent => 7.69,
            self::Skull => 20.00,
        };
    }
}
