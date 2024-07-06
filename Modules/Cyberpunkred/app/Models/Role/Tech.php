<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models\Role;

use Modules\Cyberpunkred\Models\Role;
use Stringable;

class Tech extends Role implements Stringable
{
    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Tech\'s Role Ability is Maker. Using '
            . 'the Maker Role Ability, the Tech can fix, improve, modify, '
            . 'make, and invent new items. Whenever a Tech increases their '
            . 'Maker Rank by one, they gain one rank in two different Maker '
            . 'Specialties of their choice, including repairing, upgrading, '
            . 'fabricating, and inventing';
        $this->abilityName = 'Maker';
        $this->description = 'You can\'t leave anything alone—if it sits near '
            . 'you for more than five minutes, you\'ve disassembled it and '
            . 'made it into something new. You\'ve always got at least two '
            . 'screwdrivers and a wrench in your pockets. Computer down? No '
            . 'problem. Hydrogen burner out in your Metrocar? No problem. '
            . 'Can\'t get the video to run or your interface glitching? No '
            . 'problem. You make your living building, fixing, and modifying—a '
            . 'crucial occupation in a technological world recovering from a '
            . 'War that broke the back of the supply chain. You can make some '
            . 'good bucks fixing everyday stuff, but for the serious money you '
            . 'need to tackle the big jobs. Illegal weapons. Illegal or stolen '
            . 'cybertech. Corporate espionage and counter-espionage gear for '
            . '"black operations." If you\'re any good, you\'re making a lot '
            . 'of money. And that money goes into new gadgets, hardware, and '
            . 'information. Your black market work isn\'t just making you '
            . 'friends—it\'s also racking you up an impressive number of '
            . 'enemies as well—so you invest a lot in defense systems and, if '
            . 'really pushed to the wall, call in a few markers on a Solo or '
            . 'two. You\'ve fixed up tech for everybody from black ops '
            . 'Corporate samurai to Ms. Zepada down the block. No one\'s ever '
            . 'come back to you with a complaint but that might be because of '
            . 'the turrets guarding your front door. You\'re addicted to '
            . 'technology in all its forms and that\'s what makes you a Tech.';
        $this->rank = $role['rank'] ?? self::DEFAULT_ROLE_RANK;
    }

    public function __toString(): string
    {
        return 'Tech';
    }
}
