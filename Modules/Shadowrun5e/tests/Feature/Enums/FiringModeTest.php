<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Enums;

use Modules\Shadowrun5e\Enums\FiringMode;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class FiringModeTest extends TestCase
{
    public function testName(): void
    {
        self::assertSame('Single-shot', FiringMode::SingleShot->name());
        self::assertSame('Semi-automatic', FiringMode::SemiAutomatic->name());
        self::assertSame('Burst fire', FiringMode::BurstFire->name());
        self::assertSame('Full auto', FiringMode::FullAuto->name());
    }
}
