<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of Augmentations.
 * @extends ArrayObject<int, Augmentation>
 */
class AugmentationArray extends ArrayObject
{
    /**
     * Add an augmentation to the array.
     * @param Augmentation $augmentation
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $augmentation = null): void
    {
        if ($augmentation instanceof Augmentation) {
            parent::offsetSet($index, $augmentation);
            return;
        }
        throw new TypeError(
            'AugmentationArray only accepts Augmentation objects'
        );
    }
}
