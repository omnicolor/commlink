<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum TechnologyRating: string
{
    case Primitive = 'A';
    case Low = 'B';
    case Medium = 'C';
    case High = 'D';
    case Advanced = 'E';
    case HyperAdvanced = 'F';
}
