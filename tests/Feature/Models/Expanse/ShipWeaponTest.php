<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\ShipWeapon;
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
        self::assertNotNull($weapon->description);
        self::assertSame('torpedo', $weapon->id);
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
