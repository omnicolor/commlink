<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Contact;
use App\Models\Shadowrun5E\ContactArray;

/**
 * Tests for the ContactArray class.
 * @covers \App\Models\Shadowrun5E\ContactArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ContactArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var ContactArray<Contact>
     */
    protected ContactArray $contacts;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->contacts = new ContactArray();
    }

    /**
     * Test an empty ContactArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->contacts);
    }

    /**
     * Test adding a contact to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->contacts[] = new Contact([
            'archetype' => 'Fixer',
            'connection' => 1,
            'id' => 0,
            'loyalty' => 2,
            'name' => 'ContactMan',
        ]);
        self::assertNotEmpty($this->contacts);
    }

    /**
     * Test that adding a non-contact to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->contacts[] = new \StdClass();
        self::assertEmpty($this->contacts);
    }

    /**
     * Test that adding a non-contact to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->contacts->offsetSet(contact: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->contacts);
    }
}
