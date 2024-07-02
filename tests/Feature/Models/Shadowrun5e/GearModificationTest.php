<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\GearModification;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class GearModificationTest extends TestCase
{
    /**
     * Test trying to load an invalid gear modification.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Gear mod "foo" not found');
        new GearModification('foo');
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $mod = new GearModification('biomonitor');
        self::assertEquals('3', $mod->availability);
        self::assertEquals(1, $mod->capacityCost);
        self::assertEquals('commlink|cyberdeck|rcc', $mod->containerType);
        self::assertEquals(300, $mod->cost);
        self::assertEmpty($mod->effects);
        self::assertEquals('biomonitor', $mod->id);
        self::assertEquals('Biomonitor', $mod->name);
        self::assertNull($mod->page);
        self::assertNull($mod->rating);
        self::assertEquals('core', $mod->ruleset);
        self::assertEmpty($mod->wirelessEffects);
    }

    /**
     * Test the constructor for a more fleshed out mod.
     */
    public function testConstructorRating(): void
    {
        $mod = new GearModification('attack-dongle-2');
        self::assertEquals(0, $mod->capacityCost);
        self::assertEquals('commlink', $mod->containerType);
        self::assertEquals(12000, $mod->cost);
        self::assertEquals(['attack' => 2], $mod->effects);
        self::assertEquals(61, $mod->page);
        self::assertEquals(2, $mod->rating);
        self::assertEquals('data-trails', $mod->ruleset);
    }

    /**
     * Test the __toString() method.
     */
    public function testToString(): void
    {
        $mod = new GearModification('attack-dongle-2');
        self::assertEquals('Attack dongle', (string)$mod);
    }

    /**
     * Test getCost().
     */
    public function testGetCost(): void
    {
        $mod = new GearModification('attack-dongle-2');
        self::assertSame(12000, $mod->getCost());
    }
}
