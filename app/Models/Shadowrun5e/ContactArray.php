<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use ArrayObject;
use TypeError;

/**
 * Collection of Contacts.
 * @extends ArrayObject<int|string, Contact>
 */
class ContactArray extends ArrayObject
{
    /**
     * Add a contact to the array.
     * @param Contact $contact
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $contact = null): void
    {
        if ($contact instanceof Contact) {
            parent::offsetSet($index, $contact);
            return;
        }
        throw new TypeError('ContactArray only accepts Contact objects');
    }
}
