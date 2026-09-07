<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Enums;

enum Ability: string
{
    case Charisma = 'charisma';
    case Constitution = 'constitution';
    case Dexterity = 'dexterity';
    case Intelligence = 'intelligence';
    case Strength = 'strength';
    case Wisdom = 'wisdom';
}
