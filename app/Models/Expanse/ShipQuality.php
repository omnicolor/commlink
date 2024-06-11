<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class ShipQuality implements Stringable
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;
    public string $name;
    /** @psalm-suppress PossiblyUnusedProperty */
    public int $page;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $ruleset;

    /**
     * Collection of effects the quality adds to the ship.
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<string, mixed>
     */
    public array $effects = [];

    /**
     * Collection of all ship qualities.
     * @var array<string, array<string, array<string, int>|int|string>>
     */
    public static array $qualities;

    /**
     * @psalm-suppress UnusedVariable
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.expanse') . 'ship-qualities.php';
        self::$qualities ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$qualities[$this->id])) {
            throw new RuntimeException(sprintf(
                'Expanse ship quality "%s" is invalid',
                $this->id
            ));
        }

        $quality = self::$qualities[$this->id];
        $this->description = $quality['description'];
        $this->effects = $quality['effects'] ?? [];
        $this->name = $quality['name'];
        $this->page = $quality['page'];
        $this->ruleset = $quality['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return a collection of all qualities.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, ShipQuality>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.expanse') . 'ship-qualities.php';
        self::$qualities ??= require $filename;

        $qualities = [];
        /** @var string $id */
        foreach (array_keys(self::$qualities) as $id) {
            $qualities[$id] = new self($id);
        }
        return $qualities;
    }
}
