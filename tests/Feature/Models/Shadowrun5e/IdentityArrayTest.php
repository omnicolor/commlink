<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Identity;
use App\Models\Shadowrun5e\IdentityArray;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the IdentityArray class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class IdentityArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var IdentityArray<Identity>
     */
    protected IdentityArray $identities;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->identities = new IdentityArray();
    }

    /**
     * Test an empty IdentityArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->identities);
    }

    /**
     * Test adding an identity to the array.
     */
    public function testAdd(): void
    {
        $this->identities[] = new Identity();
        self::assertNotEmpty($this->identities);
    }

    /**
     * Test that adding a non-identity to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage(
            'IdentityArray only accepts Identity objects'
        );
        // @phpstan-ignore-next-line
        $this->identities[] = new stdClass();
    }

    /**
     * Test that adding a non-identity to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->identities->offsetSet(identity: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->identities);
    }
}
