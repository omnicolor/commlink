<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\ShipWeapon;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for ship weapons.
 * @group expanse
 * @group models
 * @small
 */
final class ShipWeaponTest extends TestCase
{
    /**
     * Test trying to load an invalid weapon.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Expanse ship weapon "not-found" is invalid'
        );
        new ShipWeapon('not-found', 'fore');
    }

    /**
     * Test loading a valid weapon.
     * @test
     */
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

    /**
     * Test loading all weapons.
     * @test
     */
    public function testAll(): void
    {
        self::assertNotEmpty(ShipWeapon::all());
    }
}
