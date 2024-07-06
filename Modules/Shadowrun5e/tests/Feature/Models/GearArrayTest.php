<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Gear;
use Modules\Shadowrun5e\Models\GearArray;
use Modules\Shadowrun5e\Models\GearFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
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
