<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\Spirit;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RangeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class SpiritTest extends TestCase
{
    public function testAttributes(): void
    {
        $spirit = Spirit::findOrFail('air');

        self::assertSame('F+3', $spirit->agility);
        self::assertSame('F-2', $spirit->body);
        self::assertSame('F', $spirit->charisma);
        self::assertSame('F', $spirit->intuition);
        self::assertSame('F', $spirit->logic);
        self::assertSame('F+4', $spirit->reaction);
        self::assertSame('F-3', $spirit->strength);
        self::assertSame('F', $spirit->willpower);

        $spirit->force = 1;
        self::assertSame(4, $spirit->agility);
        self::assertSame(1, $spirit->body);
        self::assertSame(1, $spirit->charisma);
        self::assertSame(1, $spirit->force);
        self::assertSame(1, $spirit->intuition);
        self::assertSame(1, $spirit->logic);
        self::assertSame(5, $spirit->reaction);
        self::assertSame(1, $spirit->strength);
        self::assertSame(1, $spirit->willpower);

        $spirit->force = 12;
        self::assertSame(15, $spirit->agility);
        self::assertSame(10, $spirit->body);
        self::assertSame(12, $spirit->charisma);
        self::assertSame(12, $spirit->force);
        self::assertSame(12, $spirit->intuition);
        self::assertSame(12, $spirit->logic);
        self::assertSame(16, $spirit->reaction);
        self::assertSame(9, $spirit->strength);
        self::assertSame(12, $spirit->willpower);
    }

    public function testToString(): void
    {
        $spirit = Spirit::findOrFail('air');
        self::assertSame('Spirit Of Air', (string)$spirit);
    }

    public function testForceTooLow(): void
    {
        $spirit = Spirit::findOrFail('air');
        self::expectException(RangeException::class);
        $spirit->force = 0;
    }
}
