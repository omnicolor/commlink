<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models;

use Modules\Cyberpunkred\Models\RangedWeapon;
use Modules\Cyberpunkred\Models\Weapon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('cyberpunkred')]
#[Small]
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
     */
    #[DataProvider('costDataProvider')]
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
