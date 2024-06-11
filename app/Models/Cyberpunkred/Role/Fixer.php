<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role;
use OutOfBoundsException;
use Stringable;

class Fixer extends Role implements Stringable
{
    public const TYPE_BROKER_DEALS = 1;
    public const TYPE_PROCURE_ATYPICAL = 2;
    public const TYPE_BROKER_SERVICES = 3;
    public const TYPE_SUPPLY_REGULAR = 4;
    public const TYPE_PROCURE_ILLEGAL = 5;
    public const TYPE_SUPPLY_RESOURCES = 6;
    public const TYPE_OPERATE_NIGHT_MARKETS = 7;
    public const TYPE_BROKER_CONTRACTS = 8;
    public const TYPE_BROKER_FENCE = 9;
    public const TYPE_EXCLUSIVE_AGENT = 10;

    /**
     * Fixer's type.
     * @var int
     */
    public int $type;

    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role = [])
    {
        $this->abilityDescription = 'The Fixer\'s Role Ability is Operator. '
            . 'Fixers know how to get things on the black market and are adept '
            . 'at navigating the complex social customs of The Street, where '
            . 'hundreds of cultures and economic levels collide. Fixers '
            . 'maintain vast webs of contacts and clients who they can reach '
            . 'out to source goods, favors, or information. Fixers can also '
            . 'source desirable resources and make favorable deals.';
        $this->abilityName = 'Operator';
        $this->description = 'You realized fast that you weren\'t ever going '
            . 'to get a Corporate job or be tough enough to be a Solo. But you '
            . 'always knew you had a knack for figuring out what other people '
            . 'wanted, and how to get it for them. For a price, of course. Now '
            . 'your deals have moved past the nickel-and-dime stuff into the '
            . 'big time.||'
            . 'Maybe you move illegal weapons over the border. Or steal and '
            . 'resell medical supplies. Perhaps you\'re a skill broker acting '
            . 'as an agent for high-priced Solos and \'Runners, or even hiring '
            . 'a whole Nomad pack to back a client\'s contracts. You buy and '
            . 'sell favors like an old-style Mafia godfather. You have '
            . 'connections into all kinds of businesses, deals, and political '
            . 'groups. You use your contacts and allies as part of a vast web '
            . 'of intrigue and coercion. If there\'s a hot nightclub in the '
            . 'City, you\'ve bought into it. If there are military-class '
            . 'weapons on The Street, you smuggled ‘em in. If there\'s a '
            . 'faction war going down, you\'re negotiating between sides with '
            . 'an eye on the main chance. But you\'re not entirely in it for '
            . 'the bucks. If someone needs to get the heat off, you\'ll hide '
            . 'them. You get people housing when there isn\'t any, and you '
            . 'bring in food when the streets are blockaded. Maybe you do it '
            . 'because you know they\'ll owe you later, but you\'re not sure. '
            . 'You\'re one part Robin Hood and two parts AI Capone. In the '
            . 'past, they would have called you a crime lord. But this is the '
            . 'fragmented, nasty, deadly Time of the Red. So now they call you '
            . 'a Fixer.';
        $this->rank = $role['rank'] ?? self::DEFAULT_ROLE_RANK;
        $this->type = $role['type'] ?? self::TYPE_BROKER_DEALS;
    }

    public function __toString(): string
    {
        return 'Fixer';
    }

    /**
     * Return the type of fixer.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getType(): string
    {
        return match ($this->type) {
            self::TYPE_BROKER_DEALS => 'Broker deals between rival gangs.',
            self::TYPE_PROCURE_ATYPICAL => 'Procure rare or atypical resources '
                . 'for exclusive clientele.',
            self::TYPE_BROKER_SERVICES => 'Specialize in brokering Solo or '
                . 'Tech services as an agent.',
            self::TYPE_SUPPLY_REGULAR => 'Supply a regular resource for the '
                . 'Night Markets, like food, medicines, or drugs.',
            self::TYPE_PROCURE_ILLEGAL => 'Procure highly illegal resources, '
                . 'like street drugs or milspec weapons.',
            self::TYPE_SUPPLY_RESOURCES => 'Supply resources for Techs and '
                . 'Medtechs, like parts and medical supplies.',
            self::TYPE_OPERATE_NIGHT_MARKETS => 'Operate several successful '
                . 'Night Markets, although not as owner.',
            self::TYPE_BROKER_FENCE => 'Broker deals as a fence for scavengers '
                . 'raiding Corps or Combat Zones.',
            self::TYPE_BROKER_CONTRACTS => 'Broker use contracts for heavy '
                . 'machinery, military vehicles, and aircraft.',
            self::TYPE_EXCLUSIVE_AGENT => 'Act as an exclusive agent for a '
                . 'Media, Rockerboy, or a Nomad Pack.',
            default => throw new OutOfBoundsException(),
        };
    }
}
