<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Adept power.
 */
class AdeptPower implements Stringable
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
    public static ?array $powers = null;

    public function __construct(public string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'adept-powers.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$powers ??= require $filename;
        $id = strtolower($id);
        if (!isset(self::$powers[$id])) {
            throw new RuntimeException(sprintf(
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
