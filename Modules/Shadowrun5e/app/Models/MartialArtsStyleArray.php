<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Array of martial arts styles.
 * @extends ArrayObject<int, MartialArtsStyle>
 */
class MartialArtsStyleArray extends ArrayObject
{
    /**
     * Add a style to the array.
     * @param MartialArtsStyle $style
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $style = null): void
    {
        if ($style instanceof MartialArtsStyle) {
            parent::offsetSet($index, $style);
            return;
        }
        throw new TypeError(
            'MartialArtsStyleArray only accepts MartialArtsStyle objects'
        );
    }
}
