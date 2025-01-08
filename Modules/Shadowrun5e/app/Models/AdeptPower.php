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
 * Adept power.
 */
final class AdeptPower implements Stringable
{
    /**
     * Cost of the power in power points.
     */
    public readonly float $cost;

    /**
     * Description of the power.
     */
    public readonly string $description;

    /**
     * Collection of in-game effects for the power.
     * @var array<string, int>
     */
    public readonly array $effects;

    /**
     * Level of the power.
     */
    public ?int $level;

    /**
     * Name of the power.
     */
    public readonly string $name;

    /**
     * Page the power was introduced on.
     */
    public int|null $page;

    /**
     * Rule book the power was introduced in.
     */
    public readonly string $ruleset;

    /**
     * Collection of all powers.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $powers = null;

    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'adept-powers.php';
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
        $this->level = $power['level'] ?? null;
        $this->name = $power['name'];
        $this->page = $power['page'] ?? null;
        $this->ruleset = $power['ruleset'] ?? 'core';
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
