<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum Attribute: string
{
    case Body = 'body';
    case Charisma = 'charisma';
    case Dexterity = 'dexterity';
    case Edge = 'edge';
    case Intelligence = 'intelligence';
    case Reflexes = 'reflexes';
    case Strength = 'strength';
    case Willpower = 'willpower';
}
