<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role;
use Stringable;

class Nomad extends Role implements Stringable
{
    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Nomad\'s Role Ability is Moto. '
            . 'Whenever a Nomad increases their Rank in Moto, they have the '
            . 'option of adding another stock vehicle (with minimum specs) of '
            . 'their Moto Rank or lower to the pool of Family vehicles they '
            . 'have permission to use from the Family Motorpool or to make an '
            . 'upgrade to one of their current vehicles. Thanks to being '
            . 'around vehicles since birth, Nomads are also able to drive any '
            . 'type of vehicle with tremendous skill.';
        $this->abilityName = 'Mono';
        $this->description = 'Years ago, the Corps drove your family off the '
            . 'farm. They rolled in, took over the land, and put rent-a-cops '
            . 'all over the place. But that was before the War. You were '
            . 'loners, homeless, until you created a Nomad Pack of nearly '
            . 'two-hundred members. Back then, your Pack was crammed into a '
            . 'huge, ragtag fleet of cars, vans, buses, and RVs roaming the '
            . 'freeways looking for supplies, odd jobs, and spare parts in a '
            . 'fragmented world. The Pack was your home—it had teachers, '
            . 'Medtechs, leaders, and mechanics—a virtual town on wheels in '
            . 'which everyone was related by marriage or kinship. But in the '
            . 'Time of the Red, your Nomad Pack has evolved. Your knowledge of '
            . 'roadcraft—of how to get between the safezones over the savage '
            . 'highways has allowed you to become the masters of getting '
            . 'people, supplies, and materials to a world that desperately '
            . 'needs them. Your cousins on the open seas have taken over the '
            . 'huge container ships and turned them into the Nomad convoys '
            . 'keeping civilization running. Your Deltajock famboys keep the '
            . 'supply lines to the Orbital Highriders open. If it has to get '
            . 'somewhere and get there safely, Nomads get the job done. Your '
            . 'vehicles are well-armored and bristling with stolen weapons: '
            . 'miniguns, rocket launchers, and the like. Every kid knows how '
            . 'to use a rifle, and everyone packs a knife. Like modern-day '
            . 'cowboys, you ride the hard trail. You\'ve got a gun, a bike, '
            . 'and your Family, and that\'s all you need. You\'re a Nomad.';
        $this->rank = $role['rank'] ?? self::DEFAULT_ROLE_RANK;
    }

    public function __toString(): string
    {
        return 'Nomad';
    }
}
