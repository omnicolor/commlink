<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Models;

use Modules\Capers\Models\Gear;
use Modules\Capers\Models\GearArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('capers')]
#[Small]
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
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->gear = new GearArray();
    }

    /**
     * Test an empty GearArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->gear);
    }

    /**
     * Test adding a Gear item to the array.
     */
    public function testAdd(): void
    {
        $this->gear[] = Gear::get('tire', 4);
        self::assertNotEmpty($this->gear);
    }

    /**
     * Test adding a weapon to the array.
     */
    public function testAddWeapon(): void
    {
        $this->gear[] = Gear::get('knife');
        self::assertNotEmpty($this->gear);
    }

    /**
     * Test adding an explosive to the array.
     */
    public function testAddExplosive(): void
    {
        $this->gear[] = Gear::get('dynamite');
        self::assertNotEmpty($this->gear);
    }

    /**
     * Test that adding a non-gear to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->gear[] = new stdClass();
    }

    /**
     * Test that adding a non-gear to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->gear->offsetSet(gear: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->gear);
    }
}
