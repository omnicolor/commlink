<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Critter/Spirit power.
 * @psalm-suppress PossiblyUnusedProperty
 */
class CritterPower
{
    public const ACTION_AUTO = 'Auto';
    public const ACTION_COMPLEX = 'Complex';
    public const ACTION_SIMPLE = 'Simple';

    public const DURATION_ALWAYS = 'Always';
    public const DURATION_INSTANT = 'Instant';
    public const DURATION_PERMANENT = 'Permanent';
    public const DURATION_SPECIAL = 'Special';
    public const DURATION_SUSTAINED = 'Sustained';

    public const RANGE_LOS = 'LOS';
    public const RANGE_SELF = 'Self';
    public const RANGE_SPECIAL = 'Special';
    public const RANGE_TOUCH = 'Touch';

    public const TYPE_MANA = 'M';
    public const TYPE_PHYSICAL = 'P';

    /**
     * Type of action required to use the power: Auto, Complex, or Simple.
     */
    public string $action;

    /**
     * Description of the power.
     */
    public string $description;

    /**
     * Duration for the power: Always, Instant, Permanent, Special, or
     * Sustained.
     */
    public string $duration;

    /**
     * Unique ID for the power.
     */
    public string $id;

    /**
     * Name of the power.
     */
    public string $name;

    /**
     * Page the power is described on.
     */
    public int $page;

    /**
     * Range of the power: LOS (line of sight), Self, Special, or Touch.
     */
    public string $range;

    /**
     * Ruleset the power was introduced in.
     */
    public string $ruleset;

    /**
     * Type of the power: M (mana) or P (physical).
     */
    public string $type;

    /**
     * Collection of all powers.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $powers;

    /**
     * Constructor.
     * @param string $id
     * @param ?string $subname
     * @throws RuntimeException if the power is not found
     */
    public function __construct(string $id, public ?string $subname = null)
    {
        $filename = config('app.data_path.shadowrun5e') . 'critter-powers.php';
        self::$powers ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$powers[$this->id])) {
            throw new RuntimeException(\sprintf(
                'Critter/Spirit power "%s" is invalid',
                $this->id
            ));
        }

        $power = self::$powers[$this->id];
        $this->action = $power['action'];
        $this->description = $power['description'];
        $this->duration = $power['duration'];
        $this->name = $power['name'];
        if (null !== $subname) {
            $this->name .= ' ' . $subname;
        }
        $this->page = $power['page'];
        $this->range = $power['range'];
        $this->ruleset = $power['ruleset'];
        $this->type = $power['type'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
