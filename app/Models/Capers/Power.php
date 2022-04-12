<?php

declare(strict_types=1);

namespace App\Models\Capers;

use RuntimeException;

class Power
{
    public const TYPE_MAJOR = 'Major';
    public const TYPE_MINOR = 'Minor';

    /**
     * How the power gets activated.
     * @var string
     */
    public string $activation;

    /**
     * Boosts that are available for the power.
     * @var BoostArray
     */
    public BoostArray $availableBoosts;

    /**
     * Boosts the character has chosen for the power.
     * @var BoostArray
     */
    public BoostArray $boosts;

    /**
     * Short description of the power.
     * @var string
     */
    public string $description;

    /**
     * How long the power lasts.
     * @var string
     */
    public string $duration;

    /**
     * Description of the power's effects.
     * @var string
     */
    public string $effect;

    /**
     * Unique ID for the power.
     * @var string
     */
    public string $id;

    /**
     * Maximum number of ranks a character can have for the power.
     * @var int
     */
    public int $maxRank;

    /**
     * Name of the power.
     * @var string
     */
    public string $name;

    /**
     * Description of the power's range (if it has one).
     * @var string
     */
    public string $range;

    /**
     * What the power affects.
     * @var string
     */
    public string $target;

    /**
     * Type of power: Major or Minor.
     * @var string
     */
    public string $type;

    /**
     * @var array<string, array<string, string>>
     */
    public static ?array $powers;

    /**
     * Constructor.
     * @param string $id
     * @param int $rank
     * @param array<int, string> $boosts
     */
    public function __construct(
        string $id,
        public int $rank = 1,
        array $boosts = []
    ) {
        $filename = config('app.data_path.capers') . 'powers.php';
        self::$powers ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$powers[$this->id])) {
            throw new RuntimeException(
                \sprintf('Power ID "%s" is invalid', $id)
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

    public function __toString(): string
    {
        return $this->name;
    }

    public static function all(): PowerArray
    {
        $filename = config('app.data_path.capers') . 'powers.php';
        self::$powers ??= require $filename;

        $powers = new PowerArray();
        foreach (self::$powers ?? [] as $powerId => $power) {
            $powers[$powerId] = new self($powerId);
        }
        return $powers;
    }

    public static function major(): PowerArray
    {
        $filename = config('app.data_path.capers') . 'powers.php';
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
        $filename = config('app.data_path.capers') . 'powers.php';
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
