<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\ShipWeapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class ShipWeaponTest extends TestCase
{
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Expanse ship weapon "not-found" is invalid'
        );
        new ShipWeapon('not-found', 'fore');
    }

    public function testLoad(): void
    {
        $weapon = new ShipWeapon('ToRpEdO', 'fore');
        self::assertSame('4d6', $weapon->damage);
        self::assertSame('ToRpEdO', $weapon->id);
        self::assertSame('Torpedo', (string)$weapon);
        self::assertSame(133, $weapon->page);
        self::assertSame(ShipWeapon::RANGE_LONG, $weapon->range);
        self::assertSame('core', $weapon->ruleset);
    }

    public function testAll(): void
    {
        self::assertNotEmpty(ShipWeapon::all());
    }
}
