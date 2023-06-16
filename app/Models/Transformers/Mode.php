<?php

declare(strict_types=1);

namespace App\Models\Transformers;

enum Mode: string
{
    case Robot = 'robot';
    case Alternate = 'alternate';
}
