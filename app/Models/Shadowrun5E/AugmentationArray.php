<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Collection of Augmentations.
 * @extends \ArrayObject<int, Augmentation>
 */
class AugmentationArray extends \ArrayObject
{
    /**
     * Add an augmentation to the array.
     * @param int|null $index
     * @param Augmentation $augmentation
     * @throws \TypeError
     */
    public function offsetSet($index = null, $augmentation = null): void
    {
        if ($augmentation instanceof Augmentation) {
            parent::offsetSet($index, $augmentation);
            return;
        }
        throw new \TypeError(
            'AugmentationArray only accepts Augmentation objects'
        );
    }
}
