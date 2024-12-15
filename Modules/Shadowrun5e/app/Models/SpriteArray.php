<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of sprites.
 * @extends ArrayObject<int, Sprite>
 */
class SpriteArray extends ArrayObject
{
    /**
     * Add a item to the array.
     * @param Sprite $sprite
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $sprite = null): void
    {
        if ($sprite instanceof Sprite) {
            parent::offsetSet($index, $sprite);
            return;
        }
        throw new TypeError('SpriteArray only accepts Sprite objects');
    }
}
