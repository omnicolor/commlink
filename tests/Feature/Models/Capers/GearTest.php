<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Explosive;
use App\Models\Capers\Gear;
use App\Models\Capers\Weapon;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Capers gear.
 * @group capers
 * @small
 */
final class GearTest extends TestCase
{
    /**
     * Test creating a valid piece of gear.
     * @test
     */
    public function testLoadGear(): void
    {
        $gear = Gear::get('hotel');
        self::assertSame('Hotel, 10-story, 100-room, furnished', (string)$gear);
        self::assertSame(1, $gear->quantity);
        self::assertSame(650000.0, $gear->cost);
        self::assertSame('real-estate', $gear->type);
    }

    /**
     * Test creating a valid piece of gear with a different quantity.
     * @test
     */
    public function testLoadGearWithQuantity(): void
    {
        $gear = Gear::get('horse', 2);
        self::assertSame(2, $gear->quantity);
    }

    /**
     * Test trying to load an invalid piece of gear.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Gear ID "invalid" is invalid');
        Gear::get('invalid');
    }

    /**
     * Test trying to load a weapon.
     * @test
     */
    public function testLoadWeapon(): void
    {
        $item = Gear::get('club');
        self::assertInstanceOf(Weapon::class, $item);
        self::assertInstanceOf(Gear::class, $item);
        self::assertNotInstanceOf(Explosive::class, $item);
    }

    /**
     * Test trying to load all items, check to see if several types are in the
     * collection.
     * @test
     */
    public function testAll(): void
    {
        $hasExplosive = $hasWeapon = $hasRealEstate = false;
        $gear = Gear::all();
        self::assertNotEmpty($gear);

        foreach ($gear as $item) {
            if ('explosive' === $item->type) {
                $hasExplosive = true;
                continue;
            }
            if ('weapon' === $item->type) {
                $hasWeapon = true;
                continue;
            }
            if ('real-estate' === $item->type) {
                $hasRealEstate = true;
            }
        }
        self::assertTrue($hasExplosive);
        self::assertTrue($hasWeapon);
        self::assertTrue($hasRealEstate);
    }

    /**
     * Test trying to load only explosives.
     * @test
     */
    public function testExplosives(): void
    {
        $explosives = Gear::explosives();
        self::assertNotEmpty($explosives);
        foreach ($explosives as $item) {
            if ('explosive' !== $item->type) {
                self::fail('Non-explosive item returned');
            }
        }
    }

    /**
     * Test extra fields on an explosive.
     * @test
     */
    public function testExplosiveFields(): void
    {
        /** @var Explosive */
        $item = Gear::get('grenade');
        self::assertInstanceOf(Explosive::class, $item);
        self::assertSame('10’ rad.', $item->blast);
        self::assertSame('3 × Suit', $item->damage);
    }

    /**
     * Test trying to load only normal (non-weapon) gear.
     * @test
     */
    public function testNormalGear(): void
    {
        $gear = Gear::normalGear();
        self::assertNotEmpty($gear);
        foreach ($gear as $item) {
            if ('explosive' === $item->type) {
                self::fail('Explosive item returned');
            }
            if ('weapon' === $item->type) {
                self::fail('Weapon returned');
            }
        }
    }

    /**
     * Test trying to load only weapons.
     * @test
     */
    public function testWeapons(): void
    {
        $weapons = Gear::weapons();
        self::assertNotEmpty($weapons);
        foreach ($weapons as $item) {
            if ('weapon' !== $item->type) {
                self::fail('Non-weapon returned');
            }
        }
    }

    /**
     * Test extra fields on a weapon.
     * @test
     */
    public function testWeaponFields(): void
    {
        /** @var Weapon */
        $item = Gear::get('rifle');
        self::assertInstanceOf(Weapon::class, $item);
        self::assertSame('Suit + 1', $item->damage);
        self::assertSame('100/300', $item->range);
        self::assertSame('10', $item->rounds);
    }
}
