<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Armor to protect a character.
 */
class Armor
{
    /**
     * Whether the armor is currently active
     * @var bool
     */
    public bool $active = false;

    /**
     * Availability code
     * @var string
     */
    public string $availability;

    /**
     * Cost of the item
     * @var integer
     */
    public int $cost;

    /**
     * Description of the item
     * @var string
     */
    public string $description;

    /**
     * List of additional effects of the armor
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Name of the item
     * @var string
     */
    public string $name;

    /**
     * ID of the item
     * @var string
     */
    public string $id;

    /**
     * Modifications to the item
     * @var ArmorModificationArray
     */
    public ArmorModificationArray $modifications;

    /**
     * Page the armor was introduced on
     * @var ?int
     */
    public ?int $page;

    /**
     * Armor rating
     * @var int
     */
    public int $rating;

    /**
     * Armor rating for stacking
     * @var ?int
     */
    public ?int $stackRating;

    /**
     * Rulebook for the item
     * @var string
     */
    public string $ruleset;

    /**
     * List of all armor
     * @var ?array<mixed>
     */
    public static ?array $armor;

    /**
     * Construct a new armor object.
     * @param string $id ID to load.
     * @throws \RuntimeException if the ID is invalid.
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'armor.php';
        self::$armor ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$armor[$id])) {
            throw new \RuntimeException(
                sprintf('Armor ID "%s" is invalid', $id)
            );
        }

        $armor = self::$armor[$id];
        $this->availability = $armor['availability'];
        $this->cost = $armor['cost'];
        $this->description = $armor['description'];
        if (isset($armor['effects'])) {
            $this->effects = $armor['effects'];
        }
        $this->id = $id;
        $this->modifications = new ArmorModificationArray();
        $this->name = $armor['name'];
        $this->page = $armor['page'] ?? null;
        $this->rating = $armor['rating'];
        $this->stackRating = $armor['stack-rating'] ?? null;
        $this->ruleset = $armor['ruleset'] ?? 'core';
    }

    /**
     * Return the name of the armor.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Build a new Armor object from a raw Mongo array.
     * @param array<string, mixed> $armor
     * @return Armor
     * @throws \RuntimeException
     */
    public static function build(array $armor): Armor
    {
        $armorItem = new Armor($armor['id']);
        $armorItem->active = $armor['active'] ?? false;
        foreach ($armor['modifications'] ?? [] as $mod) {
            try {
                $armorItem->modifications[] = new ArmorModification($mod);
                continue;
            } catch (\Exception $e) {
            }
            try {
                $armorItem->modifications[] = new GearModification($mod);
                continue;
            } catch (\Exception $e) {
            }
            throw new \RuntimeException(sprintf(
                'Armor/Gear mod not found: %s',
                $mod
            ));
        }
        return $armorItem;
    }

    /**
     * Return an armor based on its name.
     * @param string $name
     * @return Armor
     * @throws \RuntimeException
     */
    public static function findByName(string $name): Armor
    {
        $filename = config('app.data_path.shadowrun5e') . 'armor.php';
        self::$armor ??= require $filename;
        foreach (self::$armor as $armor) {
            if (strtolower($armor['name']) === strtolower($name)) {
                return new Armor($armor['id']);
            }
        }
        throw new \RuntimeException(sprintf(
            'Armor name "%s" was not found',
            $name
        ));
    }

    /**
     * Return the cost of the armor, including modifications.
     * @return int
     */
    public function getCost(): int
    {
        $cost = $this->cost;
        foreach ($this->modifications as $mod) {
            $cost += $mod->getCost($this);
        }
        return $cost;
    }

    /**
     * Return the modified rating of this armor.
     * @return int
     */
    public function getModifiedRating(): int
    {
        $modifiedRating = $this->rating;
        foreach ($this->modifications as $modification) {
            if ([] === $modification->effects) {
                continue;
            }
            foreach ($modification->effects as $effect => $bonus) {
                if ('armor' === $effect) {
                    $modifiedRating += $bonus;
                }
            }
        }
        return $modifiedRating;
    }
}
