<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\Armor;
use App\Models\Cyberpunkred\CostCategory;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for Cyberpunk Red armor.
 * @group models
 * @group cyberpunkred
 * @small
 */
final class ArmorTest extends TestCase
{
    /**
     * Test loading an invalid armor.
     * @test
     */
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Armor ID "invalid" is invalid');
        new Armor('invalid');
    }

    /**
     * Test loading a valid armor.
     * @test
     */
    public function testLoadArmor(): void
    {
        $armor = new Armor('light-armorjack');
        self::assertSame('Light armorjack', (string)$armor);
        self::assertSame(CostCategory::Premium, $armor->cost_category);
        self::assertSame(
            'A combination of Kevlar® and plastic meshes inserted into the '
                . 'weave of the fabric.',
            $armor->description
        );
        self::assertSame('light-armorjack', $armor->id);
        self::assertSame(97, $armor->page);
        self::assertSame(0, $armor->penalty);
        self::assertSame('core', $armor->ruleset);
        self::assertSame(11, $armor->stopping_power);
        self::assertSame('Light armorjack', $armor->type);

        self::assertSame(100, $armor->getCost());
    }

    public function testLoadArmorWithTextCostCategory(): void
    {
        $armor = new Armor('kevlar');
        self::assertSame(CostCategory::Costly, $armor->cost_category);
    }

    public function testGetCost(): void
    {
        $armor = new Armor('bodyweight-suit');
        self::assertSame(1000, $armor->getCost());
    }

    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Armor "Not Found" was not found');
        Armor::findByName('Not Found');
    }

    public function testFindByName(): void
    {
        $armor = Armor::findByName('Light Armorjack');
        self::assertSame('Light armorjack', $armor->type);
    }
}
