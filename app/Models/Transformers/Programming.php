<?php

declare(strict_types=1);

namespace App\Models\Transformers;

enum Programming: string
{
    case Engineer = 'engineer';
    case Gunner = 'gunner';
    case Scout = 'scout';
    case Warrior = 'warrior';
}
