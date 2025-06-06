<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Perks.
 * @extends ArrayObject<int, Perk>
 */
class PerkArray extends ArrayObject
{
    /**
     * Add a perk to the array.
     * @param Perk $perk
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $perk = null): void
    {
        if ($perk instanceof Perk) {
            parent::offsetSet($index, $perk);
            return;
        }
        throw new TypeError('PerkArray only accepts Perk objects');
    }
}
