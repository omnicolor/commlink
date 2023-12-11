<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\MeleeWeapon;
use App\Models\Cyberpunkred\RangedWeapon;
use App\Models\Cyberpunkred\Weapon;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for Weapon abstract class from CyberpunkRed.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class WeaponTest extends TestCase
{
    /**
     * Test trying to load a weapon without including an ID.
     * @test
     */
    public function testLoadNoId(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'ID must be included when instantiating a weapon'
        );
        Weapon::build([]);
    }

    /**
     * Test trying to load an invalid weapon.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon ID "invalid" is invalid');
        Weapon::build(['id' => 'invalid']);
    }

    /**
     * Test converting a weapon to a string.
     * @test
     */
    public function testToString(): void
    {
        $weapon = Weapon::build(['id' => 'medium-pistol']);
        self::assertSame('Medium pistol', (string)$weapon);
    }

    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon "Not found" was not found');
        Weapon::findByName('Not found');
    }

    public function testFindByNameRanged(): void
    {
        $weapon = Weapon::findByName('mEDIUM pISTOL');
        self::assertInstanceOf(RangedWeapon::class, $weapon);
        self::assertSame('medium-pistol', $weapon->id);
    }

    public function testFindByNameMelee(): void
    {
        $weapon = Weapon::findByName('Medium melee');
        self::assertInstanceOf(MeleeWeapon::class, $weapon);
        self::assertSame('medium-melee', $weapon->id);
    }
}
