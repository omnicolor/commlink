<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\ArmorModification;
use Modules\Shadowrun5e\Models\ArmorModificationArray;
use Modules\Shadowrun5e\Models\GearModification;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ArmorModificationArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var ArmorModificationArray<ArmorModification|GearModification>
     */
    protected ArmorModificationArray $mods;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mods = new ArmorModificationArray();
    }

    /**
     * Test an empty ArmorModificationArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->mods);
    }

    /**
     * Test adding an armor mod to the array.
     */
    public function testAddArmorMod(): void
    {
        $this->mods[] = new ArmorModification('auto-injector');
        self::assertNotEmpty($this->mods);
    }

    /**
     * Test adding a gear mod to the array.
     */
    public function testAddGearMod(): void
    {
        $this->mods[] = new GearModification('biomonitor');
        self::assertNotEmpty($this->mods);
    }

    /**
     * Test that adding a non-armor mod to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage(
            'ArmorModificationArray only accepts Armor- or GearModification objects'
        );
        // @phpstan-ignore-next-line
        $this->mods[] = new stdClass();
    }

    /**
     * Test that adding a non-armor mod to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->mods->offsetSet(mod: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->mods);
    }
}
