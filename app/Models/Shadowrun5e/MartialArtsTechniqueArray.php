<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use ArrayObject;
use TypeError;

/**
 * Array of martial arts techniques.
 * @extends ArrayObject<int, MartialArtsTechnique>
 */
class MartialArtsTechniqueArray extends ArrayObject
{
    /**
     * Add a technique to the array.
     * @param MartialArtsTechnique $technique
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $technique = null): void
    {
        if ($technique instanceof MartialArtsTechnique) {
            parent::offsetSet($index, $technique);
            return;
        }
        throw new TypeError(
            'MartialArtsTechniqueArray only accepts MartialArtsTechnique objects'
        );
    }
}
