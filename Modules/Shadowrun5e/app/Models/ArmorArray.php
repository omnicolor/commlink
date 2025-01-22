<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of armor.
 * @extends ArrayObject<int, Armor>
 */
class ArmorArray extends ArrayObject
{
    /**
     * Add an armor to the array.
     * @param Armor $armor
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $armor = null): void
    {
        if ($armor instanceof Armor) {
            parent::offsetSet($index, $armor);
            return;
        }
        throw new TypeError('ArmorArray only accepts Armor objects');
    }
}
