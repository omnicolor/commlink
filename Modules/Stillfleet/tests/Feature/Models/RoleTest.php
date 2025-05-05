<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Enums\AdvancedPowersCategory;
use Modules\Stillfleet\Models\Power;
use Modules\Stillfleet\Models\Role;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class RoleTest extends TestCase
{
    public function testToString(): void
    {
        $role = Role::findOrFail('banshee');
        self::assertSame('Banshee', (string)$role);
    }

    public function testGrit(): void
    {
        $role = Role::findOrFail('banshee');
        self::assertSame(['movement', 'reason'], $role->grit);
    }

    public function testAdvancePowersLists(): void
    {
        $role = Role::findOrFail('banshee');
        self::assertSame([AdvancedPowersCategory::Communications], $role->advanced_powers_lists);
    }

    public function testMarqueePower(): void
    {
        $role = Role::findOrFail('banshee');
        self::assertEquals(Power::find('tack'), $role->marquee_power);
    }

    public function testOptionalPowers(): void
    {
        $role = Role::findOrFail('banshee');
        self::assertEquals(
            [
                Power::find('astrogate'),
                Power::find('interface'),
                Power::find('power-up'),
                Power::find('reposition'),
            ],
            $role->optional_powers,
        );
    }

    public function testResponsibilities(): void
    {
        $role = Role::findOrFail('banshee');
        self::assertCount(3, $role->responsibilities);
    }

    public function testAll(): void
    {
        self::assertNotEmpty(Role::all());
    }

    public function testPowersEmpty(): void
    {
        $role = Role::findOrFail('banshee');
        self::assertCount(3, $role->powers);
    }

    public function testPowersWithOptional(): void
    {
        $role = Role::findOrFail('banshee'); //, 1, ['astrogate']
        $role->addPowers(Power::findOrFail('astrogate'));
        self::assertCount(4, $role->powers);
    }
}
