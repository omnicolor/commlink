<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Commlink;
use App\Models\Shadowrun5e\GearModification;
use App\Models\Shadowrun5e\Program;
use Tests\TestCase;

/**
 * Tests for the Commlink class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class CommlinkTest extends TestCase
{
    /**
     * Test initializing a commlink device with an attributes array.
     */
    public function testSettingAttributesArray(): void
    {
        $commlink = new Commlink('cyberdeck-ares-echo-unlimited');
        self::assertSame([9, 6, 4, 5], $commlink->attributes);
        self::assertFalse($commlink->configurable);
        self::assertSame(3, $commlink->programsAllowed);
        self::assertSame(11, $commlink->getConditionMonitor());
    }

    /**
     * Test setting the attributes for a commlink.
     */
    public function testAttributesForCommlink(): void
    {
        $commlink = new Commlink('commlink-sony-angel');
        self::assertSame([null, null, 1, 1], $commlink->attributes);
        self::assertFalse($commlink->configurable);
        self::assertSame(1, $commlink->programsAllowed);
        self::assertSame(9, $commlink->getConditionMonitor());
    }

    /**
     * Test setting the attributes for a configurable cyberdeck.
     */
    public function testAttributesForConfigurableCyberdeck(): void
    {
        $commlink = new Commlink('cyberdeck-evo-sublime');
        self::assertSame([7, 6, 5, 5], $commlink->attributes);
        self::assertTrue($commlink->configurable);
        self::assertSame(4, $commlink->programsAllowed);
        self::assertSame(10, $commlink->getConditionMonitor());
    }

    /**
     * Test handling a device with no rating (which shouldn't happen).
     */
    public function testNoRatingConditionMonitor(): void
    {
        $commlink = new Commlink('cyberdeck-evo-sublime');
        $commlink->rating = null;
        self::assertSame(0, $commlink->getConditionMonitor());
    }

    /**
     * Test getCost() on a modified Commlink with a program.
     */
    public function testGetCost(): void
    {
        $commlink = new Commlink('commlink-sony-angel');
        $commlink->modifications[] = new GearModification('attack-dongle-2');
        $commlink->programs[] = new Program('armor');

        self::assertSame(12350, $commlink->getCost());
    }
}
