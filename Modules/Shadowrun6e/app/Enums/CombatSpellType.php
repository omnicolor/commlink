<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum CombatSpellType: string
{
    case Direct = 'D';
    case Indirect = 'I';
    case IndirectArea = 'IA';
}
