<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\AdeptPower;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for AdeptPower class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class AdeptPowerTest extends TestCase
{
    /**
     * Test trying to load an invalid Power.
     */
    public function testLoadInvalid(): void
    {
        AdeptPower::$powers = null;
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Adept power ID "foo" is invalid');
        new AdeptPower('foo');
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $power = new AdeptPower('improved-sense-direction-sense');
        self::assertEquals(0.25, $power->cost);
        self::assertNotNull($power->description);
        self::assertNotEmpty($power->effects);
        self::assertEquals('improved-sense-direction-sense', $power->id);
        self::assertNull($power->level);
        self::assertEquals('Improved Sense: Direction Sense', $power->name);
        self::assertEquals(310, $power->page);
        self::assertEquals('core', $power->ruleset);
    }

    /**
     * Test the __toString() method.
     */
    public function testToString(): void
    {
        $power = new AdeptPower('improved-sense-direction-sense');
        self::assertEquals('Improved Sense: Direction Sense', (string)$power);
    }
}
