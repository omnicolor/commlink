<?php

// phpcs:ignoreFile

declare(strict_types=1);

namespace App\Models\Avatar;

enum Background
{
    case Military;
    case Monastic;
    case Outlaw;
    case Privileged;
    case Urban;
    case Wilderness;
}
