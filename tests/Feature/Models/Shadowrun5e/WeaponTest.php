<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Weapon;
use App\Models\Shadowrun5e\WeaponModification;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class WeaponTest extends TestCase
{
    private Weapon $weapon;

    /**
     * Set up subject under test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->weapon = new Weapon('ak-98');
    }

    /**
     * Test loading a weapon with an invalid ID.
     */
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
        self::assertEquals('ak-98', $this->weapon->id);
    }

    /**
     * Test that loading a weapon sets its availability.
     */
    public function testWeaponHasAvailability(): void
    {
        self::assertEquals('8F', $this->weapon->availability);
    }

    /**
     * Test that loading a weapon sets its cost.
     */
    public function testWeaponHasCost(): void
    {
        self::assertEquals(1250, $this->weapon->cost);
    }

    /**
     * Test that loading a weapon sets its description.
     */
    public function testWeaponHasDescription(): void
    {
        self::assertNotNull($this->weapon->description);
    }

    /**
     * Test that loading a weapon sets its damage.
     */
    public function testWeaponHasDamage(): void
    {
        self::assertEquals('10P', $this->weapon->damage);
    }

    /**
     * Test that loading a weapon sets its name.
     */
    public function testWeaponHasName(): void
    {
        self::assertEquals('AK-98', $this->weapon->name);
    }

    /**
     * Test that loading a weapon that doesn't have a subname leaves it alone.
     */
    public function testWeaponDoesntHaveSubname(): void
    {
        self::assertNull($this->weapon->subname);
    }

    /**
     * Test that loading a weapon sets its ruleset when it has one.
     */
    public function testWeaponSetsRuleset(): void
    {
        self::assertEquals('run-and-gun', $this->weapon->ruleset);
    }

    /**
     * Test that loading a weapon doesn't change the ruleset if it is from core.
     */
    public function testWeaponDoesntSetRuleset(): void
    {
        $weapon = new Weapon('ares-predator-v');
        self::assertEquals('core', $weapon->ruleset);
    }

    /**
     * Test that a weapon can be cast to a string.
     */
    public function testCastWeaponToString(): void
    {
        self::assertEquals('AK-98', (string)$this->weapon);
    }

    /**
     * Every character can use an unarmed strike.
     */
    public function testUnarmedStrike(): void
    {
        $weapon = new Weapon('unarmed-strike');
        self::assertEquals('physical', $weapon->accuracy);
        self::assertEquals('(STR)', $weapon->damage);
        self::assertEquals(
            'Unarmed Combat covers the various '
                . 'self-defense and attack moves that employ the body as a '
                . 'primary weapon. This includes a wide array of martial arts '
                . 'along with the use of cybernetic implant weaponry and the '
                . 'fighting styles that sprung up around those implants.',
            $weapon->description
        );
        self::assertEquals('unarmed-strike', $weapon->id);
        self::assertEquals('Unarmed Strike', $weapon->name);
        self::assertEquals(0, $weapon->reach);
        self::assertEquals(132, $weapon->page);
        self::assertEquals('core', $weapon->ruleset);
        self::assertEquals('unarmed-combat', $weapon->skill);
    }

    /**
     * Test that loading a weapon with an integrated modification loads it.
     */
    public function testWeaponWithIntegratedModification(): void
    {
        $weapon = new Weapon('ares-predator-v');
        self::assertNotEmpty($weapon->modifications);
        self::assertInstanceOf(
            WeaponModification::class,
            $weapon->modifications[0]
        );
    }

    /**
     * Test loading a weapon with an invalid modification.
     */
    public function testLoadingWeaponWithInvalidModification(): void
    {
        // Break the data file
        // @phpstan-ignore-next-line
        $originalMods = Weapon::$weapons['ares-predator-v']['modifications'];
        Weapon::$weapons['ares-predator-v']['modifications'] = ['invalid'];
        $weapon = new Weapon('ares-predator-v');
        self::assertEmpty($weapon->modifications);
        Weapon::$weapons['ares-predator-v']['modifications'] = $originalMods;
    }

    /**
     * Data provider for weapons of each class along with its range.
     * @return array<int, array<int, string>>
     */
    public static function weaponRangeDataProvider(): array
    {
        return [
            ['Melee Weapon', '???'],
            ['Assault Cannon', '50/300/750/1200'],
            ['Assault Rifle', '25/150/350/550'],
            ['Bow', 'STR/STRx10/STRx30/STRx60'],
            ['Grenade', 'STRx2/STRx4/STRx6/STRx10'],
            ['Grenade Launcher', '5-50/100/150/500'],
            ['Heavy Crossbow', '14/45/120/180'],
            ['Heavy Machinegun', '40/250/750/1200'],
            ['Heavy Pistol', '5/20/40/60'],
            ['Hold-Out Pistol', '5/15/30/50'],
            ['Light Crossbow', '6/24/60/120'],
            ['Light Machinegun', '25/200/400/800'],
            ['Light Pistol', '5/15/30/50'],
            ['Machine Pistol', '5/15/30/50'],
            ['Medium Crossbow', '9/36/90/150'],
            ['Medium Machinegun', '40/250/750/1200'],
            ['Missile Launcher', '20-70*/150/450/1500'],
            ['Shotgun', '10/40/80/150'],
            ['Shotgun (flechette)', '15/30/45/60'],
            ['Sniper Rifle', '50/350/800/1500'],
            ['Submachine Gun', '10/40/80/150'],
            ['Taser', '5/10/15/20'],
            ['Throwing Weapon', 'STR/STRx2/STRx5/STRx7'],
            ['Thrown Knife', 'STR/STRx2/STRx3/STRx5'],
        ];
    }

    /**
     * Test getting the range for various classes of weapons.
     * @dataProvider weaponRangeDataProvider
     */
    public function testGetRange(string $class, string $range): void
    {
        $this->weapon->class = $class;
        self::assertEquals($range, $this->weapon->getRange());
    }

    /**
     * Test getting the damage for a weapon that doesn't take strength into
     * account.
     */
    public function testGetDamageNotStrengthBased(): void
    {
        self::assertEquals('10P', $this->weapon->getDamage(5));
        self::assertEquals('10P', $this->weapon->getDamage(0));
    }

    /**
     * Test getting the damage for a weapon based on strength.
     */
    public function testGetDamageStrengthBased(): void
    {
        $weapon = new Weapon('combat-knife');
        self::assertEquals('4P', $weapon->getDamage(2));
        self::assertEquals('6P', $weapon->getDamage(4));
    }

    /**
     * Test getting damage for an unarmed strike.
     */
    public function testGetDamageUnarmedStrike(): void
    {
        $strike = new Weapon('unarmed-strike');
        self::assertEquals('2S', $strike->getDamage(2));
        self::assertEquals('5S', $strike->getDamage(5));
    }

    /**
     * Test buildWeapon() with an invalid ID.
     */
    public function testBuildWeaponNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon ID "not-found" is invalid');
        Weapon::buildWeapon(['id' => 'not-found']);
    }

    /**
     * Test buildWeapon() with nothing extra added.
     */
    public function testBuildWeaponNoMods(): void
    {
        $weapon = Weapon::buildWeapon([
            'id' => 'ak-98',
            'link' => 'test-link',
            'loaded' => 'deadb33f',
        ]);
        self::assertEquals('AK-98', $weapon->name);
        self::assertEquals('test-link', $weapon->link);
        self::assertEquals('deadb33f', $weapon->loaded);
        self::assertEmpty($weapon->ammunition);
    }

    /**
     * Test buildWeapon() with a built-in modification.
     */
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

    /**
     * Test buildWeapon() with an after market mod and accessory.
     */
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
        self::assertEquals(
            'Internal Smartlink',
            // @phpstan-ignore-next-line
            $weapon->modifications[0]->name
        );
        // @phpstan-ignore-next-line
        self::assertEquals('Bayonet', $weapon->accessories['barrel']->name);
    }

    /**
     * Test buildWeapon() with some ammunition.
     */
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

    /**
     * Test findByName() with an item that isn't found.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon name "Not Found" was not found');
        Weapon::findByName('Not Found');
    }

    /**
     * Test findByName() with an item that is found.
     */
    public function testFindByName(): void
    {
        self::assertInstanceOf(Weapon::class, Weapon::findByName('AK-98'));
    }

    /**
     * Test getCost() on an unloaded and unmodified weapon.
     */
    public function testGetCost(): void
    {
        $weapon = new Weapon('ak-98');
        self::assertSame(1250, $weapon->getCost());
    }

    /**
     * Test getting the cost of a weapon with modifications.
     */
    public function testGetCostModified(): void
    {
        $weapon = new Weapon('ak-98');
        $weapon->modifications[] = new WeaponModification('smartlink-internal');
        self::assertSame(2500, $weapon->getCost());
    }

    /**
     * Test getting the cost of a weapon with accessories.
     */
    public function testGetCostAccessories(): void
    {
        $weapon = new Weapon('ak-98');
        $weapon->accessories['top'] = new WeaponModification('bayonet');
        self::assertSame(1300, $weapon->getCost());
        $weapon->accessories['under'] = new WeaponModification('bayonet');
        self::assertSame(1350, $weapon->getCost());
    }
}
