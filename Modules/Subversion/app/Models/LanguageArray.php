<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of languages.
 * @extends ArrayObject<int, Language>
 */
class LanguageArray extends ArrayObject
{
    /**
     * Add a language to the array.
     * @param ?int $index
     * @param Language $language
     * @throws TypeError
     */
    #[Override]
    public function offsetSet($index = null, $language = null): void
    {
        if ($language instanceof Language) {
            parent::offsetSet($index, $language);
            return;
        }
        throw new TypeError('LanguageArray only accepts Language objects');
    }
}
