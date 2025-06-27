<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum LegalityRating: string
{
    case Unrestricted = 'A';
    case Monitored = 'B';
    case Licensed = 'C';
    case Controlled = 'D';
    case Restricted = 'E';
    case HighlyRestricted = 'F';
}
