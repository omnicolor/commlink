<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

enum Era: string
{
    case WarWithEra = 'WW';
    case Generation1 = 'G1';
    case Generation2 = 'G2';
    case FarFuture = 'FF';
}
