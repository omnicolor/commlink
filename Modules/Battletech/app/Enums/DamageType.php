<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum DamageType: string
{
    case AreaEffect = 'A';
    case BurstFire = 'B';
    case Continuous = 'C';
    case Subduing = 'D';
    case Splash = 'S';

    /**
     * @codeCoverageIgnore
     */
    public function description(): string
    {
        return match ($this) {
            self::AreaEffect => 'Regardless of the attack roll result, the '
                . 'MoS for all attacks delivered by an area-effect (blast) '
                . 'weapon is considered 0 when assessing damage. This is '
                . 'because the damage automatically affects all targets '
                . 'within that radius unless an obstruction strong enough to '
                . 'absorb the damage is present between the point of impact '
                . 'and the target. As the damage radiates outward from an '
                . 'area-effect weapon’s point of impact—unless the weapon’s '
                . 'rules state otherwise—the attack’s AP and BD factors '
                . 'decrease by 1 for each meter of distance (to a minimum of '
                . '0).',
            self::BurstFire => 'Burst-fire weapons fired in Burst-Fire mode '
                . 'inflict 1 point of additional damage for every point of '
                . 'MoS on a successful attack, up to the maximum number of '
                . 'shots fired in the attack. The maximum number of shots a '
                . 'burst-fire weapon can fire is found in the weapon’s Burst '
                . 'rating (in the weapon’s Notes).',
            self::Continuous => 'Unless otherwise specified by the rules for '
                . 'the weapon or situation, damage from any weapon with the '
                . 'Continuous Damage feature occurs over and over during the '
                . 'End Phase after the weapon first hits its target. The '
                . 'initial damage at the time of the first successful attack '
                . 'is resolved per a Standard damage attack (unless other '
                . 'features, such as Splash damage, indicate otherwise), but '
                . 'in the next End Phase after that (and in subsequent End '
                . 'Phases, as long as the Continuous Damage effects linger), '
                . 'the character suffers additional damage points that '
                . 'receive no added MoS effects. For more information, see '
                . 'Continuous Damage (see p. 180).',
            self::Subduing => 'Subduing damage inflicted by a ranged weapon '
                . 'follows the same rules as standard ranged attack damage, '
                . 'but all damage points are assigned as Fatigue rather than '
                . 'Standard damage.',
            self::Splash => 'If the optional Hit Locations rules are not in '
                . 'effect, weapons with Splash damage increase their AP '
                . 'ratings by 1 point (to a maximum of 10) against other '
                . 'characters. Aside from any other damage features (such as '
                . 'Continuous damage effects), Splash damage is resolved as a '
                . 'normal attack.',
        };
    }
}
