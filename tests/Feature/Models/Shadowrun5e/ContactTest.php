<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Contact;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Contact class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ContactTest extends TestCase
{
    /**
     * Test that the constructor sets all of the properties.
     * @test
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
        self::assertEquals('Fixer', $contact->archetype);
        self::assertEquals(1, $contact->connection);
        self::assertEquals('Notes from the GM', $contact->gmNotes);
        self::assertEquals(5, $contact->loyalty);
        self::assertEquals('Frank the Fixer', $contact->name);
        self::assertEquals('Player notes', $contact->notes);
    }

    /**
     * Test that the constructor sets all of the properties.
     * @test
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
        self::assertEquals('Talismonger', $contact->archetype);
        self::assertEquals(2, $contact->connection);
        self::assertEquals('', $contact->gmNotes);
        self::assertEquals(3, $contact->loyalty);
        self::assertEquals('Phil', $contact->name);
        self::assertEquals('Notes', $contact->notes);
    }

    /**
     * Test the __toString() method.
     * @test
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
        self::assertEquals('My Name', (string)$contact);
    }
}
