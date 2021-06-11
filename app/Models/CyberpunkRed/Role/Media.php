<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role;

class Media extends Role
{
    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Media\'s Role Ability is Credibility. '
            . 'The Media can not only convince an audience of the truth of '
            . 'what they publish but also has a larger audience the more '
            . 'credible they are. Medias also have greater levels of access to '
            . 'sources and information; they are always in the know with their '
            . 'ears to the ground to pick up on rumors and information '
            . 'passively.';
        $this->abilityName = 'Credibility';
        $this->description = 'They\'re bending the truth out there. And '
            . 'you\'re going to stop them. Someone has to do it. The '
            . 'Corporations used to rule the world. They dumped toxics, '
            . 'destabilized economies, and committed murder with equal '
            . 'impunity. The government back then couldn\'t even stop '
            . 'them—hell, they owned the government. But then the War came.||'
            . 'The War stripped away the facade and let us all know exactly '
            . 'what had been going on under our noses. And the people who made '
            . 'sure we all knew the score were the Medias. That\'s you. '
            . 'You\'ve got a vidlink and a press pass, and you\'re not afraid '
            . 'to use them. You\'re a city-wide figure, seen nightly all over '
            . 'the Data Pool in the Time of the Red. It\'s not like the old '
            . 'days, when you had a major Mediacorp behind you; this time, '
            . 'you\'ve gotta depend on your fans, your contacts, and your own '
            . 'reputation. But it\'s harder for these new Corps to make you '
            . 'disappear. So when you dig down for the dirt and slime the '
            . 'corrupt officials and Corporate lapdogs try to cover up, you '
            . 'can dig deep. The next morning, you can put the details of '
            . 'their crimes all over the screamsheets and vidscreens. Three or '
            . 'four times, the bad guys have tried to kill you—that\'s why '
            . 'your backup\'s a crack Solo bodyguard and you\'ve got one of '
            . 'the top ‘Runners in the business digging through NET '
            . 'Architectures to back your stories. You have to be good, or '
            . 'else.||'
            . 'Your ‘Runner\'s just phoned in with a hot lead. You grab your '
            . 'gear and flag your backup. You\'re going to break those '
            . 'bastards. This time, for sure.';
        $this->rank = $role['rank'] ?? 4;
    }

    /**
     * Return the name of the role.
     * @return string
     */
    public function __toString(): string
    {
        return 'Media';
    }
}
