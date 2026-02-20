<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Facades\App\Services\DiceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Stillfleet\Models\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class WeaponTest extends TestCase
{
    public function testStaticCost(): void
    {
        $weapon = Weapon::find('musket');
        self::assertSame(50, $weapon?->cost);
    }

    public function testDynamicCost(): void
    {
        DiceService::shouldReceive('rollDice')
            ->once()
            ->with('d4')
            ->andReturn((object)['total' => 3]);
        $weapon = Weapon::find('club');
        self::assertSame(3, $weapon?->cost);
    }

    public function testFindByOtherName(): void
    {
        $weapon = Weapon::findByOtherName('claw hammer');
        self::assertSame('Club', (string)$weapon);
    }

    public function testFindByOtherNameNotFound(): void
    {
        self::expectException(ModelNotFoundException::class);
        Weapon::findByOtherName('not found');
    }

    public function testScopeByType(): void
    {
        self::assertCount(2, Weapon::melee()->get());
        self::assertCount(1, Weapon::missile()->get());
    }
}
