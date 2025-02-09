<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

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
