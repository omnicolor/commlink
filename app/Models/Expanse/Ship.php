<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * @psalm-suppress UndefinedClass
 */
class Ship
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?CrewCompetence $competence;
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?int $crew_minimum;
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?int $crew_standard;
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?string $favored_range;
    /** @psalm-suppress PossiblyUnusedProperty */
    public bool $has_epstein;
    public string $id;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $length;
    public string $name;
    /** @psalm-suppress PossiblyUnusedProperty */
    public int $page;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $ruleset;
    /** @psalm-suppress PossiblyUnusedProperty */
    public int $sensors;
    public ShipSize $size;

    /**
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<int, string>
     */
    public array $favored_stunts;

    /**
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<int, string>
     */
    public array $flaws;

    /**
     * @var array<int, ShipQuality>
     */
    public array $qualities = [];

    /**
     * @var array<int, ShipWeapon>
     */
    public array $weapons = [];

    /**
     * Collection of all ships.
     * @var array<string, array<string, array<string, mixed>|int|string>>
     */
    public static array $ships;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.expanse') . 'ships.php';
        self::$ships ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$ships[$this->id])) {
            throw new RuntimeException(\sprintf(
                'Expanse ship "%s" is invalid',
                $this->id
            ));
        }

        $ship = self::$ships[$this->id];
        $this->size = ShipSize::from($ship['size']);
        $this->crew_minimum = $ship['crew_minimum'] ?? $this->size->crewMin();
        $this->crew_standard = $ship['crew_standard'] ?? $this->size->crewStandard();
        $this->favored_range = $ship['favored_range'] ?? null;
        $this->favored_stunts = $ship['favored_stunts'] ?? [];
        $this->flaws = $ship['flaws'] ?? [];
        $this->has_epstein = $ship['has_epstein'] ?? true;
        $this->length = $ship['length'] ?? $this->size->length();
        $this->name = $ship['name'];
        $this->page = $ship['page'];
        $this->ruleset = $ship['ruleset'];
        $this->sensors = $ship['sensors'];

        foreach ($ship['qualities'] ?? [] as $quality) {
            try {
                $this->qualities[] = new ShipQuality($quality);
                // @codeCoverageIgnoreStart
            } catch (RuntimeException) {
                Log::warning(
                    'Expanse ship "{ship}" has invalid quality "{quality}"',
                    [
                        'ship' => $this->id,
                        'quality' => $quality,
                    ]
                );
                // @codeCoverageIgnoreEnd
            }
        }

        foreach ($ship['weapons'] ?? [] as $weapon) {
            try {
                $this->weapons[] = new ShipWeapon(
                    $weapon['id'],
                    $weapon['mount'],
                    $weapon['quantity'] ?? null
                );
                // @codeCoverageIgnoreStart
            } catch (RuntimeException) {
                Log::warning(
                    'Expanse ship "{ship}" has invalid weapon "{weapon}"',
                    [
                        'ship' => $this->id,
                        'weapon' => $weapon['id'],
                    ]
                );
            }
            // @codeCoverageIgnoreEnd
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return all ships.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, Ship>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.expanse') . 'ships.php';
        self::$ships ??= require $filename;

        $ships = [];
        /** @var string $id */
        foreach (array_keys(self::$ships) as $id) {
            $ships[$id] = new Ship($id);
        }
        return $ships;
    }
}
