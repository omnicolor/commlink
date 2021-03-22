<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role;

class Fixer extends Role
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
    public function __construct(array $role)
    {
        $this->rank = $role['rank'];
        $this->type = $role['type'];
    }

    /**
     * Return the name of the role.
     * @return string
     */
    public function __toString(): string
    {
        return 'Fixer';
    }

    /**
     * Return the type of fixer.
     * @return string
     */
    public function getType(): string
    {
        switch ($this->type) {
            case self::TYPE_BROKER_DEALS:
                return 'Broker deals between rival gangs.';
            case self::TYPE_PROCURE_ATYPICAL:
                return 'Procure rare or atypical resources for exclusive '
                    . 'clientele.';
            case self::TYPE_BROKER_SERVICES:
                return 'Specialize in brokering Solo or Tech services as an '
                    . 'agent.';
            case self::TYPE_SUPPLY_REGULAR:
                return 'Supply a regular resource for the Night Markets, like '
                    . 'food, medicines, or drugs.';
            case self::TYPE_PROCURE_ILLEGAL:
                return 'Procure highly illegal resources, like street drugs or '
                    . 'milspec weapons.';
            case self::TYPE_SUPPLY_RESOURCES:
                return 'Supply resources for Techs and Medtechs, like parts '
                    . 'and medical supplies.';
            case self::TYPE_OPERATE_NIGHT_MARKETS:
                return 'Operate several successful Night Markets, although not '
                    . 'as owner.';
            case self::TYPE_BROKER_FENCE:
                return 'Broker deals as a fence for scavengers raiding Corps '
                    . 'or Combat Zones.';
            case self::TYPE_BROKER_CONTRACTS:
                return 'Broker use contracts for heavy machinery, military '
                    . 'vehicles, and aircraft.';
            case self::TYPE_EXCLUSIVE_AGENT:
                return 'Act as an exclusive agent for a Media, Rockerboy, or a '
                    . 'Nomad Pack.';
        }
        throw new \OutOfBoundsException();
    }
}
