<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Adept power.
 */
class AdeptPower
{
    /**
     * Cost of the power in power points.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public float $cost;

    /**
     * Description of the power.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * Collection of in-game effects for the power.
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<string, int>
     */
    public array $effects;

    /**
     * Unique ID for the power.
     */
    public string $id;

    /**
     * Level of the power.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public ?int $level;

    /**
     * Name of the power.
     */
    public string $name;

    /**
     * Page the power was introduced on.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public ?int $page;

    /**
     * Rule book the power was introduced in.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $ruleset;

    /**
     * Collection of all powers.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $powers;

    /**
     * Build a new AdeptPower object.
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'adept-powers.php';
        self::$powers ??= require $filename;
        $id = \strtolower($id);
        if (!isset(self::$powers[$id])) {
            throw new RuntimeException(\sprintf(
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

    public function __toString(): string
    {
        return $this->name;
    }
}
