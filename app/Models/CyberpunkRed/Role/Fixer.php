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
            default => throw new \OutOfBoundsException(),
        };
    }
}
