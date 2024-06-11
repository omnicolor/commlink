<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;
use Stringable;

use function config;
use function sort;
use function sprintf;
use function strtolower;

/**
 * Gear, representing something a character can use.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Gear implements Stringable
{
    /**
     * Whether the item is active.
     */
    public ?bool $active;

    /**
     * Availability code of the item.
     */
    public string $availability;

    /**
     * Base cost of the item.
     */
    public int $cost;

    /**
     * Matrix damage the item has taken.
     */
    public ?int $damage;

    /**
     * Description of the item.
     */
    public string $description;

    /**
     * Effects of the item.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Modifications applied to the item.
     */
    public GearModificationArray $modifications;

    /**
     * Name of the item.
     */
    public string $name;

    /**
     * Optional rating for the item.
     */
    public ?int $rating = null;

    /**
     * Optional subname of the item.
     */
    public ?string $subname = null;

    /**
     * List of all gear.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $gear = null;

    /**
     * @throws RuntimeException If ID is invalid
     */
    public function __construct(public string $id, public int $quantity = 1)
    {
        $filename = config('app.data_path.shadowrun5e') . 'gear.php';
        self::$gear ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$gear[$id])) {
            throw new RuntimeException(
                sprintf('Item ID "%s" is invalid', $id)
            );
        }

        $item = self::$gear[$id];
        $this->availability = (string)$item['availability'];
        $this->cost = (int)$item['cost'];
        $this->description = $item['description'];
        $this->effects = $item['effects'] ?? [];
        $this->modifications = new GearModificationArray();
        $this->name = $item['name'];
        $this->rating = $item['rating'] ?? null;
        $this->subname = $item['subname'] ?? null;
    }

    public function __toString(): string
    {
        if (null !== $this->subname) {
            return $this->name . ' - ' . $this->subname;
        }
        return $this->name;
    }

    /**
     * Builds a Gear object from a Mongo result.
     * @param array<string, mixed> $gear
     * @throws RuntimeException
     */
    public static function build(array $gear): Gear
    {
        $gearObj = GearFactory::get($gear);

        foreach ($gear['modifications'] ?? [] as $mod) {
            $gearObj->modifications[] = new GearModification($mod);
        }
        $gearObj->damage = $gear['damage'] ?? 0;
        $gearObj->subname = $gear['subname'] ?? $gearObj->subname;
        if (!($gearObj instanceof Commlink)) {
            return $gearObj;
        }
        $gearObj->active = $gear['active'] ?? false;
        foreach ($gear['programsRunning'] ?? [] as $program) {
            $gearObj->programsRunning[] = new Program($program);
        }
        $gearObj->setAttributes = $gear['setAttributes'] ?? $gearObj->attributes;
        $gearObj->sin = $gear['sin'] ?? null;
        $gearObj->marks = $gear['marks'] ?? [];
        $gearObj->slavedDevices = $gear['slavedDevices'] ?? [];

        if (!isset($gear['programsInstalled'])) {
            return $gearObj;
        }

        sort($gear['programsInstalled']);
        foreach ($gear['programsInstalled'] as $rawProgram) {
            $gearObj->programs[] = Program::build(
                $rawProgram,
                $gearObj->programsRunning
            );
        }

        return $gearObj;
    }

    /**
     * Return a item based on its name.
     * @throws RuntimeException
     */
    public static function findByName(string $name): Gear
    {
        $filename = config('app.data_path.shadowrun5e') . 'gear.php';
        self::$gear ??= require $filename;
        foreach (self::$gear as $gear) {
            if (strtolower((string)$gear['name']) === strtolower($name)) {
                return GearFactory::get($gear['id']);
            }
            if (
                isset($gear['subname'])
                && strtolower((string)$gear['subname']) === strtolower($name)
            ) {
                return GearFactory::get($gear['id']);
            }
        }
        throw new RuntimeException(sprintf(
            'Gear name "%s" was not found',
            $name
        ));
    }

    /**
     * Return the cost of this item, including its modifications.
     */
    public function getCost(): int
    {
        $cost = $this->cost;
        foreach ($this->modifications as $mod) {
            $cost += $mod->getCost();
        }
        return $cost * $this->quantity;
    }
}
