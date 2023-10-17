<?php

declare(strict_types=1);

namespace App\Models\Capers;

use ArrayObject;
use TypeError;

/**
 * Collection of Perks.
 * @extends ArrayObject<int, Perk>
 * @psalm-suppress UnusedClass
 */
class PerkArray extends ArrayObject
{
    /**
     * Add a perk to the array.
     * @param ?int $index
     * @param Perk $perk
     * @throws TypeError
     */
    public function offsetSet($index = null, $perk = null): void
    {
        if ($perk instanceof Perk) {
            parent::offsetSet($index, $perk);
            return;
        }
        throw new TypeError('PerkArray only accepts Perk objects');
    }
}
