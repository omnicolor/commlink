<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Collection of spells.
 * @extends \ArrayObject<int, Spell>
 */
class SpellArray extends \ArrayObject
{
    /**
     * Add a item to the array.
     * @param ?int $index
     * @param Spell $spell
     * @throws \TypeError
     */
    public function offsetSet($index = null, $spell = null): void
    {
        if ($spell instanceof Spell) {
            parent::offsetSet($index, $spell);
            return;
        }
        throw new \TypeError(
            'SpellArray only accepts Spell objects'
        );
    }
}
