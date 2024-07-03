<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models;

use Modules\Cyberpunkred\Models\MeleeWeapon;
use Modules\Cyberpunkred\Models\RangedWeapon;
use Modules\Cyberpunkred\Models\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class WeaponTest extends TestCase
{
    public function testLoadNoId(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'ID must be included when instantiating a weapon'
        );
        Weapon::build([]);
    }

    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon ID "invalid" is invalid');
        Weapon::build(['id' => 'invalid']);
    }

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
