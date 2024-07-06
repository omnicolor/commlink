<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

enum Classification: string
{
    case Major = 'major';
    case Minor = 'minor';
    case Standard = 'standard';
}
