<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Enums;

use RangeException;

enum StillfleeterRank: string
{
    case Leerling = 'leerling';
    case Arbeider = 'arbeider';
    case Huzaar = 'huzaar';
    case Rijder = 'rijder';
    case Kannonier = 'kannonier';
    case Korporaaltje = 'korporaaltje';
    case MatroosPrime = 'matroos-prime';
    case Korporaal = 'korporaal';
    case Wachtmeester = 'wachtmeester';
    case Opperwatch = 'opperwatch';
    case Adjudant = 'adjudant';
    case Kornet = 'kornet';
    case Devaiss = 'devaiss';
    case Luitenant = 'luitenant';
    case Luitcommandeur = 'luitcommandeur';
    case Commandeur = 'commandeur';
    case Zeekapitein = 'zeekapitein';
    case Kolonel = 'kolonel';
    case Achteradmiraal = 'achteradmiraal';
    case Admiraal = 'admiraal';

    public static function roll(int $roll): self
    {
        return match ($roll) {
            1 => self::Leerling,
            2 => self::Arbeider,
            3 => self::Huzaar,
            4 => self::Rijder,
            5 => self::Kannonier,
            6 => self::Korporaaltje,
            7 => self::MatroosPrime,
            8 => self::Korporaal,
            9 => self::Wachtmeester,
            10 => self::Opperwatch,
            11 => self::Adjudant,
            12 => self::Kornet,
            13 => self::Devaiss,
            default => throw new RangeException('Roll must be between 1 and 20'),
        };
    }
}
