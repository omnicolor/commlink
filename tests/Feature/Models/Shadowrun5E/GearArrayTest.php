<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Gear;
use App\Models\Shadowrun5E\GearArray;
use App\Models\Shadowrun5E\GearFactory;

/**
 * Tests for the GearArray class.
 * @covers \App\Models\Shadowrun5E\GearArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class GearArrayTest extends \Tests\TestCase
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
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->gears);
    }

    /**
     * Test adding a normal piece of gear to the array.
     * @test
     */
    public function testAddGear(): void
    {
        $this->gears[] = GearFactory::get('credstick-gold');
        self::assertNotEmpty($this->gears);
    }

    /**
     * Test adding a matrix-device to the array.
     * @test
     */
    public function testAddCommlink(): void
    {
        $this->gears[] = GearFactory::get(['id' => 'commlink-sony-angel']);
        self::assertNotEmpty($this->gears);
    }

    /**
     * Test that adding a non-gear to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->gears[] = new \StdClass();
    }

    /**
     * Test that adding a non-gear to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->gears[] = new \StdClass();
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->gears);
    }
}
