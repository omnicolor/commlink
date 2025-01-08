<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of programs.
 * @extends ArrayObject<int, Program>
 */
final class ProgramArray extends ArrayObject
{
    /**
     * Add an item to the array.
     * @param Program $program
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $program = null): void
    {
        if ($program instanceof Program) {
            parent::offsetSet($index, $program);
            return;
        }
        throw new TypeError('ProgramArray only accepts Program objects');
    }
}
