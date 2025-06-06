<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use Override;
use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class Power implements Stringable
{
    public const string TYPE_MAJOR = 'Major';
    public const string TYPE_MINOR = 'Minor';

    /**
     * How the power gets activated.
     */
    public string $activation;

    /**
     * Boosts that are available for the power.
     */
    public BoostArray $availableBoosts;

    /**
     * Boosts the character has chosen for the power.
     */
    public BoostArray $boosts;

    /**
     * Short description of the power.
     */
    public string $description;

    /**
     * How long the power lasts.
     */
    public string $duration;

    /**
     * Description of the power's effects.
     */
    public string $effect;

    /**
     * Maximum number of ranks a character can have for the power.
     */
    public int $maxRank;

    /**
     * Name of the power.
     */
    public string $name;

    /**
     * Description of the power's range (if it has one).
     */
    public string $range;

    /**
     * What the power affects.
     */
    public string $target;

    /**
     * Type of power: Major or Minor.
     */
    public string $type;

    /**
     * @var array<string, array<string, string>>
     */
    public static ?array $powers = null;

    /**
     * Constructor.
     * @param array<int, string> $boosts
     */
    public function __construct(
        public string $id,
        public int $rank = 1,
        array $boosts = [],
    ) {
        $filename = config('capers.data_path') . 'powers.php';
        self::$powers ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$powers[$this->id])) {
            throw new RuntimeException(
                sprintf('Power ID "%s" is invalid', $id)
            );
        }

        $power = self::$powers[$this->id];
        $this->activation = $power['activation'];
        $this->availableBoosts = new BoostArray();
        foreach ($power['boosts'] as $boostId => $boost) {
            $this->availableBoosts[$boostId] = new Boost(
                $boostId,
                $boost['description'],
                $boost['name']
            );
        }
        $this->boosts = new BoostArray();
        foreach ($boosts as $boostId) {
            $boost = $power['boosts'][$boostId];
            $this->boosts[] = new Boost(
                $boostId,
                $boost['description'],
                $boost['name']
            );
        }
        $this->description = $power['description'];
        $this->duration = $power['duration'];
        $this->effect = $power['effect'];
        $this->maxRank = $power['maxRank'];
        $this->name = $power['name'];
        $this->range = $power['range'];
        $this->target = $power['target'];
        $this->type = $power['type'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public static function all(): PowerArray
    {
        $filename = config('capers.data_path') . 'powers.php';
        self::$powers ??= require $filename;

        $powers = new PowerArray();
        /** @var string $id */
        foreach (array_keys(self::$powers ?? []) as $id) {
            $powers[$id] = new self($id);
        }
        return $powers;
    }

    public static function major(): PowerArray
    {
        $filename = config('capers.data_path') . 'powers.php';
        self::$powers ??= require $filename;

        $powers = new PowerArray();
        foreach (self::$powers ?? [] as $powerId => $power) {
            if (self::TYPE_MAJOR !== $power['type']) {
                continue;
            }
            $powers[$powerId] = new self($powerId);
        }
        return $powers;
    }

    public static function minor(): PowerArray
    {
        $filename = config('capers.data_path') . 'powers.php';
        self::$powers ??= require $filename;

        $powers = new PowerArray();
        foreach (self::$powers ?? [] as $powerId => $power) {
            if (self::TYPE_MINOR !== $power['type']) {
                continue;
            }
            $powers[$powerId] = new self($powerId);
        }
        return $powers;
    }
}
