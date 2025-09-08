<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Enums\WeaponRange;
use Modules\Shadowrun5e\Models\Weapon;
use Modules\Shadowrun5e\Models\WeaponModification;
use Override;
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
    #[Override]
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
        self::assertSame('run-and-gun', $this->weapon->ruleset);
    }

    /**
     * Test that loading a weapon doesn't change the ruleset if it is from core.
     */
    public function testWeaponDoesntSetRuleset(): void
    {
        $weapon = new Weapon('ares-predator-v');
        self::assertSame('core', $weapon->ruleset);
    }

    public function testWeaponWithTextRange(): void
    {
        $weapon = new Weapon('defiance-t-250-short');
        self::assertSame(WeaponRange::Shotgun, $weapon->range);
    }

    public function testWeaponWithoutRange(): void
    {
        $weapon = new Weapon('ares-predator-v');
        self::assertSame(WeaponRange::HeavyPistol, $weapon->range);
    }

    public function testCastWeaponToString(): void
    {
        self::assertSame('AK-98', (string)$this->weapon);
    }

    /**
     * Every character can use an unarmed strike.
     */
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
        self::assertNotNull(Weapon::$weapons);
        self::assertArrayHasKey('ares-predator-v', Weapon::$weapons);

        // Break the data file
        $originalMods = Weapon::$weapons['ares-predator-v']['modifications'];
        Weapon::$weapons['ares-predator-v']['modifications'] = ['invalid'];

        $weapon = new Weapon('ares-predator-v');
        self::assertEmpty($weapon->modifications);
        Weapon::$weapons['ares-predator-v']['modifications'] = $originalMods;
    }

    /**
     * Test getting the damage for a weapon that doesn't take strength into
     * account.
     */
    public function testGetDamageNotStrengthBased(): void
    {
        self::assertSame('10P', $this->weapon->getDamage(5));
        self::assertSame('10P', $this->weapon->getDamage(0));
    }

    /**
     * Test getting the damage for a weapon based on strength.
     */
    public function testGetDamageStrengthBased(): void
    {
        $weapon = new Weapon('combat-knife');
        self::assertSame('4P', $weapon->getDamage(2));
        self::assertSame('6P', $weapon->getDamage(4));
    }

    /**
     * Test getting damage for an unarmed strike.
     */
    public function testGetDamageUnarmedStrike(): void
    {
        $strike = new Weapon('unarmed-strike');
        self::assertSame('2S', $strike->getDamage(2));
        self::assertSame('5S', $strike->getDamage(5));
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
        self::assertSame('AK-98', $weapon->name);
        self::assertSame('test-link', $weapon->link);
        self::assertSame('deadb33f', $weapon->loaded);
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
        self::assertNotNull($weapon->modifications[0]);
        self::assertEquals(
            'Internal Smartlink',
            $weapon->modifications[0]->name
        );
        self::assertNotNull($weapon->accessories['barrel']);
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
        self::assertSame('ak-98', Weapon::findByName('AK-98')->id);
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

    public function testAll(): void
    {
        self::assertCount(6, Weapon::all());
    }
}
