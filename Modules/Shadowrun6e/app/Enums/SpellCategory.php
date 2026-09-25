<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum SpellCategory: string
{
    case Combat = 'combat';
    case Detection = 'detection';
    case Health = 'health';
    case Illusion = 'illusion';
    case Manipulation = 'manipulation';
}
