<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models\Role;

use Modules\Cyberpunkred\Models\Role;
use Stringable;

class Lawman extends Role implements Stringable
{
    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Lawman\'s Role Ability is Backup. '
            . 'With this ability, Lawmen can call upon the help of a group of '
            . 'fellow officers, based on the Lawman\'s Rank and the conditions '
            . 'under which they make the call. This Backup is armed and '
            . 'armored based on their Rank.';
        $this->abilityName = 'Backup';
        $this->description = 'In the old days before the War, they only used '
            . 'to shoot at cops. Now you\'re lucky if you just take a slug. '
            . 'The Street is mean these days, filled with new drugs, new '
            . 'gangs, and new weapons that make a Minami-10 look like a kid\'s '
            . 'toy. But even so, you\'re out there doing what you can to '
            . 'Protect—and Serve.||There used to be a big City Force, but most '
            . 'of the Old Guard in NCPD have been thrown out on their own to '
            . 'keep what peace they can. The ones who remain still take the '
            . 'Badge seriously; they work to keep people safe and make some '
            . 'kind of stand against chaos. Even if you\'d rather just walk a '
            . 'beat, if you\'re a professional Lawman of any stripe, you\'re '
            . 'stuck carrying at least four high-caliber weapons, most of them '
            . 'full-auto types, wearing a Kevlar® vest that\'ll stop '
            . '850ft/lbs. per square inch—and often you\'re still outgunned '
            . 'and outflanked. Half the gangs were cybered up to begin with: '
            . 'super speed, super reflexes, could see in the dark, carried '
            . 'weapons in their arms... and that was before the War and the '
            . 'Fall of the Towers pumped a metric ton of milspec cybertech '
            . 'into the Night Markets. The other half of the guys on The '
            . 'Street are freelance Corporate mercs who used to have jobs '
            . 'during the War; hired to enforce Corp armies disbanded by the '
            . 'New United States\' goon squads. Now they\'re the goon squads '
            . 'and you\'re trying to keep them under control too. Used to be '
            . 'the Corporate Cops had heavy weapons, full combat armor, Trauma '
            . 'Team backup, AV-4 assault vehicles, and gyrocopters with '
            . 'miniguns. But the nice, clean sectors full of new office '
            . 'buildings and fancy restaurants—where no jacked-up psychopunk '
            . 'is going to ever go on a killing spree with an FN-RAL37—are '
            . 'mostly gone. Now you\'ve got mostly burned-out buildings and '
            . 'abandoned cars, where every night is a new firefight and '
            . 'another great opportunity for a messy death. Or you might draw '
            . 'a Psycho Squad berth and get the job of hunting down heavily '
            . 'armed and armored cyborgs who\'ve flipped out. A cyberpsycho '
            . 'can walk through machine gun fire and not even feel it, so a '
            . 'lot of the Psycho Squad become a bit crazy themselves; they '
            . 'load up with boosted reflexes, get some monstrously huge guns, '
            . 'and go hunt the cyborgs solo. But you\'re not that crazy. Yet.';
        $this->rank = $role['rank'] ?? self::DEFAULT_ROLE_RANK;
    }

    public function __toString(): string
    {
        return 'Lawman';
    }
}
