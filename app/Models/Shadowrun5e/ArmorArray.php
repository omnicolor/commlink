<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Collection of armor.
 * @extends \ArrayObject<int, Armor>
 */
class ArmorArray extends \ArrayObject
{
    /**
     * Add an armor to the array.
     * @param ?int $index
     * @param Armor $armor
     * @throws \TypeError
     */
    public function offsetSet($index = null, $armor = null): void
    {
        if ($armor instanceof Armor) {
            parent::offsetSet($index, $armor);
            return;
        }
        throw new \TypeError('ArmorArray only accepts Armor objects');
    }
}
