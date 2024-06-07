<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Gear;
use App\Models\Shadowrun5e\GearArray;
use App\Models\Shadowrun5e\GearFactory;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the GearArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class GearArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var GearArray<Gear>
     */
    protected GearArray $gears;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->gears = new GearArray();
    }

    /**
     * Test an empty GearArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->gears);
    }

    /**
     * Test adding a normal piece of gear to the array.
     */
    public function testAddGear(): void
    {
        $this->gears[] = GearFactory::get('credstick-gold');
        self::assertNotEmpty($this->gears);
    }

    /**
     * Test adding a matrix-device to the array.
     */
    public function testAddCommlink(): void
    {
        $this->gears[] = GearFactory::get(['id' => 'commlink-sony-angel']);
        self::assertNotEmpty($this->gears);
    }

    /**
     * Test that adding a non-gear to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->gears[] = new stdClass();
    }

    /**
     * Test that adding a non-gear to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->gears->offsetSet(gear: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->gears);
    }
}
