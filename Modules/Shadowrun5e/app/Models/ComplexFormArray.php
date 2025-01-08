<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of complex forms.
 * @extends ArrayObject<int, ComplexForm>
 */
final class ComplexFormArray extends ArrayObject
{
    /**
     * Add a form to the array.
     * @param ComplexForm $form
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $form = null): void
    {
        if ($form instanceof ComplexForm) {
            parent::offsetSet($index, $form);
            return;
        }
        throw new TypeError(
            'ComplexFormArray only accepts ComplexForm objects'
        );
    }
}
