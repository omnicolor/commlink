<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Critter/Spirit power.
 */
final class CritterPower implements Stringable
{
    public const string ACTION_AUTO = 'Auto';
    public const string ACTION_COMPLEX = 'Complex';
    public const string ACTION_SIMPLE = 'Simple';

    public const string DURATION_ALWAYS = 'Always';
    public const string DURATION_INSTANT = 'Instant';
    public const string DURATION_PERMANENT = 'Permanent';
    public const string DURATION_SPECIAL = 'Special';
    public const string DURATION_SUSTAINED = 'Sustained';

    public const string RANGE_LOS = 'LOS';
    public const string RANGE_SELF = 'Self';
    public const string RANGE_SPECIAL = 'Special';
    public const string RANGE_TOUCH = 'Touch';

    public const string TYPE_MANA = 'M';
    public const string TYPE_PHYSICAL = 'P';

    /**
     * Type of action required to use the power: Auto, Complex, or Simple.
     */
    public readonly string $action;
    public readonly string $description;

    /**
     * Duration for the power: Always, Instant, Permanent, Special, or
     * Sustained.
     */
    public readonly string $duration;
    public readonly string $name;
    public readonly int $page;

    /**
     * Range of the power: LOS (line of sight), Self, Special, or Touch.
     */
    public readonly string $range;
    public readonly string $ruleset;

    /**
     * Type of the power: M (mana) or P (physical).
     */
    public readonly string $type;

    /**
     * Collection of all powers.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $powers;

    /**
     * Constructor.
     * @throws RuntimeException if the power is not found
     */
    public function __construct(
        public readonly string $id,
        null|string $subname = null,
    ) {
        $filename = config('shadowrun5e.data_path') . 'critter-powers.php';
        self::$powers ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$powers[$id])) {
            throw new RuntimeException(sprintf(
                'Critter/Spirit power "%s" is invalid',
                $this->id
            ));
        }

        $power = self::$powers[$this->id];
        $this->action = $power['action'];
        $this->description = $power['description'];
        $this->duration = $power['duration'];
        if (null !== $subname) {
            $this->name = $power['name'] . ' ' . $subname;
        } else {
            $this->name = $power['name'];
        }
        $this->page = $power['page'];
        $this->range = $power['range'];
        $this->ruleset = $power['ruleset'];
        $this->type = $power['type'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
