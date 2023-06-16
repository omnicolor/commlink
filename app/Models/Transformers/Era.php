<?php

declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * @psalm-suppress UnusedClass
 */
enum Era: string
{
    case WarWithEra = 'WW';
    case Generation1 = 'G1';
    case Generation2 = 'G2';
    case FarFuture = 'FF';
}
