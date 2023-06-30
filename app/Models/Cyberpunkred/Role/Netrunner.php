<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role;

class Netrunner extends Role
{
    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Netrunner\'s Role Ability is '
            . 'Interface. Interface is what allows the Netrunner to Netrun—to '
            . 'interface with electronic mind-modems (called cyberdecks) to '
            . 'control computers, electronics, and associated programming. The '
            . 'Interface Role Ability also gives the Netrunner access to a '
            . 'wide suite of Abilities related to computer hacking and system '
            . 'control.';
        $this->abilityName = 'Interface';
        $this->description = 'You\'re a brain-burning computer hacker & master '
            . 'of the Post-NET cyberverse in the Time of the Red. At three, '
            . 'your parents bought you an old Kirama LPD-12 cyberdeck with '
            . 'Zetatech 526 optical goggles (you were too young for interface '
            . 'plugs), and your life was changed. By fifth grade, you were '
            . 'already using REFRAME-G1s meta-programming to crack into the '
            . 'school district\'s system and change your grades. When you were '
            . 'thirteen, you shifted enough funds out of unprotected Trans '
            . 'United Bank accounts to finance your first neural interface '
            . 'plugs. You couldn\'t wait to run high and fast with the other '
            . 'gods of the NET—Bartmoss, Magnificent Curtis, and the rest. '
            . 'Then the 4th Corp War blew the Old NET apart. The R.A.B.I.D.S. '
            . 'made NET travel a suicide run; the Nodes were fragmented or '
            . 'corrupted. But there were still places to run. You just had to '
            . 'go there and jack in the hard way. You traded in sitting on the '
            . 'couch for a Bodyweight combat bodysuit and Virtuality 5 '
            . 'interface goggles to mesh NET with Meatspace. The systems you '
            . 'cracked were smaller, but even deadlier. Now, you\'re really '
            . 'part of a team, with Solos to cover your back, Medtechs to '
            . 'restart your heart if the ICE gets you, and Techs to help you '
            . 'hot-wire your cyberdeck for more speed and software deployment. '
            . 'Now, nothing can stop you. As an electronic wraith, you slip '
            . 'into the "hardest" mainframe systems with ease: stealing, '
            . 'trading, and selling their deepest secrets at will. The Black '
            . 'ICE may still kill you in the end, but until the ride runs out '
            . 'you\'ll be there, bare-brained and headfirst in the New NET.';
        $this->rank = $role['rank'] ?? self::DEFAULT_ROLE_RANK;
    }

    public function __toString(): string
    {
        return 'Netrunner';
    }
}
