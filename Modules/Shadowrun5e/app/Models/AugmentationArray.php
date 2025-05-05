<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Augmentations.
 * @extends ArrayObject<int, Augmentation>
 */
final class AugmentationArray extends ArrayObject
{
    /**
     * Add an augmentation to the array.
     * @param Augmentation $augmentation
     * @throws TypeError
     */
    #[Override]
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
