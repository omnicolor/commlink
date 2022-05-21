<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Collection of Contacts.
 * @extends \ArrayObject<int, Contact>
 */
class ContactArray extends \ArrayObject
{
    /**
     * Add a contact to the array.
     * @param ?int $index
     * @param Contact $contact
     * @throws \TypeError
     */
    public function offsetSet($index = null, $contact = null): void
    {
        if ($contact instanceof Contact) {
            parent::offsetSet($index, $contact);
            return;
        }
        throw new \TypeError('ContactArray only accepts Contact objects');
    }
}
