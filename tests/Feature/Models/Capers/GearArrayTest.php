<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Gear;
use App\Models\Capers\GearArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the GearArray class.
 * @group capers
 * @group models
 * @small
 */
final class GearArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var GearArray<Gear>
     */
    protected GearArray $gear;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->gear = new GearArray();
    }

    /**
     * Test an empty GearArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->gear);
    }

    /**
     * Test adding a Gear item to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->gear[] = Gear::get('tire', 4);
        self::assertNotEmpty($this->gear);
    }

    /**
     * Test adding a weapon to the array.
     * @test
     */
    public function testAddWeapon(): void
    {
        $this->gear[] = Gear::get('knife');
        self::assertNotEmpty($this->gear);
    }

    /**
     * Test adding an explosive to the array.
     * @test
     */
    public function testAddExplosive(): void
    {
        $this->gear[] = Gear::get('dynamite');
        self::assertNotEmpty($this->gear);
    }

    /**
     * Test that adding a non-gear to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->gear[] = new stdClass();
    }

    /**
     * Test that adding a non-gear to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->gear->offsetSet(gear: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->gear);
    }
}
