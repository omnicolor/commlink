<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum FociCategory: string
{
    case Enchanting = 'enchanting';
    case Metamagic = 'metamagic';
    case Power = 'power';
    case Qi = 'qi';
    case Spell = 'spell';
    case Spirit = 'spirit';
    case Weapon = 'weapon';
}
