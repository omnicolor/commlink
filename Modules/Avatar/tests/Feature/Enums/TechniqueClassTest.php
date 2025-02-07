<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Enums;

use Modules\Avatar\Enums\TechniqueClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class TechniqueClassTest extends TestCase
{
    public function testName(): void
    {
        self::assertSame(
            'Advance and Attack',
            TechniqueClass::AdvanceAndAttack->name(),
        );
        self::assertSame(
            'Defend and Maneuver',
            TechniqueClass::DefendAndManeuver->name(),
        );
        self::assertSame(
            'Evade and Observe',
            TechniqueClass::EvadeAndObserve->name(),
        );
    }
}
