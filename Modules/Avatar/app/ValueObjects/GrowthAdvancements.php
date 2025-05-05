<?php

declare(strict_types=1);

namespace Modules\Avatar\ValueObjects;

use RangeException;

class GrowthAdvancements
{
    public readonly int $new_move_from_my_playbook;
    public readonly int $new_move_from_another_playbook;
    public readonly int $shift_your_center;
    public readonly int $unlock_your_moment_of_balance;

    /**
     * @param array{
     *   new_move_from_my_playbook?: int<0, 2>,
     *   new_move_from_another_playbook?: int<0, 2>,
     *   shift_your_center?: int<0, 2>,
     *   unlock_your_moment_of_balance?: int<0, 2>
     * } $taken_advancements
     */
    public function __construct(array $taken_advancements)
    {
        $this->new_move_from_my_playbook
            = $taken_advancements['new_move_from_my_playbook'] ?? 0;
        $this->new_move_from_another_playbook
            = $taken_advancements['new_move_from_another_playbook'] ?? 0;
        $this->shift_your_center
            = $taken_advancements['shift_your_center'] ?? 0;
        $this->unlock_your_moment_of_balance
            = $taken_advancements['unlock_your_moment_of_balance'] ?? 0;
        if (
            0 > $this->new_move_from_my_playbook
            || 0 > $this->new_move_from_another_playbook
            || 0 > $this->shift_your_center
            || 0 > $this->unlock_your_moment_of_balance
        ) {
            throw new RangeException('Growth advancements can not be less than zero');
        }

        if (
            2 < $this->new_move_from_my_playbook
            || 2 < $this->new_move_from_another_playbook
            || 2 < $this->shift_your_center
            || 2 < $this->unlock_your_moment_of_balance
        ) {
            throw new RangeException('Growth advancements can not be greater than two');
        }
    }
}
