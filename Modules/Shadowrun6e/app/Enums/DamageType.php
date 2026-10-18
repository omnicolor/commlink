<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum DamageType: string
{
    case Physical = 'P';
    case Special = 'Special';
    case Stun = 'S';
}
