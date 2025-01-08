<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Array of martial arts techniques.
 * @extends ArrayObject<int, MartialArtsTechnique>
 */
final class MartialArtsTechniqueArray extends ArrayObject
{
    /**
     * Add a technique to the array.
     * @param MartialArtsTechnique $technique
     * @throws TypeError
     */
    #[Override]
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
