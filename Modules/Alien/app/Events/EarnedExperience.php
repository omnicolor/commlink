<?php

declare(strict_types=1);

namespace Modules\Alien\Events;

use Modules\Alien\States\CharacterState;
use Thunk\Verbs\Attributes\Autodiscovery\AppliesToState;
use Thunk\Verbs\Event;

#[AppliesToState(CharacterState::class)]
class EarnedExperience extends Event
{
    public function __construct(
        public int $character_id,
        public string $real_character_id,
        public int $amount,
    ) {
    }

    public function apply(CharacterState $state): void
    {
        $state->experience += $this->amount;
    }

    public function validate(CharacterState $state): void
    {
        $this->assert(
            0 < $this->amount,
            'Earned experience must be positive',
        );
    }
}
