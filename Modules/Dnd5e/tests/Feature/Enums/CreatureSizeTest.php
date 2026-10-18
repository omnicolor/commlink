<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\Enums;

use Modules\Dnd5e\Enums\CreatureSize;
use PHPUnit\Framework\TestCase;

final class CreatureSizeTest extends TestCase
{
    public function testSpace(): void
    {
        self::assertSame(2.5, CreatureSize::Tiny->space());
        self::assertSame(5.0, CreatureSize::Small->space());
        self::assertSame(5.0, CreatureSize::Medium->space());
        self::assertSame(10.0, CreatureSize::Large->space());
        self::assertSame(15.0, CreatureSize::Huge->space());
        self::assertSame(20.0, CreatureSize::Gargantuan->space());
    }
}
