<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models\Role;

use Modules\Cyberpunkred\Models\Role;
use OutOfBoundsException;
use Stringable;

class Rockerboy extends Role implements Stringable
{
    public const ACT_GROUP = 1;
    public const ACT_SOLO = 2;

    public const GUNNING_OLD_GROUP_MEMBER = 1;

    public const PERFORM_CAFE = 1;

    public const TYPE_MUSICIAN = 1;
    public const TYPE_SLAM_POET = 2;
    public const TYPE_STREET_ARTIST = 3;

    /**
     * Whether the character is a solo (2) or group (1) act.
     */
    public int $act;

    /**
     * Who's gunning for the rockerboy?
     */
    public int $gunning;

    /**
     * Where does the rockerboy perform?
     */
    public int $perform;

    /**
     * What kind of Rockerboy is the character?
     */
    public int $type;

    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->abilityDescription = 'The Rockerboy\'s Role Ability is '
            . 'Charismatic Impact. With this ability, they can influence '
            . 'others by sheer presence of personality. They need not be a '
            . 'musical performer; they can influence others through poetry, '
            . 'art, dance, or simply their physical presence. They could be a '
            . 'rocker—or a cult leader. As they grow in skill, they can affect '
            . 'larger and larger groups and call on their fans for greater and '
            . 'greater requests of loyalty.';
        $this->abilityName = 'Charismatic Impact';
        $this->description = 'If you live to rock, this is where you belong. '
            . 'As a Rockerboy, you\'re one of the street poets, the social '
            . 'conscience, and the rebels of the Time of the Red. With the '
            . 'advent of digital porta-studios and garage music mastering, '
            . 'every Rockerboy with a message can take it to The Street, put '
            . 'it in the record stores, bounce it off the comsats. Sometimes, '
            . 'your message isn\'t something the Corporations or the '
            . 'government wants to hear. Sometimes what you say is going to '
            . 'get right in the faces of the powerful people who really want '
            . 'to run this world. But you don\'t care, because as a Rockerboy, '
            . 'you know it\'s your place to challenge authority, whether in '
            . 'straight-out protest songs that tell it like it is, playing '
            . 'kick-ass rock n\' roll to get the people away from the TV sets '
            . 'and into The Streets, firing up the crowd with speeches, or '
            . 'composing fiery writings that shape the minds and hearts of '
            . 'millions. You have a proud history as a Rockerboy. Dylan, '
            . 'Springsteen, U2, NWA, the Who, Jett, the Stones—the legions of '
            . 'hard-rock heroes who told the truth with screaming guitars or '
            . 'gut-honest lyrics. You have the power to get the people up; to '
            . 'lead, inspire, and inform. Your message can give the timid '
            . 'courage, the weak strength, and the blind vision. Rockerboy '
            . 'legends like Johnny Silverhand, Rockerboy Manson (for whom the '
            . 'Role is named) and Kerry Eurodyne have led armies against '
            . 'Corporations and governments. Rockerboys have exposed '
            . 'corruption and brought down dictators. It\'s a lot of power for '
            . 'someone doing gigs every night in another city. But you can '
            . 'handle it. After all: you came to play!';
        $this->act = $role['act'] ?? self::ACT_SOLO;
        $this->gunning = $role['gunning'] ?? self::GUNNING_OLD_GROUP_MEMBER;
        $this->perform = $role['perform'] ?? self::PERFORM_CAFE;
        $this->rank = $role['rank'] ?? self::DEFAULT_ROLE_RANK;
        $this->type = $role['type'] ?? self::TYPE_MUSICIAN;
    }

    public function __toString(): string
    {
        return 'Rockerboy';
    }

    /**
     * Return the type of act the rockerboy performs in.
     * @throws OutOfBoundsException
     */
    public function getAct(): string
    {
        return match ($this->act) {
            self::ACT_SOLO => 'solo',
            self::ACT_GROUP => 'group',
            default => throw new OutOfBoundsException(),
        };
    }

    /**
     * Return a description of who's gunning for you.
     * @throws OutOfBoundsException
     */
    public function getWhosGunning(): string
    {
        return match ($this->gunning) {
            self::GUNNING_OLD_GROUP_MEMBER => 'Old group member who thinks you did them dirty.',
            default => throw new OutOfBoundsException(),
        };
    }

    /**
     * Return the type of rockerboy.
     * @throws OutOfBoundsException
     */
    public function getType(): string
    {
        return match ($this->type) {
            self::TYPE_MUSICIAN => 'musician',
            self::TYPE_SLAM_POET => 'slam poet',
            self::TYPE_STREET_ARTIST => 'street artist',
            default => throw new OutOfBoundsException(),
        };
    }
}
