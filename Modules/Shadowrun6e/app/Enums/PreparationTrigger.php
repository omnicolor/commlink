<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum PreparationTrigger: string
{
    case Command = 'command';
    case Contact = 'contact';
    case Time = 'time';
}
