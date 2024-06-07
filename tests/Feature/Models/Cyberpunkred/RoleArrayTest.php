<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\Role;
use App\Models\Cyberpunkred\Role\Fixer;
use App\Models\Cyberpunkred\RoleArray;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the RoleArray class.
 * @group cyberpunkred
 */
#[Small]
final class RoleArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var RoleArray<Role>
     */
    protected RoleArray $roles;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->roles = new RoleArray();
    }

    /**
     * Test an empty RoleArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->roles);
    }

    /**
     * Test adding a role to the array.
     */
    public function testAdd(): void
    {
        $this->roles[] = new Fixer([
            'rank' => 4,
            'type' => Fixer::TYPE_BROKER_DEALS,
        ]);
        self::assertNotEmpty($this->roles);
    }

    /**
     * Test that adding something other than a Role to the array throws an
     * exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('RoleArray only accepts Role objects');
        // @phpstan-ignore-next-line
        $this->roles[] = new stdClass();
    }

    /**
     * Test that adding something other than a Role to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->roles->offsetSet(role: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->roles);
    }
}
