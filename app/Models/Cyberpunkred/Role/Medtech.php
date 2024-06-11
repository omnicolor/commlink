<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role;
use Stringable;

class Medtech extends Role implements Stringable
{
    /**
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Medtech\'s Role Ability is Medicine. '
            . 'With this ability, Medtechs can keep people alive who should be '
            . 'dead with their knowledge, tools, and training. In the Time of '
            . 'the Red, they are as much doctors as they are mechanics, caring '
            . 'for people who are often more machine than human. Whenever the '
            . 'Medtech increases their Medicine Rank, they also choose one of '
            . 'three Medicine Specialties to allocate a single point to: '
            . 'surgery, pharmaceuticals, or cryosystems operation.';
        $this->abilityName = 'Medicine';
        $this->description = 'You\'re an artist, and the human body is your '
            . 'canvas. You\'ve got the best tools the Time of the Red can '
            . 'offer, and you know how to use them. If you\'re lucky, you got '
            . 'to attend one of the real med schools scattered around the '
            . 'wreck of the Old United States. And after the War, military '
            . 'hospitals were everywhere and the few doctors on the war front '
            . 'needed helping hands to hold down screaming patients and splice '
            . 'cyberware back together. So, maybe you learned that way.||'
            . 'And there\'s always an old ripperdoc or two out there who '
            . 'hearken back to that old science fiction story called The '
            . 'Bladerunner—not that old flatscreen vid, but the really old '
            . 'sci-fi book about renegade doctors who performed illegal street '
            . 'surgery in one of the first dystopian novels. Maybe one of '
            . 'those guys trained you. Maybe that\'s where you are right now, '
            . 'patching up the wounded, mending up the sick, and keeping the '
            . 'locals alive. For love, commitment, or maybe a just a fat '
            . 'payday on the side.||'
            . 'If you\'re really lucky, you\'ve scored a berth in the local '
            . 'Trauma Team franchise. Trauma Teams are groups of licensed '
            . 'paramedicals who patrol the city looking for patients. You '
            . 'operate from an AV-4 Urban Assault Vehicle, redesigned into an '
            . 'ambulance configuration, and armed with a belly-mounted '
            . 'minigun. It\'s the best of the best—Trauma Team charges some '
            . 'heavy subscription fees to save its clients, and that '
            . 'translates into new medical toys, faster AV ambulances, and '
            . 'hefty salaries for the best surgeons around.||'
            . 'It doesn\'t matter how you got here. What matters is that '
            . 'you\'re here, on The Street, doing the job. And you\'d be doing '
            . 'it no matter what the reason. It\'s what marks you as a '
            . 'Medtech.';
        $this->rank = $role['rank'] ?? self::DEFAULT_ROLE_RANK;
    }

    public function __toString(): string
    {
        return 'Medtech';
    }
}
