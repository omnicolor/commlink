<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\RangedWeapon;
use App\Models\Cyberpunkred\Weapon;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for RangedWeapon class.
 * @group models
 * @group cyberpunkred
 * @small
 */
final class RangedWeaponTest extends TestCase
{
    /**
     * Test trying to load a weapon without including an ID.
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
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon ID "invalid" is invalid');
        Weapon::build(['id' => 'invalid']);
    }

    /**
     * Test loading a ranged weapon with the minimal amount of information.
     */
    public function testLoadMinimum(): void
    {
        $weapon = Weapon::build(['id' => 'medium-pistol']);
        self::assertSame('2d6', $weapon->damage);
        self::assertSame(RangedWeapon::QUALITY_STANDARD, $weapon->quality);
    }

    /**
     * Test converting a weapon to a string.
     */
    public function testToString(): void
    {
        $weapon = Weapon::build(['id' => 'medium-pistol']);
        self::assertSame('Medium pistol', (string)$weapon);
    }

    /**
     * Test loading a weapon that sets the quality.
     */
    public function testLoadWithQuality(): void
    {
        $weapon = Weapon::build([
            'id' => 'medium-pistol',
            'quality' => RangedWeapon::QUALITY_EXCELLENT,
        ]);
        self::assertSame(RangedWeapon::QUALITY_EXCELLENT, $weapon->quality);
    }

    /**
     * Test loading a weapon with an invalid quality.
     */
    public function testLoadWithInvalidQuality(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Weapon ID "medium-pistol" has invalid quality "super"'
        );
        Weapon::build(['id' => 'medium-pistol', 'quality' => 'super']);
    }

    /**
     * Test loading a weapon that chooses a name for the weapon.
     */
    public function testLoadWithName(): void
    {
        $weapon = Weapon::build([
            'id' => 'medium-pistol',
            'name' => 'Militech "Avenger"',
        ]);
        self::assertSame(RangedWeapon::QUALITY_STANDARD, $weapon->quality);
        self::assertSame('Militech "Avenger"', (string)$weapon);
    }

    /**
     * Return the different base costs and qualities with how much the weapon
     * should cost.
     * @return array<int, array<int, int|string>>
     */
    public static function costDataProvider(): array
    {
        return [
            [50, RangedWeapon::QUALITY_POOR, 20],
            [50, RangedWeapon::QUALITY_STANDARD, 50],
            [50, RangedWeapon::QUALITY_EXCELLENT, 100],
            [100, RangedWeapon::QUALITY_POOR, 50],
            [100, RangedWeapon::QUALITY_STANDARD, 100],
            [100, RangedWeapon::QUALITY_EXCELLENT, 500],
            [500, RangedWeapon::QUALITY_POOR, 100],
            [500, RangedWeapon::QUALITY_STANDARD, 500],
            [500, RangedWeapon::QUALITY_EXCELLENT, 1000],
        ];
    }

    /**
     * Test getting the cost for a weapon with a base cost and different
     * qualities.
     * @dataProvider costDataProvider
     * @param int $cost
     * @param string $quality
     * @param int $expected
     */
    public function testGetCost(
        int $cost,
        string $quality,
        int $expected
    ): void {
        $weapon = Weapon::build([
            'id' => 'medium-pistol',
            'quality' => $quality,
        ]);
        $weapon->cost = $cost;
        self::assertSame($expected, $weapon->getCost());
    }
}
