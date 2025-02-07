<?php

declare(strict_types=1);

namespace Modules\Avatar\Enums;

enum LegacyOfExcellenceDriveStatus: string
{
    case Chosen = 'chosen';
    case Fulfilled = 'fulfilled';
    case Unfulfilled = 'unfulfilled';
}
