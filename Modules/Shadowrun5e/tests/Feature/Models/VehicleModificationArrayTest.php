<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\VehicleModification;
use Modules\Shadowrun5e\Models\VehicleModificationArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class VehicleModificationArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var VehicleModificationArray<VehicleModification>
     */
    private VehicleModificationArray $mods;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->mods = new VehicleModificationArray();
    }

    /**
     * Test an empty VehicleModificationArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->mods);
    }

    /**
     * Test adding an item to the array.
     */
    public function testAdd(): void
    {
        $this->mods[] = new VehicleModification('manual-control-override');
        self::assertNotEmpty($this->mods);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->mods[] = new stdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->mods->offsetSet(mod: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->mods);
    }
}
