<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\Weapon;
use App\Models\Cyberpunkred\WeaponArray;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the WeaponArray class.
 * @group cyberpunkred
 */
#[Small]
final class WeaponArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var WeaponArray
     */
    protected WeaponArray $weapons;

    /**
     * Set up a clean subject.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->weapons = new WeaponArray();
    }

    /**
     * Test an empty array.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->weapons);
    }

    /**
     * Test adding a weapon to the array.
     */
    public function testAdd(): void
    {
        $this->weapons[] = Weapon::build(['id' => 'medium-pistol']);
        self::assertNotEmpty($this->weapons);
    }

    /**
     * Test that adding something other than a weapon to the array throws an
     * exception.
     */
    public function testAddWrongTypeThrowsException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->weapons[] = new stdClass();
    }

    /**
     * That that adding something other than a weapon to the array doesn't add
     * anything.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->weapons->offsetSet(weapon: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->weapons);
    }
}
