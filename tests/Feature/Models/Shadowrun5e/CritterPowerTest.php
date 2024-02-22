<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\CritterPower;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for the critter/spirit power class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class CritterPowerTest extends TestCase
{
    /**
     * Test trying to load an invalid power.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Critter/Spirit power "not-found" is invalid'
        );
        new CritterPower('not-found');
    }

    /**
     * Test loading a valid power.
     * @test
     */
    public function testLoad(): void
    {
        $power = new CritterPower('accident');

        self::assertSame(CritterPower::ACTION_COMPLEX, $power->action);
        self::assertNotNull($power->description);
        self::assertSame(CritterPower::DURATION_INSTANT, $power->duration);
        self::assertSame('Accident', $power->name);
        self::assertSame('Accident', (string)$power);
        self::assertSame(394, $power->page);
        self::assertSame(CritterPower::RANGE_LOS, $power->range);
        self::assertSame(CritterPower::TYPE_PHYSICAL, $power->type);
    }

    /**
     * Test loading a valid power with a subname.
     * @test
     */
    public function testLoadWithSubname(): void
    {
        $power = new CritterPower('accident', 'Prone');
        self::assertSame('Accident Prone', $power->name);
    }
}
