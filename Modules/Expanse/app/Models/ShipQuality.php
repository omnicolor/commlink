<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class ShipQuality implements Stringable
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * Collection of effects the quality adds to the ship.
     * @var array<string, mixed>
     */
    public array $effects = [];

    /**
     * Collection of all ship qualities.
     * @var ?array<string, array<string, array<string, int>|int|string>>
     */
    public static ?array $qualities;

    /**
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('expanse.data_path') . 'ship-qualities.php';
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
     * @return array<string, ShipQuality>
     */
    public static function all(): array
    {
        $filename = config('expanse.data_path') . 'ship-qualities.php';
        self::$qualities ??= require $filename;

        $qualities = [];
        /** @var string $id */
        foreach (array_keys(self::$qualities) as $id) {
            $qualities[$id] = new self($id);
        }
        return $qualities;
    }
}
