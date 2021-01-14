<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Adept power.
 */
class AdeptPower
{
    /**
     * Cost of the power in power points
     * @var float
     */
    public float $cost;

    /**
     * Description of the power
     * @var string
     */
    public string $description;

    /**
     * Collection of in-game effects for the power
     * @var array<string, int>
     */
    public array $effects;

    /**
     * Unique ID for the power
     * @var string
     */
    public string $id;

    /**
     * Level of the power
     * @var ?int
     */
    public ?int $level;

    /**
     * Name of the power
     * @var string
     */
    public string $name;

    /**
     * Page the power was introduced on
     * @var ?int
     */
    public ?int $page;

    /**
     * Rule book the power was introduced in
     * @var string
     */
    public string $ruleset;

    /**
     * Collection of all powers
     * @var ?array<mixed>
     */
    public static ?array $powers;

    /**
     * Build a new Power object.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_url') . 'adept-powers.php';
        self::$powers ??= require $filename;
        $id = strtolower($id);
        if (!isset(self::$powers[$id])) {
            throw new \RuntimeException(sprintf(
                'Adept power ID "%s" is invalid',
                $id
            ));
        }

        $power = self::$powers[$id];
        $this->cost = $power['cost'];
        $this->description = $power['description'];
        $this->effects = $power['effects'] ?? [];
        $this->id = $id;
        $this->level = $power['level'] ?? null;
        $this->name = $power['name'];
        $this->page = $power['page'] ?? null;
        $this->ruleset = $power['ruleset'] ?? 'core';
    }

    /**
     * Return the name of the power.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
