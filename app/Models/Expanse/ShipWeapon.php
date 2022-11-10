<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;

class ShipWeapon
{
    public const RANGE_LONG = 'long';
    public const RANGE_MEDIUM = 'medium';
    public const RANGE_CLOSE = 'close';

    public ?string $damage = null;
    public string $description;
    public string $id;
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
     * Constructor.
     * @param string $id
     * @param string $mount
     * @param int $quality
     * @throws RuntimeException
     */
    public function __construct(
        string $id,
        public string $mount,
        public ?int $quality = 1
    ) {
        $filename = config('app.data_path.expanse') . 'ship-weapons.php';
        self::$weapons ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$weapons[$this->id])) {
            throw new RuntimeException(\sprintf(
                'Expanse ship weapon "%s" is invalid',
                $this->id
            ));
        }

        $weapon = self::$weapons[$this->id];
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
     * @return array<string, ShipWeapon>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.expanse') . 'ship-weapons.php';
        self::$weapons ??= require $filename;

        $weapons = [];
        foreach (self::$weapons as $id => $weapon) {
            $weapons[(string)$id] = new self($id, 'fore');
        }
        return $weapons;
    }
}
