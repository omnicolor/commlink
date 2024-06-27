<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

enum Mode: string
{
    case Alternate = 'alternate';
    case Robot = 'robot';
}
