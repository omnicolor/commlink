<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Exception;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

class Armor implements Stringable
{
    /**
     * Whether the armor is currently active.
     */
    public bool $active = false;

    /**
     * Availability code.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $availability;

    /**
     * Cost of the item.
     */
    public int $cost;

    /**
     * Description of the item.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * List of additional effects of the armor.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Name of the item.
     */
    public string $name;

    /**
     * Modifications to the item.
     */
    public ArmorModificationArray $modifications;

    /**
     * Page the armor was introduced on.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public ?int $page;

    /**
     * Armor rating.
     */
    public int $rating;

    /**
     * Armor rating for stacking.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public ?int $stackRating;

    /**
     * Rulebook for the item.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $ruleset;

    /**
     * List of all armor.
     * @var ?array<mixed>
     */
    public static ?array $armor;

    /**
     * Construct a new armor object.
     * @throws RuntimeException if the ID is invalid.
     */
    public function __construct(public string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'armor.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$armor ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$armor[$id])) {
            throw new RuntimeException(
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
        $this->modifications = new ArmorModificationArray();
        $this->name = $armor['name'];
        $this->page = $armor['page'] ?? null;
        $this->rating = $armor['rating'];
        $this->stackRating = $armor['stack-rating'] ?? null;
        $this->ruleset = $armor['ruleset'] ?? 'core';
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Build a new Armor object from a raw Mongo array.
     * @param array<string, mixed> $armor
     * @throws RuntimeException
     */
    public static function build(array $armor): Armor
    {
        $armorItem = new Armor($armor['id']);
        $armorItem->active = $armor['active'] ?? false;
        foreach ($armor['modifications'] ?? [] as $mod) {
            try {
                $armorItem->modifications[] = new ArmorModification($mod);
                continue;
            } catch (Exception) {
                // Ignore, could be a GearModification.
            }
            try {
                $armorItem->modifications[] = new GearModification($mod);
                continue;
            } catch (Exception) {
                // Ignore, we'll throw a different exception in a sec.
            }
            throw new RuntimeException(sprintf(
                'Armor/Gear mod not found: %s',
                $mod
            ));
        }
        return $armorItem;
    }

    /**
     * Return an armor based on its name.
     * @throws RuntimeException
     */
    public static function findByName(string $name): Armor
    {
        $filename = config('shadowrun5e.data_path') . 'armor.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$armor ??= require $filename;
        foreach (self::$armor as $armor) {
            if (strtolower((string) $armor['name']) === strtolower($name)) {
                return new Armor($armor['id']);
            }
        }
        throw new RuntimeException(sprintf(
            'Armor name "%s" was not found',
            $name
        ));
    }

    /**
     * Return the cost of the armor, including modifications.
     * @psalm-suppress PossiblyUnusedMethod
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
