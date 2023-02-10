<?php

declare(strict_types=1);

namespace App\Models\Avatar;

/**
 * @psalm-suppress all
 */
enum Background
{
    case Military;
    case Monastic;
    case Outlaw;
    case Privileged;
    case Urban;
    case Wilderness;
}
