<?php

declare(strict_types=1);

namespace App\Models\Capers;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function str_replace;
use function strtolower;
use function ucfirst;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class Gear implements Stringable
{
    public float $cost;
    public string $name;
    public string $type;

    /**
     * @var array<string, array<string, float|int|string>>
     */
    public static ?array $gear;

    protected function __construct(public string $id, public int $quantity)
    {
        $filename = config('app.data_path.capers') . 'gear.php';
        self::$gear ??= require $filename;

        $gear = self::$gear[$id];
        $this->cost = $gear['cost'];
        $this->name = $gear['name'];
        $this->type = $gear['type'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getType(): string
    {
        return ucfirst(str_replace('-', ' ', $this->type));
    }

    public static function get(string $id, int $quantity = 1): Gear
    {
        $filename = config('app.data_path.capers') . 'gear.php';
        self::$gear ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$gear[$id])) {
            throw new RuntimeException(
                sprintf('Gear ID "%s" is invalid', $id)
            );
        }

        $gear = self::$gear[$id];
        if ('explosive' === $gear['type']) {
            return new Explosive($id, $quantity);
        }
        if ('weapon' === $gear['type']) {
            return new Weapon($id, $quantity);
        }
        return new self($id, $quantity);
    }

    public static function all(): GearArray
    {
        $filename = config('app.data_path.capers') . 'gear.php';
        self::$gear ??= require $filename;

        $gear = new GearArray();
        /** @var string $id */
        foreach (array_keys(self::$gear ?? []) as $id) {
            $gear[$id] = Gear::get($id);
        }
        return $gear;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public static function explosives(): GearArray
    {
        $filename = config('app.data_path.capers') . 'gear.php';
        self::$gear ??= require $filename;

        $explosives = new GearArray();
        foreach (self::$gear ?? [] as $id => $item) {
            if ('explosive' !== $item['type']) {
                continue;
            }
            $explosives[] = new Explosive($id, 1);
        }
        return $explosives;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public static function normalGear(): GearArray
    {
        $filename = config('app.data_path.capers') . 'gear.php';
        self::$gear ??= require $filename;

        $gear = new GearArray();
        foreach (self::$gear ?? [] as $id => $item) {
            if ('explosive' === $item['type'] || 'weapon' === $item['type']) {
                continue;
            }
            $gear[] = new self($id, 1);
        }
        return $gear;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public static function weapons(): GearArray
    {
        $filename = config('app.data_path.capers') . 'gear.php';
        self::$gear ??= require $filename;

        $weapons = new GearArray();
        foreach (self::$gear ?? [] as $id => $item) {
            if ('weapon' !== $item['type']) {
                continue;
            }
            $weapons[] = new Weapon($id, 1);
        }
        return $weapons;
    }
}
