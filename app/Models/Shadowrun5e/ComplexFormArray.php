<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

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
     * @psalm-suppress ParamNameMismatch
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
