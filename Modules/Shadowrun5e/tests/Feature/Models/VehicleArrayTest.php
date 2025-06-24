<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Vehicle;
use Modules\Shadowrun5e\Models\VehicleArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
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
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->vehicles = new VehicleArray();
    }

    /**
     * Test an empty VehicleArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->vehicles);
    }

    /**
     * Test adding a vehicle to the array.
     */
    public function testAdd(): void
    {
        $this->vehicles[] = new Vehicle(['id' => 'dodge-scoot']);
        self::assertNotEmpty($this->vehicles);
    }

    /**
     * Test that adding a non-vehicle to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->vehicles[] = new stdClass();
    }

    /**
     * Test that adding a non-vehicle to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->vehicles->offsetSet(vehicle: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->vehicles);
    }
}
