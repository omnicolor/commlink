<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

/**
 * @psalm-suppress all
 */
enum Background: string
{
    case Military = 'military';
    case Monastic = 'monastic';
    case Outlaw = 'outlaw';
    case Privileged = 'privileged';
    case Urban = 'urban';
    case Wilderness = 'wilderness';
}
