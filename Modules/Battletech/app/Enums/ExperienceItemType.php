<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum ExperienceItemType: string
{
    case Attribute = 'attribute';
    case Skill = 'skill';
    case Starting = 'starting';
    case Trait = 'trait';
}
