<?php

declare(strict_types=1);

namespace App\Models\Transformers;

enum Classification: string
{
    case Major = 'major';
    case Minor = 'minor';
    case Standard = 'standard';
}
