<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum SpellDuration: string
{
    case Instantaneous = 'I';
    case Sustained = 'S';
    case Limited = 'L';
    case Permanent = 'P';
}
