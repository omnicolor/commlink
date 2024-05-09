<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use ArrayObject;
use TypeError;

/**
 * Collection of languages.
 * @extends ArrayObject<int, Language>
 * @psalm-suppress UnusedClass
 */
class LanguageArray extends ArrayObject
{
    /**
     * Add a language to the array.
     * @param ?int $index
     * @param Language $language
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet($index = null, $language = null): void
    {
        if ($language instanceof Language) {
            parent::offsetSet($index, $language);
            return;
        }
        throw new TypeError('LanguageArray only accepts Language objects');
    }
}
