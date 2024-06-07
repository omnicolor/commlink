<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Commlink;
use App\Models\Shadowrun5e\Gear;
use App\Models\Shadowrun5e\GearFactory;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for the gear factory.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class GearFactoryTest extends TestCase
{
    /**
     * Test trying to get a string ID that isn't found.
     */
    public function testGetInvalidString(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Item ID "invalid" is invalid');
        GearFactory::get('invalid');
    }

    /**
     * Test trying to get an invalid ID from an array.
     */
    public function testGetInvalidArrayId(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Item ID "invalid" is invalid');
        GearFactory::get(['id' => 'invalid']);
    }

    /**
     * Test trying to get a valid item.
     */
    public function testGetValidString(): void
    {
        $item = GearFactory::get('credstick-gold');
        self::assertInstanceOf(Gear::class, $item);
    }

    /**
     * Test that quantity defaults to 1 for string items.
     */
    public function testGetValidStringSetsQuantity(): void
    {
        $item = GearFactory::get('credstick-gold');
        self::assertSame(1, $item->quantity);
    }

    /**
     * Test trying to get a valid item from an array.
     */
    public function testGetValidArrayId(): void
    {
        $item = GearFactory::get(['id' => 'credstick-gold']);
        self::assertInstanceOf(Gear::class, $item);
    }

    /**
     * Test that quantity defaults to 1 for array items.
     */
    public function testGetValidArraySetsQuantity(): void
    {
        $item = GearFactory::get(['id' => 'credstick-gold']);
        self::assertSame(1, $item->quantity);
    }

    /**
     * Test that quantity sets the quantity if given.
     */
    public function testGetValidArraySetsQuantityIfGiven(): void
    {
        $item = GearFactory::get(['id' => 'credstick-gold', 'quantity' => 9]);
        self::assertSame(9, $item->quantity);
    }

    /**
     * Test loading a cyberdeck.
     */
    public function testLoadingCyberdeck(): void
    {
        $item = GearFactory::get(['id' => 'cyberdeck-evo-sublime']);
        self::assertInstanceOf(Commlink::class, $item);
    }

    /**
     * Test that loading a cyberdeck sets the broadcasting SIN.
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
