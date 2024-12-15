<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of complex forms.
 * @extends ArrayObject<int, ComplexForm>
 */
class ComplexFormArray extends ArrayObject
{
    /**
     * Add a form to the array.
     * @param ComplexForm $form
     * @throws TypeError
     */
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
