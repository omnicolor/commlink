<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models\Role;

use Modules\Cyberpunkred\Models\Role;
use Stringable;

class Solo extends Role implements Stringable
{
    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Solo\'s Role Ability is Combat '
            . 'Awareness. With Combat Awareness, a Solo can call up their '
            . 'training to have an enhanced situational awareness of the '
            . 'battlefield. When combat begins, anytime outside of combat, or '
            . 'in combat with an Action, a Solo may divide the total number of '
            . 'points they have in their Combat Awareness Role Ability among a '
            . 'number of combat abilities. If a Solo chooses to not change '
            . 'their point assignments, their previous ones persist. '
            . 'Activating some of these abilities will cost the Solo more '
            . 'points than others.';
        $this->abilityName = 'Combat Awareness';
        $this->description = 'You were reborn with a gun in your hand—the '
            . 'flesh and blood hand—not the metallic weapons factory that '
            . 'covers most of your other arm. Whether as a freelance guard and '
            . 'killer-for-hire, or as one of the Corporate cybersoldiers who '
            . 'enforce business deals and the Company\'s "black operations," '
            . 'you\'re one of the elite fighting machines of the Time of the '
            . 'Red. Most Solos put in military time during the 4th Corporate '
            . 'War, in a Corporate army, or in one of the government\'s '
            . 'current "police actions" around the country. As the battle '
            . 'damage piles up, you start to rely more and more upon tech: '
            . 'cyberlimbs for weapons and armor, bio-program chips to increase '
            . 'your reflexes and awareness, combat drugs to give you that edge '
            . 'over your opponents. When you\'re the best of the best, you '
            . 'might even leave the ranks of Corporate samurai and go '
            . 'ronin—freelancing your lethal talents as a killer, bodyguard, '
            . 'or enforcer to whoever can pay your very high fees. Sounds '
            . 'good? There\'s a price—a heavy one. You\'ve lost so much of '
            . 'your original meat body that you\'re almost a machine. Your '
            . 'killing reflexes are so jacked up that you have to restrain '
            . 'yourself from going berserk at any moment. Years of combat '
            . 'drugs taken to keep the edge have given you terrifying '
            . 'addictions. There are few people you can trust anymore. One '
            . 'night you might sleep in a penthouse condo in the City, the '
            . 'next in a filthy alley on The Street. But that\'s the price of '
            . 'being the best. And you\'re willing to pay it. Because you\'re '
            . 'a Solo.';
        $this->rank = $role['rank'] ?? self::DEFAULT_ROLE_RANK;
    }

    public function __toString(): string
    {
        return 'Solo';
    }
}
