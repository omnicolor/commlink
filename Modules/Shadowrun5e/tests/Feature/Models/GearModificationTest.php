<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\GearModification;
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
        self::assertSame(1, $mod->capacityCost);
        self::assertSame('commlink|cyberdeck|rcc', $mod->containerType);
        self::assertSame(300, $mod->cost);
        self::assertEmpty($mod->effects);
        self::assertSame('biomonitor', $mod->id);
        self::assertSame('Biomonitor', $mod->name);
        self::assertNull($mod->page);
        self::assertNull($mod->rating);
        self::assertSame('core', $mod->ruleset);
        self::assertEmpty($mod->wirelessEffects);
    }

    /**
     * Test the constructor for a more fleshed out mod.
     */
    public function testConstructorRating(): void
    {
        $mod = new GearModification('attack-dongle-2');
        self::assertSame(0, $mod->capacityCost);
        self::assertSame('commlink', $mod->containerType);
        self::assertSame(12000, $mod->cost);
        self::assertSame(['attack' => 2], $mod->effects);
        self::assertSame(61, $mod->page);
        self::assertSame(2, $mod->rating);
        self::assertSame('data-trails', $mod->ruleset);
    }

    /**
     * Test the __toString() method.
     */
    public function testToString(): void
    {
        $mod = new GearModification('attack-dongle-2');
        self::assertSame('Attack dongle', (string)$mod);
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
