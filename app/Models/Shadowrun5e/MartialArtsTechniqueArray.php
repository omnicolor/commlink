<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Array of martial arts techniques.
 * @extends \ArrayObject<int, MartialArtsTechnique>
 */
class MartialArtsTechniqueArray extends \ArrayObject
{
    /**
     * Add a technique to the array.
     * @param ?int $index
     * @param MartialArtsTechnique $technique
     * @throws \TypeError
     */
    public function offsetSet($index = null, $technique = null): void
    {
        if ($technique instanceof MartialArtsTechnique) {
            parent::offsetSet($index, $technique);
            return;
        }
        throw new \TypeError(
            'MartialArtsTechniqueArray only accepts MartialArtsTechnique objects'
        );
    }
}
