<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\WeaponModification;
use Modules\Shadowrun5e\Models\WeaponModificationArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class WeaponModificationArrayTest extends TestCase
{
    /**
     * Subject under test.
     */
    protected WeaponModificationArray $mods;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->mods = new WeaponModificationArray();
    }

    /**
     * Test an empty array.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->mods);
    }

    /**
     * Test adding to the array.
     */
    public function testAppend(): void
    {
        $this->mods[] = new WeaponModification('bayonet');
        self::assertNotEmpty($this->mods);
    }

    /**
     * Test setting up a weapon's slots.
     */
    public function testInitializeSlots(): void
    {
        $this->mods['barrel'] = null;
        $this->mods['internal'] = null;

        self::assertCount(2, $this->mods);
    }

    /**
     * Test adding a modification to a weapon's slot.
     */
    public function testAddToSlot(): void
    {
        $this->mods['barrel'] = new WeaponModification('bayonet');
        self::assertSame('Bayonet', (string)$this->mods['barrel']);
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
     * Test that adding the wrong type to the array doesn't add the object.
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
