<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class ShipWeapon implements Stringable
{
    public const RANGE_LONG = 'long';
    public const RANGE_MEDIUM = 'medium';
    public const RANGE_CLOSE = 'close';

    public ?string $damage = null;
    public string $description;
    public string $name;
    public int $page;
    public string $range;
    public string $ruleset;

    /**
     * Collection of all weapons.
     * @var array<string, array<string, int|string>>
     */
    public static array $weapons;

    /**
     * @throws RuntimeException
     */
    public function __construct(
        public string $id,
        public string $mount,
        public ?int $quality = 1,
    ) {
        $filename = config('expanse.data_path') . 'ship-weapons.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$weapons ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$weapons[$id])) {
            throw new RuntimeException(sprintf(
                'Expanse ship weapon "%s" is invalid',
                $id
            ));
        }

        $weapon = self::$weapons[$id];
        $this->damage = $weapon['damage'] ?? null;
        $this->description = $weapon['description'];
        $this->name = $weapon['name'];
        $this->page = $weapon['page'];
        $this->range = $weapon['range'];
        $this->ruleset = $weapon['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return a collection of all weapons.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, ShipWeapon>
     */
    public static function all(): array
    {
        $filename = config('expanse.data_path') . 'ship-weapons.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$weapons ??= require $filename;

        $weapons = [];
        /** @var string $id */
        foreach (array_keys(self::$weapons) as $id) {
            $weapons[$id] = new self($id, 'fore');
        }
        return $weapons;
    }
}
