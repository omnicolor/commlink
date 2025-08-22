<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Enums\TechStrata;
use Modules\Stillfleet\Enums\VoidwareType;
use Modules\Stillfleet\Models\Gear;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
class GearTest extends TestCase
{
    public function testScopeByComms(): void
    {
        $items = Gear::comms()->get();
        self::assertCount(1, $items);
        self::assertSame('Aleph', (string)$items[0]);
    }

    public function testScopeByDrugs(): void
    {
        $items = Gear::drugs()->get();
        self::assertCount(1, $items);
        $item = $items[0];
        self::assertInstanceOf(Gear::class, $item);
        self::assertSame('Graxanna', (string)$item);
        self::assertSame(VoidwareType::Drug, $item->type);
        self::assertSame(TechStrata::Bio, $item->tech_strata);
    }

    public function testScopeByPets(): void
    {
        $items = Gear::pets()->get();
        self::assertCount(1, $items);
        self::assertSame('Brainrat erotic jesters', (string)$items[0]);
    }

    public function testScopeByVehicles(): void
    {
        $items = Gear::vehicles()->get();
        self::assertCount(1, $items);
        self::assertSame('Automobile', (string)$items[0]);
    }

    public function testScopeByVentureware(): void
    {
        $items = Gear::ventureware()->get();
        self::assertCount(1, $items);
        self::assertSame('Accelerator belt', (string)$items[0]);
    }
}
