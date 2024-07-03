<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Models;

enum CoinType: string
{
    case Copper = 'cp';
    case Electrum = 'ep';
    case Gold = 'gp';
    case Platinum = 'pp';
    case Silver = 'sp';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function convert(int $amount, self $from, self $to): float
    {
        return match ($from) {
            self::Copper => match ($to) {
                self::Copper => $amount,
                self::Silver => $amount / 10,
                self::Electrum => $amount / 50,
                self::Gold => $amount / 100,
                self::Platinum => $amount / 1000,
            },
            self::Electrum => match ($to) {
                self::Copper => $amount * 50,
                self::Silver => $amount * 5,
                self::Electrum => $amount,
                self::Gold => $amount / 2,
                self::Platinum => $amount / 20,
            },
            self::Gold => match ($to) {
                self::Copper => $amount * 100,
                self::Silver => $amount * 10,
                self::Electrum => $amount * 2,
                self::Gold => $amount,
                self::Platinum => $amount / 10,
            },
            self::Platinum => match ($to) {
                self::Copper => $amount * 1000,
                self::Silver => $amount * 100,
                self::Electrum => $amount * 20,
                self::Gold => $amount * 10,
                self::Platinum => $amount,
            },
            self::Silver => match ($to) {
                self::Copper => $amount * 10,
                self::Silver => $amount,
                self::Electrum => $amount / 5,
                self::Gold => $amount / 10,
                self::Platinum => $amount / 100,
            },
        };
    }
}
