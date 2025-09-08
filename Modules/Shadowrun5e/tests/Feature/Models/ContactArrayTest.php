<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Contact;
use Modules\Shadowrun5e\Models\ContactArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ContactArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var ContactArray<Contact>
     */
    private ContactArray $contacts;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->contacts = new ContactArray();
    }

    /**
     * Test an empty ContactArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->contacts);
    }

    /**
     * Test adding a contact to the array.
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
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->contacts[] = new stdClass();
        self::assertEmpty($this->contacts);
    }

    /**
     * Test that adding a non-contact to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->contacts->offsetSet(contact: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->contacts);
    }
}
