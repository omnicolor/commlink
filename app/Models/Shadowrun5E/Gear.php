<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Gear, representing something a character can use.
 */
class Gear
{
    /**
     * Whether the item is active.
     * @var ?bool
     */
    public ?bool $active;

    /**
     * Availability code of the item.
     * @var string
     */
    public string $availability;

    /**
     * Base cost of the item.
     * @var int
     */
    public int $cost;

    /**
     * Matrix damage the item has taken.
     * @var ?int
     */
    public ?int $damage;

    /**
     * Description of the item.
     * @var string
     */
    public string $description;

    /**
     * Effects of the item.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * ID of the item.
     * @var string
     */
    public string $id;

    /**
     * Modifications applied to the item.
     * @var GearModificationArray
     */
    public GearModificationArray $modifications;

    /**
     * Name of the item.
     * @var string
     */
    public string $name;

    /**
     * Quantity of the item.
     * @var int
     */
    public int $quantity;

    /**
     * Optional rating for the item.
     * @var ?int
     */
    public ?int $rating = null;

    /**
     * Optional subname of the item.
     * @var ?string
     */
    public ?string $subname = null;

    /**
     * List of all gear.
     * @var ?array<string, mixed>
     */
    public static ?array $gear;

    /**
     * Load an item.
     * @param string $id ID to load
     * @param int $quantity Number of items
     * @throws \RuntimeException If ID is invalid
     */
    public function __construct(string $id, int $quantity = 1)
    {
        $filename = config('app.data_path.shadowrun5e') . 'gear.php';
        self::$gear ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$gear[$id])) {
            throw new \RuntimeException(
                sprintf('Item ID "%s" is invalid', $id)
            );
        }

        $item = self::$gear[$id];
        $this->availability = (string)$item['availability'];
        $this->cost = (int)$item['cost'];
        $this->description = $item['description'];
        $this->effects = $item['effects'] ?? [];
        $this->id = $id;
        $this->modifications = new GearModificationArray();
        $this->name = $item['name'];
        $this->quantity = $quantity;
        $this->rating = $item['rating'] ?? null;
        $this->subname = $item['subname'] ?? null;
    }

    /**
     * Return the item's name.
     * @return string
     */
    public function __toString(): string
    {
        if (!is_null($this->subname)) {
            return $this->name . ' - ' . $this->subname;
        }
        return $this->name;
    }

    /**
     * Builds a Gear object from a Mongo result.
     * @param array<string, mixed> $gear
     * @throws \RuntimeException
     */
    public static function build(array $gear): Gear
    {
        $gearObj = GearFactory::get($gear);

        foreach ($gear['modifications'] ?? [] as $mod) {
            $gearObj->modifications[] = new GearModification($mod);
        }
        $gearObj->damage = $gear['damage'] ?? 0;
        $gearObj->subname = $gear['subname'] ?? null;
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
     * @param string $name
     * @return Gear
     * @throws \RuntimeException
     */
    public static function findByName(string $name): Gear
    {
        $filename = config('app.data_path.shadowrun5e') . 'gear.php';
        self::$gear ??= require $filename;
        foreach (self::$gear as $gear) {
            if (strtolower($gear['name']) === strtolower($name)) {
                return GearFactory::get($gear['id']);
            }
            if (
                isset($gear['subname'])
                && strtolower($gear['subname']) === strtolower($name)
            ) {
                return GearFactory::get($gear['id']);
            }
        }
        throw new \RuntimeException(sprintf(
            'Gear name "%s" was not found',
            $name
        ));
    }

    /**
     * Return the cost of this item, including its modifications.
     * @return int
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
