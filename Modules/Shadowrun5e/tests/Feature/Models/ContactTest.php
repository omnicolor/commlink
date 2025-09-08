<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Contact;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ContactTest extends TestCase
{
    /**
     * Test that the constructor sets all of the properties.
     */
    public function testInitializationWithGmNotes(): void
    {
        $data = [
            'archetype' => 'Fixer',
            'connection' => 1,
            'gmNotes' => 'Notes from the GM',
            'loyalty' => 5,
            'name' => 'Frank the Fixer',
            'notes' => 'Player notes',
        ];
        $contact = new Contact($data);
        self::assertSame('Fixer', $contact->archetype);
        self::assertSame(1, $contact->connection);
        self::assertSame('Notes from the GM', $contact->gmNotes);
        self::assertSame(5, $contact->loyalty);
        self::assertSame('Frank the Fixer', $contact->name);
        self::assertSame('Player notes', $contact->notes);
    }

    /**
     * Test that the constructor sets all of the properties.
     */
    public function testInitializationWithoutGmNotes(): void
    {
        $data = [
            'archetype' => 'Talismonger',
            'connection' => 2,
            'loyalty' => 3,
            'name' => 'Phil',
            'notes' => 'Notes',
        ];
        $contact = new Contact($data);
        self::assertSame('Talismonger', $contact->archetype);
        self::assertSame(2, $contact->connection);
        self::assertSame('', $contact->gmNotes);
        self::assertSame(3, $contact->loyalty);
        self::assertSame('Phil', $contact->name);
        self::assertSame('Notes', $contact->notes);
    }

    /**
     * Test the __toString() method.
     */
    public function testToString(): void
    {
        $data = [
            'archetype' => '',
            'connection' => 0,
            'loyalty' => 0,
            'name' => 'My Name',
            'notes' => '',
        ];
        $contact = new Contact($data);
        self::assertSame('My Name', (string)$contact);
    }
}
