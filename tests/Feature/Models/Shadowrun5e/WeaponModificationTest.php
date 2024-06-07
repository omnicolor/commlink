<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Weapon;
use App\Models\Shadowrun5e\WeaponModification;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for weapon modification class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class WeaponModificationTest extends TestCase
{
    /**
     * @var WeaponModification Subject under test
     */
    private WeaponModification $modification;

    /**
     * Set up the subject under test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->modification = new WeaponModification('bayonet');
    }

    /**
     * Test that loading an invalid modification throws an exception.
     */
    public function testInvalidId(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Modification ID "invalid-item" is invalid'
        );
        WeaponModification::$modifications = null;
        new WeaponModification('invalid-item');
    }

    /**
     * Test that loading a weapon modification sets the ID.
     */
    public function testConstructorSetsId(): void
    {
        self::assertEquals('bayonet', $this->modification->id);
    }

    /**
     * Test that loading sets the availability.
     */
    public function testConstructorSetsAvailability(): void
    {
        self::assertEquals('4R', $this->modification->availability);
    }

    /**
     * Test that loading sets the cost.
     */
    public function testConstructorSetsCost(): void
    {
        self::assertEquals(50, $this->modification->cost);
    }

    /**
     * Test that loading doesn't change the costModifier if there isn't one.
     */
    public function testConstructorDoesntChangeCostModifier(): void
    {
        self::assertNull($this->modification->costModifier);
    }

    /**
     * Test that loading sets the description.
     */
    public function testConstructorSetsDescription(): void
    {
        self::assertNotNull($this->modification->description);
    }

    /**
     * Test that loading doesn't change effects if there are none.
     */
    public function testConstructorDoesntChangeEffects(): void
    {
        self::assertEquals([], $this->modification->effects);
    }

    /**
     * Test that loading doesn't change incompatible list if there are none.
     */
    public function testConstructorDoesntChangeIncompatibilities(): void
    {
        self::assertEquals([], $this->modification->incompatibleWith);
    }

    /**
     * Test that loading sets the mount point list.
     */
    public function testConstructorSetsMountList(): void
    {
        self::assertEquals(['top', 'under'], $this->modification->mount);
    }

    /**
     * Test that loading sets the name.
     */
    public function testConstructorSetsName(): void
    {
        self::assertEquals('Bayonet', $this->modification->name);
    }

    /**
     * Test that loading sets the ruleset, if not core.
     */
    public function testConstructorSetsRuleset(): void
    {
        self::assertEquals('run-and-gun', $this->modification->ruleset);
    }

    /**
     * Test that loading sets the type.
     */
    public function testConstructorSetsType(): void
    {
        self::assertEquals('accessory', $this->modification->type);
    }

    /**
     * Test casting modification to a string.
     */
    public function testToString(): void
    {
        self::assertEquals('Bayonet', (string)$this->modification);
    }

    /**
     * Test that constructor sets effects list.
     */
    public function testConstructorSetsEffects(): void
    {
        $modification = new WeaponModification('smartlink-internal');
        self::assertNotEmpty($modification->effects);
    }

    /**
     * Test that the constructor sets the cost modifier.
     */
    public function testConstructorSetsCostModifier(): void
    {
        $modification = new WeaponModification('smartlink-internal');
        self::assertEquals(2, $modification->costModifier);
    }

    /**
     * Test that the constructor doesn't change the cost if mod multiplies the
     * cost.
     */
    public function testConstructorDoesntChangeCost(): void
    {
        $modification = new WeaponModification('smartlink-internal');
        self::assertNull($modification->cost);
    }

    /**
     * Test that the constructor doesn't change the mount list if there isn't
     * one.
     */
    public function testConstructorDoesntChangeMountList(): void
    {
        $modification = new WeaponModification('smartlink-internal');
        self::assertEquals([], $modification->mount);
    }

    /**
     * Test getCost() on modifications that don't multiply the weapon's cost.
     */
    public function testGetCost(): void
    {
        $weapon = new Weapon('ares-predator-v');
        $mod = new WeaponModification('bayonet');
        self::assertSame(50, $mod->getCost($weapon));
    }

    /**
     * Test getCost() on modifications that multiply the weapon's cost.
     */
    public function testGetCostMultiplier(): void
    {
        $smartlink = new WeaponModification('smartlink-internal');

        self::assertSame(
            725,
            $smartlink->getCost(new Weapon('ares-predator-v'))
        );
    }

    /**
     * Test findByName with a name that isn't found.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Weapon modification "Not Found" was not found'
        );
        WeaponModification::findByName('Not Found');
    }

    /**
     * Test finding a modification by name.
     */
    public function testFindByName(): void
    {
        $mod = WeaponModification::findByName('Bayonet');
        self::assertSame('run-and-gun', $mod->ruleset);
    }
}
