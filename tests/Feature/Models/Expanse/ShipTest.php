<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Ship;
use App\Models\Expanse\ShipSize;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class ShipTest extends TestCase
{
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Expanse ship "not-found" is invalid');
        new Ship('not-found');
    }

    public function testLoad(): void
    {
        $ship = new Ship('DeStRoYeR');
        self::assertSame('Destroyer', (string)$ship);
        self::assertSame(16, $ship->crew_minimum);
        self::assertSame(64, $ship->crew_standard);
        self::assertNull($ship->favored_range);
        self::assertSame([], $ship->favored_stunts);
        self::assertSame([], $ship->flaws);
        self::assertTrue($ship->has_epstein);
        self::assertSame('destroyer', $ship->id);
        self::assertSame('100m', $ship->length);
        self::assertSame(127, $ship->page);
        self::assertCount(2, $ship->qualities);
        self::assertSame('core', $ship->ruleset);
        self::assertSame(2, $ship->sensors);
        self::assertSame(ShipSize::Huge, $ship->size);
        self::assertCount(3, $ship->weapons);
    }

    public function testAll(): void
    {
        self::assertNotEmpty(Ship::all());
    }
}
