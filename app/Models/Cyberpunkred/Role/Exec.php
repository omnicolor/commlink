<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role;

class Exec extends Role
{
    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Exec\'s Role Ability is Teamwork. '
            . 'Just like a real corporate executive, the Exec builds a team '
            . 'whose members help them accomplish their goals, whether legal '
            . 'or not, morale permitting. Team members have a visible job '
            . 'description (like secretary or driver) but also have a covert '
            . 'roles (such as Netrunner, bodyguard, or assassin). Plus they '
            . 'get free housing and nice set of clothes!';
        $this->abilityName = 'Teamwork';
        $this->description = 'In the old days before the Time of the Red, you '
            . 'would have been a hard- driven, fast-track MBA on their way up '
            . 'the Corporate ladder. Sure, it was selling your soul to the '
            . 'Company, but face it: the Corporations ruled the world. They '
            . 'controlled governments, markets, nations, armies—you name it. '
            . 'And you knew that whoever controlled the Corporations '
            . 'controlled everything else. But things changed when the largest '
            . 'Megacorps on the planet got into a major war that was equal to '
            . 'anything any real national governments could have thrown down.||'
            . 'Okay, so right now your life as a junior executive is anything '
            . 'but easy. There are those underneath you who\'d kill for a shot '
            . 'at your job. Literally. There are those over you who\'d kill to '
            . 'keep you out of their jobs. Literally. And they\'re not kidding '
            . 'about the killing—every up-and-comer in the Corporation has '
            . 'their own Team of Solos and Netrunners to cover important pet '
            . 'projects. Last week, you led a mixed team of Solos, Netrunners, '
            . 'and Techs on a headhunting run to "extract" a researcher from a '
            . 'rival company. You told yourself you joined the Corporation to '
            . 'make it a better place—work from the inside, you said. Or just '
            . 'until you could start your own Corporation that would be... a '
            . 'little more honest. But now you\'re not so sure. Your ideals '
            . 'are a little tarnished and things are getting pretty bleak. But '
            . 'you can\'t worry about ethics now. You\'ve got a report due in '
            . 'an hour, and it looks like that guy in sales is planning to ice '
            . 'your database for good.||You\'re gonna ice him first.';
        $this->rank = $role['rank'] ?? 4;
    }

    /**
     * Return the name of the role.
     * @return string
     */
    public function __toString(): string
    {
        return 'Exec';
    }
}
