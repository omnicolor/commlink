<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Armor;
use App\Models\Shadowrun5e\ArmorModification;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for armor modifications class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ArmorModificationTest extends TestCase
{
    /**
     * Test loading an invalid modification.
     * @test
     */
    public function testLoadNotFoundModification(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Modification ID "invalid" not found');
        new ArmorModification('invalid');
    }

    /**
     * Test loading a valid modification sets the ID.
     * @return ArmorModification
     * @test
     */
    public function testLoadArmorModificationSetsId(): ArmorModification
    {
        ArmorModification::$modifications = null;
        $mod = new ArmorModification('auto-injector');
        self::assertEquals('auto-injector', $mod->id);
        return $mod;
    }

    /**
     * Test loading a valid modification sets the cost.
     * @depends testLoadArmorModificationSetsId
     * @param ArmorModification $mod
     * @test
     */
    public function testLoadArmorModificationSetsCost(
        ArmorModification $mod
    ): void {
        self::assertEquals(1500, $mod->cost);
    }

    /**
     * Test loading a valid modification sets the description.
     * @depends testLoadArmorModificationSetsId
     * @param ArmorModification $mod
     * @test
     */
    public function testLoadArmorModificationSetsDescription(
        ArmorModification $mod
    ): void {
        self::assertNotNull($mod->description);
    }

    /**
     * Test loading a valid modification sets the effects.
     * @depends testLoadArmorModificationSetsId
     * @param ArmorModification $mod
     * @test
     */
    public function testLoadArmorModificationSetsEffects(
        ArmorModification $mod
    ): void {
        self::assertEmpty($mod->effects);
    }

    /**
     * Test loading a valid modification sets the ruleset.
     * @depends testLoadArmorModificationSetsId
     * @param ArmorModification $mod
     * @test
     */
    public function testLoadArmorModificationSetsRuleset(
        ArmorModification $mod
    ): void {
        self::assertEquals('run-and-gun', $mod->ruleset);
    }

    /**
     * Test loading a valid modification doesn't set the rating if there isn't
     * one.
     * @depends testLoadArmorModificationSetsId
     * @param ArmorModification $mod
     * @test
     */
    public function testLoadArmorModificationDoesntSetRating(
        ArmorModification $mod
    ): void {
        self::assertNull($mod->rating);
    }

    /**
     * Test loading a valid modification sets the name.
     * @depends testLoadArmorModificationSetsId
     * @param ArmorModification $mod
     * @test
     */
    public function testLoadArmorModificationSetsName(
        ArmorModification $mod
    ): void {
        self::assertEquals('Auto-injector', $mod->name);
    }

    /**
     * Test the toString method.
     * @depends testLoadArmorModificationSetsId
     * @param ArmorModification $mod
     * @test
     */
    public function testToString(ArmorModification $mod): void
    {
        self::assertEquals('Auto-injector', (string)$mod);
    }

    /**
     * Test that loading a modification with a rating sets the rating property.
     * @test
     */
    public function testLoadArmorModSetRating(): void
    {
        $mod = new ArmorModification('fire-resistance-2');
        self::assertEquals(2, $mod->rating);
    }

    /**
     * Test an armor modification that has a cost multiplier.
     * @test
     */
    public function testArmorModWithCostMultiplier(): void
    {
        $mod = new ArmorModification('ynt-softweave-armor');
        self::assertEquals(0, $mod->cost);
        self::assertEquals(2, $mod->costModifier);
    }

    /**
     * Test getCost() on an armor mod with a flat cost.
     * @test
     */
    public function testGetCostSimple(): void
    {
        $mod = new ArmorModification('fire-resistance-2');
        self::assertSame(500, $mod->getCost(new Armor('armor-jacket')));
    }

    /**
     * Test getCost() on a mod that changes its cost depending on the armor it's
     * applied to.
     * @test
     */
    public function testGetCostCostModifier(): void
    {
        $mod = new ArmorModification('ynt-softweave-armor');
        self::assertSame(1000, $mod->getCost(new Armor('armor-jacket')));
    }

    /**
     * Test findByName with a name that isn't found.
     * @test
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Armor modification "Not Found" was not found'
        );
        ArmorModification::findByName('Not Found');
    }

    /**
     * Test findByName returning a weapon modification.
     * @test
     */
    public function testFindByName(): void
    {
        $mod = ArmorModification::findByName('Fire Resistance', 2);
        self::assertSame('fire-resistance-2', $mod->id);
    }
}
