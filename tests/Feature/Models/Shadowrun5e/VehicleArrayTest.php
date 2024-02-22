<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Vehicle;
use App\Models\Shadowrun5e\VehicleArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the VehicleArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class VehicleArrayTest extends TestCase
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
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->vehicles[] = new stdClass();
    }

    /**
     * Test that adding a non-vehicle to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->vehicles->offsetSet(vehicle: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->vehicles);
    }
}
