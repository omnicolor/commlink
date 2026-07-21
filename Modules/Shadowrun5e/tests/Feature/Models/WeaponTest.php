<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Enums\WeaponRange;
use Modules\Shadowrun5e\Models\Weapon;
use Modules\Shadowrun5e\Models\WeaponModification;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;
use Tests\TestCase;

#[CoversClass(Weapon::class)]
#[CoversClass(WeaponRange::class)]
#[CoversClass(WeaponModification::class)]
#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class WeaponTest extends TestCase
{
    private Weapon $weapon;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->weapon = new Weapon('ak-98');
    }

    #[Test]
    #[TestDox('Loading a weapon with an invalid ID throws an exception')]
    public function testWeaponNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon ID "not-found" is invalid');
        new Weapon('not-found');
    }

    /**
     * Test that loading a weapon sets its ID.
     */
    public function testWeaponHasId(): void
    {
        self::assertSame('ak-98', $this->weapon->id);
    }

    /**
     * Test that loading a weapon sets its availability.
     */
    public function testWeaponHasAvailability(): void
    {
        self::assertSame('8F', $this->weapon->availability);
    }

    /**
     * Test that loading a weapon sets its cost.
     */
    public function testWeaponHasCost(): void
    {
        self::assertSame(1250, $this->weapon->cost);
    }

    /**
     * Test that loading a weapon sets its damage.
     */
    public function testWeaponHasDamage(): void
    {
        self::assertSame('10P', $this->weapon->damage);
    }

    /**
     * Test that loading a weapon sets its name.
     */
    public function testWeaponHasName(): void
    {
        self::assertSame('AK-98', $this->weapon->name);
    }

    #[Test]
    #[TestDox('Weapons without subname return null for their subname')]
    public function testWeaponDoesntHaveSubname(): void
    {
        self::assertNull($this->weapon->subname);
    }

    #[Test]
    #[TestDox('Weapons with a ruleset in the data set it')]
    public function testWeaponSetsRuleset(): void
    {
        self::assertSame('run-and-gun', $this->weapon->ruleset);
    }

    #[Test]
    #[TestDox('Weapons that have no ruleset in the data default to core')]
    public function testWeaponDoesntSetRuleset(): void
    {
        $weapon = new Weapon('ares-predator-v');
        self::assertSame('core', $weapon->ruleset);
    }

    #[Test]
    #[TestDox('A weapon that has no range in the data uses its weapon class to load the range')]
    public function testWeaponWithoutRange(): void
    {
        $weapon = new Weapon('defiance-t-250-short');
        self::assertSame(WeaponRange::Shotgun, $weapon->range);
    }

    #[Test]
    #[TestDox('A weapon that has a text range in the data returns the correct range enum')]
    public function testWeaponWithTextRange(): void
    {
        $weapon = new Weapon('ares-predator-v');
        self::assertSame(WeaponRange::HeavyPistol, $weapon->range);
    }

    #[Test]
    #[TestDox('A weapon with an enum in the data returns the correct range')]
    public function testWeaponWithEnumRange(): void
    {
        $weapon = new Weapon('hk-227');
        self::assertSame(WeaponRange::SubmachineGun, $weapon->range);
    }

    #[Test]
    #[TestDox('Casting a weapon to string returns its name')]
    public function testCastWeaponToString(): void
    {
        self::assertSame('AK-98', (string)$this->weapon);
    }

    #[Test]
    #[TestDox('Unarmed strike returns correct information')]
    public function testUnarmedStrike(): void
    {
        $weapon = new Weapon('unarmed-strike');
        self::assertSame('physical', $weapon->accuracy);
        self::assertSame('(STR)', $weapon->damage);
        self::assertSame(
            'Unarmed Combat covers the various '
                . 'self-defense and attack moves that employ the body as a '
                . 'primary weapon. This includes a wide array of martial arts '
                . 'along with the use of cybernetic implant weaponry and the '
                . 'fighting styles that sprung up around those implants.',
            $weapon->description
        );
        self::assertSame('unarmed-strike', $weapon->id);
        self::assertSame('Unarmed Strike', $weapon->name);
        self::assertSame(0, $weapon->reach);
        self::assertSame(132, $weapon->page);
        self::assertSame('core', $weapon->ruleset);
        self::assertSame('unarmed-combat', $weapon->skill->id);
    }

    #[Test]
    #[TestDox('Weapons with integrated modifications include the mod when loaded')]
    public function testWeaponWithIntegratedModification(): void
    {
        $weapon = new Weapon('ares-predator-v');
        self::assertNotEmpty($weapon->modifications);
        self::assertInstanceOf(
            WeaponModification::class,
            $weapon->modifications[0]
        );
    }

    #[Test]
    #[TestDox('Weapons with invalid modifications load, but do not include the invalid mod')]
    public function testLoadingWeaponWithInvalidModification(): void
    {
        self::assertNotNull(Weapon::$weapons);
        self::assertArrayHasKey('ares-predator-v', Weapon::$weapons);

        // Break the data file
        $originalMods = Weapon::$weapons['ares-predator-v']['modifications'];
        Weapon::$weapons['ares-predator-v']['modifications'] = ['invalid'];

        $weapon = new Weapon('ares-predator-v');
        self::assertEmpty($weapon->modifications);
        Weapon::$weapons['ares-predator-v']['modifications'] = $originalMods;
    }

    #[Test]
    #[TestDox('Weapons with damage that is not based on the wielder\'s strength return the correct damage')]
    public function testGetDamageNotStrengthBased(): void
    {
        self::assertSame('10P', $this->weapon->getDamage(5));
        self::assertSame('10P', $this->weapon->getDamage(0));
    }

    #[Test]
    #[TestDox('Weapon with damage based on strength with a positive modifier returns correct damage value')]
    public function testGetDamageStrengthBased(): void
    {
        $weapon = new Weapon('combat-knife');
        self::assertSame('4P', $weapon->getDamage(2));
        self::assertSame('6P', $weapon->getDamage(4));
    }

    #[Test]
    #[TestDox('Weapon with damage based on strength with a negative modifier returns correct damage value')]
    public function testGetDamageStrengthBasedNegativeModifier(): void
    {
        $weapon = new Weapon('proboscis');
        self::assertSame('1P', $weapon->getDamage(2));
        self::assertSame('5P', $weapon->getDamage(6));
    }

    #[Test]
    #[TestDox('Weapon with damage based on strength returns correct damage value')]
    public function testGetDamageUnarmedStrike(): void
    {
        $strike = new Weapon('unarmed-strike');
        self::assertSame('2S', $strike->getDamage(2));
        self::assertSame('5S', $strike->getDamage(5));
    }

    #[Test]
    #[TestDox('Weapon::buildWeapon() throws an exception if a weapon is not found')]
    public function testBuildWeaponNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon ID "not-found" is invalid');
        Weapon::buildWeapon(['id' => 'not-found']);
    }

    #[Test]
    #[TestDox('Weapon::buildWeapon() works with nothing extra added')]
    public function testBuildWeaponNoMods(): void
    {
        $weapon = Weapon::buildWeapon([
            'id' => 'ak-98',
            'link' => 'test-link',
            'loaded' => 'deadb33f',
        ]);
        self::assertSame('AK-98', $weapon->name);
        self::assertSame('test-link', $weapon->link);
        self::assertSame('deadb33f', $weapon->loaded);
        self::assertEmpty($weapon->ammunition);
    }

    #[Test]
    #[TestDox('Weapon::buildWeapon() works with built-in modifications')]
    public function testBuildWeaponBuiltin(): void
    {
        $weapon = Weapon::buildWeapon(['id' => 'ares-predator-v']);
        self::assertNotEmpty($weapon->modifications);
        self::assertInstanceOf(
            WeaponModification::class,
            $weapon->modifications[0]
        );
        self::assertNull($weapon->accessories['barrel']);
        self::assertNull($weapon->accessories['top']);
    }

    #[Test]
    #[TestDox('Weapon::buildWeapon() works with modifications and accessories')]
    public function testBuildWeaponAddons(): void
    {
        $array = [
            'id' => 'ak-98',
            'modifications' => ['smartlink-internal'],
            'accessories' => [
                'barrel' => 'bayonet',
            ],
        ];
        $weapon = Weapon::buildWeapon($array);
        self::assertNotNull($weapon->modifications[0]);
        self::assertEquals(
            'Internal Smartlink',
            $weapon->modifications[0]->name
        );
        self::assertNotNull($weapon->accessories['barrel']);
        self::assertEquals('Bayonet', $weapon->accessories['barrel']->name);
    }

    #[Test]
    #[TestDox('Weapon::buildWeapon() stores ammunition')]
    public function testBuildWeaponAmmo(): void
    {
        $array = [
            'id' => 'ak-98',
            'ammo' => [
                [
                    'id' => 'apds',
                    'container' => 'ak-98',
                    'tracer' => false,
                    'quantity' => 38,
                    'class' => 'Assault Rifle',
                ],
            ],
        ];
        $weapon = Weapon::buildWeapon($array);
        self::assertNotEmpty($weapon->ammunition);
    }

    #[Test]
    #[TestDox('Weapon::findByName() throws an exception for items that do not exist')]
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon name "Not Found" was not found');
        Weapon::findByName('Not Found');
    }

    #[Test]
    #[TestDox('Weapon::findByName() can find items by name')]
    public function testFindByName(): void
    {
        self::assertSame('ak-98', Weapon::findByName('AK-98')->id);
        self::assertSame(
            'defiance-t-250-short',
            Weapon::findByName('Defiance T-250 (Short Barrel)')->id
        );
    }

    #[Test]
    #[TestDox('getCost() returns correct cost for an unmodified weapon')]
    public function testGetCost(): void
    {
        $weapon = new Weapon('ak-98');
        self::assertSame(1250, $weapon->getCost());
    }

    #[Test]
    #[TestDox('Weapons with modifications return the correct cost')]
    public function testGetCostModified(): void
    {
        $weapon = new Weapon('ak-98');
        $weapon->modifications[] = new WeaponModification('smartlink-internal');
        self::assertSame(2500, $weapon->getCost());
    }

    #[Test]
    #[TestDox('Weapons with accessories return their correct cost')]
    public function testGetCostAccessories(): void
    {
        $weapon = new Weapon('ak-98');
        $weapon->accessories['top'] = new WeaponModification('bayonet');
        self::assertSame(1300, $weapon->getCost());
        $weapon->accessories['under'] = new WeaponModification('bayonet');
        self::assertSame(1350, $weapon->getCost());
    }

    #[Test]
    #[TestDox('Weapon::all() returns all weapons in the data file')]
    public function testAll(): void
    {
        self::assertCount(7, Weapon::all());
    }
}
