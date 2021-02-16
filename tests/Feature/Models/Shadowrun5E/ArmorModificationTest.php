<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Armor;
use App\Models\Shadowrun5E\ArmorModification;

/**
 * Tests for armor modifications class.
 * @covers \App\Models\Shadowrun5E\ArmorModification
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class ArmorModificationTest extends \Tests\TestCase
{
    /**
     * Test loading an invalid modification.
     * @test
     */
    public function testLoadNotFoundModification(): void
    {
        self::expectException(\RuntimeException::class);
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
        $expected = 'Modification description goes here.';
        self::assertEquals($expected, $mod->description);
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
}
