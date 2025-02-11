<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of sprites.
 * @extends ArrayObject<int, Sprite>
 */
final class SpriteArray extends ArrayObject
{
    /**
     * Add an item to the array.
     * @param Sprite $sprite
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $sprite = null): void
    {
        if ($sprite instanceof Sprite) {
            parent::offsetSet($index, $sprite);
            return;
        }
        throw new TypeError('SpriteArray only accepts Sprite objects');
    }
}
