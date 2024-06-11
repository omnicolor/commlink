<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use ArrayObject;
use TypeError;

/**
 * Collection of programs.
 * @extends ArrayObject<int, Program>
 */
class ProgramArray extends ArrayObject
{
    /**
     * Add a item to the array.
     * @param Program $program
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $program = null): void
    {
        if ($program instanceof Program) {
            parent::offsetSet($index, $program);
            return;
        }
        throw new TypeError('ProgramArray only accepts Program objects');
    }
}
