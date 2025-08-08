<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Enums;

enum Attribute: string
{
    case Charisma = 'cha';
    case Agility = 'agi';
    case Reaction = 'rea';
    case Willpower = 'wil';
    case Strength = 'str';
    case Logic = 'log';
    case Intuition = 'int';
}
