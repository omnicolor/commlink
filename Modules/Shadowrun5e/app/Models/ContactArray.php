<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Contacts.
 * @extends ArrayObject<int|string, Contact>
 */
final class ContactArray extends ArrayObject
{
    /**
     * Add a contact to the array.
     * @param Contact $contact
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $contact = null): void
    {
        if ($contact instanceof Contact) {
            parent::offsetSet($index, $contact);
            return;
        }
        throw new TypeError('ContactArray only accepts Contact objects');
    }
}
