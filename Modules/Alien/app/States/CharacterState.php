<?php

declare(strict_types=1);

namespace Modules\Alien\States;

use Thunk\Verbs\State;

class CharacterState extends State
{
    public int $damage = 0;
    public int $experience = 0;
    public int $radiation = 0;
    public int $story_points = 0;
    public int $stress = 0;
}
