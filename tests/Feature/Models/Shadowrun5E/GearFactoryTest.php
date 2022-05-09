<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Commlink;
use App\Models\Shadowrun5E\Gear;
use App\Models\Shadowrun5E\GearFactory;

/**
 * Unit tests for the gear factory.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class GearFactoryTest extends \Tests\TestCase
{
    /**
     * Test trying to get a string ID that isn't found.
     * @test
     */
    public function testGetInvalidString(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Item ID "invalid" is invalid');
        GearFactory::get('invalid');
    }

    /**
     * Test trying to get an invalid ID from an array.
     * @test
     */
    public function testGetInvalidArrayId(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Item ID "invalid" is invalid');
        GearFactory::get(['id' => 'invalid']);
    }

    /**
     * Test trying to get a valid item.
     * @test
     */
    public function testGetValidString(): void
    {
        $item = GearFactory::get('credstick-gold');
        self::assertInstanceOf(Gear::class, $item);
    }

    /**
     * Test that quantity defaults to 1 for string items.
     * @test
     */
    public function testGetValidStringSetsQuantity(): void
    {
        $item = GearFactory::get('credstick-gold');
        self::assertSame(1, $item->quantity);
    }

    /**
     * Test trying to get a valid item from an array.
     * @test
     */
    public function testGetValidArrayId(): void
    {
        $item = GearFactory::get(['id' => 'credstick-gold']);
        self::assertInstanceOf(Gear::class, $item);
    }

    /**
     * Test that quantity defaults to 1 for array items.
     * @test
     */
    public function testGetValidArraySetsQuantity(): void
    {
        $item = GearFactory::get(['id' => 'credstick-gold']);
        self::assertSame(1, $item->quantity);
    }

    /**
     * Test that quantity sets the quantity if given.
     * @test
     */
    public function testGetValidArraySetsQuantityIfGiven(): void
    {
        $item = GearFactory::get(['id' => 'credstick-gold', 'quantity' => 9]);
        self::assertSame(9, $item->quantity);
    }

    /**
     * Test loading a cyberdeck.
     * @test
     */
    public function testLoadingCyberdeck(): void
    {
        $item = GearFactory::get(['id' => 'cyberdeck-evo-sublime']);
        self::assertInstanceOf(Commlink::class, $item);
    }

    /**
     * Test that loading a cyberdeck sets the broadcasting SIN.
     * @test
     */
    public function testLoadingCyberdeckSetsSin(): void
    {
        $deck = ['id' => 'cyberdeck-evo-sublime', 'sin' => 2];
        /** @var Commlink $item */
        $item = GearFactory::get($deck);
        self::assertInstanceOf(Commlink::class, $item);
        self::assertSame(2, $item->sin);
    }
}
