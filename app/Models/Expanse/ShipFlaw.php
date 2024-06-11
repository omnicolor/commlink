<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class ShipFlaw implements Stringable
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;
    public string $name;
    /** @psalm-suppress PossiblyUnusedProperty */
    public int $page;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $ruleset;

    /**
     * Collection of effects the flaw adds to the ship.
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<string, mixed>
     */
    public array $effects = [];

    /**
     * Collection of all ship flaws.
     * @var array<string, array<string, array<string, int>|int|string>>
     */
    public static array $flaws;

    /**
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.expanse') . 'ship-flaws.php';
        self::$flaws ??= require $filename;

        $this->id = strtolower($id);
        if (!isset(self::$flaws[$this->id])) {
            throw new RuntimeException(sprintf(
                'Expanse ship flaw "%s" is invalid',
                $this->id
            ));
        }

        $flaw = self::$flaws[$this->id];
        $this->description = $flaw['description'];
        $this->effects = $flaw['effects'] ?? [];
        $this->name = $flaw['name'];
        $this->page = $flaw['page'];
        $this->ruleset = $flaw['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return a collection of all flaws.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, ShipFlaw>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.expanse') . 'ship-flaws.php';
        self::$flaws ??= require $filename;

        $flaws = [];
        /** @var string $id */
        foreach (array_keys(self::$flaws) as $id) {
            $flaws[$id] = new self($id);
        }
        return $flaws;
    }
}
