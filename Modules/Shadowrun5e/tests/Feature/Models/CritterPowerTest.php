<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\CritterPower;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class CritterPowerTest extends TestCase
{
    /**
     * Test trying to load an invalid power.
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
     */
    public function testLoad(): void
    {
        $power = new CritterPower('accident');

        self::assertSame(CritterPower::ACTION_COMPLEX, $power->action);
        self::assertSame(CritterPower::DURATION_INSTANT, $power->duration);
        self::assertSame('Accident', $power->name);
        self::assertSame('Accident', (string)$power);
        self::assertSame(394, $power->page);
        self::assertSame(CritterPower::RANGE_LOS, $power->range);
        self::assertSame(CritterPower::TYPE_PHYSICAL, $power->type);
    }

    /**
     * Test loading a valid power with a subname.
     */
    public function testLoadWithSubname(): void
    {
        $power = new CritterPower('accident', 'Prone');
        self::assertSame('Accident Prone', $power->name);
    }
}
