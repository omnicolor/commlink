<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use Illuminate\Support\Facades\Log;
use Modules\Expanse\Enums\CrewCompetence;
use Modules\Expanse\Enums\ShipSize;
use Override;
use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class Ship implements Stringable
{
    public ?CrewCompetence $competence = null;
    public ?int $crew_minimum;
    public ?int $crew_standard;
    public ?string $favored_range;
    public bool $has_epstein;
    public string $length;
    public string $name;
    public int $page;
    public string $ruleset;
    public int $sensors;
    public ShipSize $size;

    /**
     * @var array<int, string>
     */
    public array $favored_stunts;

    /**
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
     * @var ?array<string, array<string, array<string, mixed>|int|string>>
     */
    public static ?array $ships;

    public function __construct(public string $id)
    {
        $filename = config('expanse.data_path') . 'ships.php';
        self::$ships ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$ships[$id])) {
            throw new RuntimeException(sprintf(
                'Expanse ship "%s" is invalid',
                $id
            ));
        }

        $ship = self::$ships[$id];
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

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return all ships.
     * @return array<string, Ship>
     */
    public static function all(): array
    {
        $filename = config('expanse.data_path') . 'ships.php';
        self::$ships ??= require $filename;

        $ships = [];
        /** @var string $id */
        foreach (array_keys(self::$ships ?? []) as $id) {
            $ships[$id] = new Ship($id);
        }
        return $ships;
    }
}
