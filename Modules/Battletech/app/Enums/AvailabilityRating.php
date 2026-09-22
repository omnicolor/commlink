<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum AvailabilityRating: string
{
    case VeryCommon = 'A';
    case Common = 'B';
    case Uncommon = 'C';
    case Rare = 'D';
    case VeryRare = 'E';
    case Unique = 'F';
    case NonExistent = 'X';
}
