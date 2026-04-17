<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum LanguageLevel: int
{
    case Fluent = 0;
    case Specialist = 1;
    case Expert = 2;
    case Native = 3;
}
