<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\AdeptPower;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class AdeptPowerTest extends TestCase
{
    public function testToString(): void
    {
        $power = AdeptPower::findOrFail('adrenaline-boost-1');
        self::assertSame('Adrenaline Boost (1)', (string)$power);
    }

    public function testEffects(): void
    {
        $power = AdeptPower::findOrFail('adrenaline-boost-1');
        self::assertSame(
            ['initiative-score' => 2],
            $power->effects,
        );

        $power = AdeptPower::findOrFail('astral-perception');
        self::assertNull($power->effects);
    }
}
