<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Armor;
use Modules\Shadowrun5e\Models\ArmorModification;
use Modules\Shadowrun5e\Models\GearModification;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ArmorTest extends TestCase
{
    /**
     * Test loading an armor with an invalid ID.
     */
    public function testLoadingArmorWithInvalidId(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Armor ID "not-found" is invalid');
        new Armor('not-found');
    }

    /**
     * Test loading an armor with a valid ID.
     * @return Armor
     */
    public function testLoadingArmorJacketId(): Armor
    {
        $armor = new Armor('armor-jacket');
        self::assertEquals('armor-jacket', $armor->id);
        return $armor;
    }

    /**
     * Test loading an armor sets the availability.
     */
    #[Depends('testLoadingArmorJacketId')]
    public function testLoadingArmorJacketAvailability(Armor $armor): void
    {
        self::assertEquals('2', $armor->availability);
    }

    /**
     * Test that loading an armor sets the cost.
     */
    #[Depends('testLoadingArmorJacketId')]
    public function testLoadingArmorJacketCost(Armor $armor): void
    {
        self::assertEquals(1000, $armor->cost);
    }

    /**
     * Test that loading an armor sets the name.
     */
    #[Depends('testLoadingArmorJacketId')]
    public function testLoadingArmorJacketName(Armor $armor): void
    {
        self::assertEquals('Armor Jacket', $armor->name);
    }

    /**
     * Test that loading an armor sets the armor rating.
     */
    #[Depends('testLoadingArmorJacketId')]
    public function testLoadingArmorJacketRating(Armor $armor): void
    {
        self::assertEquals(12, $armor->rating);
    }

    /**
     * Test that loading an armor sets the ruleset if none is given.
     */
    #[Depends('testLoadingArmorJacketId')]
    public function testLoadingArmorJacketRulesetDefault(Armor $armor): void
    {
        self::assertEquals('core', $armor->ruleset);
    }

    /**
     * Test that loading an armor from a different book sets the ruleset.
     */
    public function testLoadingArmorDifferentBookDifferentRuleset(): void
    {
        $armor = new Armor('berwick-suit');
        self::assertEquals('run-and-gun', $armor->ruleset);
    }

    /**
     * Test that an armor item's __toString method returns its name.
     */
    #[Depends('testLoadingArmorJacketId')]
    public function testLoadingArmorJacketToString(Armor $armor): void
    {
        self::assertEquals('Armor Jacket', (string)$armor);
    }

    /**
     * Test that loading an armor with additional effects loads them.
     */
    public function testLoadingArmorLoadsEffects(): void
    {
        $armor = new Armor('berwick-suit');
        $expected = [
            'concealability' => -2,
            'social-limit' => 1,
        ];
        self::assertEquals($expected, $armor->effects);
    }

    /**
     * Test getModifiedRating on an unmodified piece of armor.
     */
    #[Depends('testLoadingArmorJacketId')]
    public function testGetModifiedRatingUnmodified(Armor $armor): void
    {
        self::assertEquals(12, $armor->getModifiedRating());
    }

    /**
     * Test getModifiedRating on a modified piece of armor, where the
     * modification changes the rating.
     */
    public function testGetModifiedRatingModified(): void
    {
        $armor = new Armor('armor-jacket');
        $armor->modifications[] = new ArmorModification('gel-packs');
        self::assertEquals(14, $armor->getModifiedRating());
    }

    /**
     * Test getModifiedRating on a modified piece of armor, where the
     * modifications have no game effects.
     */
    public function testGetModifiedRatingModifiedWithNoEffects(): void
    {
        $armor = new Armor('armor-jacket');
        $armor->modifications[] = new ArmorModification('auto-injector');
        self::assertEquals(12, $armor->getModifiedRating());
    }

    /**
     * Test getCost() with no modifications.
     */
    public function testGetCostNoModifications(): void
    {
        $armor = new Armor('armor-jacket');
        self::assertSame(1000, $armor->getCost());
    }

    /**
     * Test getCost() with a modification that has a flat cost.
     */
    public function testGetCostFlatModification(): void
    {
        $armor = new Armor('armor-jacket');
        $armor->modifications[] = new ArmorModification('auto-injector');
        self::assertSame(2500, $armor->getCost());
    }

    /**
     * Test getCost() with a modification that multiplies the armor's cost.
     */
    public function testGetCostMultiplicativeModification(): void
    {
        $armor = new Armor('armor-jacket');
        $armor->modifications[] = new ArmorModification('ynt-softweave-armor');
        self::assertSame(2000, $armor->getCost());
    }

    /**
     * Test build() with an ID that doesn't exist.
     */
    public function testBuildArmorNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Armor ID "invalid-id" is invalid');
        Armor::build(['id' => 'invalid-id']);
    }

    /**
     * Test build() with valid armor, setting active property, no mods.
     */
    public function testBuildArmorActiveNoModifications(): void
    {
        $array = [
            'id' => 'armor-jacket',
            'active' => true,
        ];
        $armor = Armor::build($array);
        self::assertSame('Armor Jacket', $armor->name);
        self::assertTrue($armor->active);
        self::assertEmpty($armor->modifications);
    }

    /**
     * Test build() with some modded armor (gear and armor mods), not active.
     */
    public function testBuildArmorWithMods(): void
    {
        $array = [
            'id' => 'berwick-suit',
            'modifications' => [
                'auto-injector',
                'attack-dongle-2',
            ],
        ];
        $armor = Armor::build($array);
        self::assertSame('Berwick Suit', $armor->name);
        self::assertFalse($armor->active);
        self::assertNotEmpty($armor->modifications);
        self::assertInstanceOf(
            ArmorModification::class,
            $armor->modifications[0]
        );
        self::assertInstanceOf(
            GearModification::class,
            $armor->modifications[1]
        );
    }

    /**
     * Test build() with an illegal mod.
     */
    public function testBuildArmorWithUnknownMods(): void
    {
        $array = [
            'id' => 'berwick-suit',
            'modifications' => [
                'unknown',
            ],
        ];
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Armor/Gear mod not found: unknown');
        Armor::build($array);
    }

    /**
     * Test findByName() with an item that isn't found.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Armor name "Not Found" was not found');
        Armor::findByName('Not Found');
    }

    /**
     * Test findByName() with an item that is found.
     */
    public function testFindByName(): void
    {
        $item = Armor::findByName('Armor Jacket');
        self::assertSame('armor-jacket', $item->id);
    }
}
