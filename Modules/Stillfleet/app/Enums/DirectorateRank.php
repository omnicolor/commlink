<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Enums;

use RangeException;

enum DirectorateRank: string
{
    case StillfleeterMinima = 'stillfleeter-minima';
    case Stillfleeter = 'stillfleeter';
    case Factor = 'factor';
    case RefactorMinima = 'refactor-minima';
    case RefactorMinder = 'refactor-minder';
    case RefactorGezant = 'refactor-gezant';
    case RefactorPrime = 'refactor-prime';
    case SubdirectorMinima = 'subdirector-minima';
    case SubdirectorMinder = 'subdirector-minder';
    case SubdirectorKastelein = 'subdirector-kastelein';
    case SubdirectorPrime = 'subdirector-prime';
    case Director = 'director';
    case DirectorPrime = 'director-prime';

    /**
     * @codeCoverageIgnore
     */
    public static function roll(int $roll): self
    {
        return match ($roll) {
            1 => self::StillfleeterMinima,
            2 => self::Stillfleeter,
            3 => self::Factor,
            4 => self::RefactorMinder,
            5 => self::RefactorGezant,
            6 => self::RefactorPrime,
            7 => self::SubdirectorMinima,
            8 => self::SubdirectorMinder,
            9 => self::SubdirectorKastelein,
            10 => self::SubdirectorPrime,
            11 => self::Director,
            12 => self::DirectorPrime,
            default => throw new RangeException('Roll must be between 1 and 12'),
        };
    }
}
