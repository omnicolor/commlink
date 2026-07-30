<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum SpellDamage: string
{
    case Physical = 'P';
    case Mana = 'S';
    case Special = 'Special';
}
