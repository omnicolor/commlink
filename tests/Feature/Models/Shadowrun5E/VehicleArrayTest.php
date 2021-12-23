<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Vehicle;
use App\Models\Shadowrun5E\VehicleArray;

/**
 * Tests for the VehicleArray class.
 * @covers \App\Models\Shadowrun5E\VehicleArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class VehicleArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var VehicleArray<Vehicle>
     */
    protected VehicleArray $vehicles;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->vehicles = new VehicleArray();
    }

    /**
     * Test an empty VehicleArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->vehicles);
    }

    /**
     * Test adding a vehicle to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->vehicles[] = new Vehicle(['id' => 'dodge-scoot']);
        self::assertNotEmpty($this->vehicles);
    }

    /**
     * Test that adding a non-vehicle to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->vehicles[] = new \StdClass();
    }

    /**
     * Test that adding a non-vehicle to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->vehicles->offsetSet(vehicle: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->vehicles);
    }
}
