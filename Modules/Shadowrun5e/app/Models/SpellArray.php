<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of spells.
 * @extends ArrayObject<int, Spell>
 */
class SpellArray extends ArrayObject
{
    /**
     * Add an item to the array.
     * @param Spell $spell
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $spell = null): void
    {
        if ($spell instanceof Spell) {
            parent::offsetSet($index, $spell);
            return;
        }
        throw new TypeError('SpellArray only accepts Spell objects');
    }
}
