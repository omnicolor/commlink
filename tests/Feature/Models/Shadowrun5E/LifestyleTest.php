<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Lifestyle;
use App\Models\Shadowrun5E\LifestyleAttributes;
use App\Models\Shadowrun5E\LifestyleOption;
use App\Models\Shadowrun5E\LifestyleZone;

/**
 * Tests for Shadowrun 5E lifestyles.
 * @covers \App\Models\Shadowrun5E\Lifestyle
 * @covers \App\Models\Shadowrun5E\LifestyleAttributes
 * @covers \App\Models\Shadowrun5E\LifestyleZone
 * @group current
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class LifestyleTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid lifestyle.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Lifestyle ID "invalid" is invalid');
        new Lifestyle('invalid');
    }

    /**
     * Test loading a low lifestyle.
     * @test
     */
    public function testLoadLowLifestyle(): void
    {
        $lifestyle = new Lifestyle('low');
        self::assertSame('Low', $lifestyle->name);
        self::assertSame('low', $lifestyle->id);
        self::assertSame(2000, $lifestyle->cost);
        self::assertNotNull($lifestyle->description);
        self::assertSame(373, $lifestyle->page);
        self::assertSame(3, $lifestyle->points);
        self::assertSame('core', $lifestyle->ruleset);
        $attributes = $lifestyle->attributes;
        self::assertInstanceOf(LifestyleAttributes::class, $attributes);
        self::assertSame(2, $attributes->comforts);
        self::assertSame(3, $attributes->comfortsMax);
        self::assertSame(2, $attributes->neighborhood);
        self::assertSame(3, $attributes->neighborhoodMax);
        self::assertSame(2, $attributes->security);
        self::assertSame(3, $attributes->securityMax);
        $zone = $lifestyle->getZone();
        self::assertInstanceOf(LifestyleZone::class, $zone);
        self::assertSame('D', $zone->name);
    }

    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $lifestyle = new Lifestyle('low');
        self::assertSame('Low', (string)$lifestyle);
    }

    /**
     * Test loading a luxury lifestyle.
     * @test
     */
    public function testLoadLuxuryLifestyle(): void
    {
        $lifestyle = new Lifestyle('luxury');
        self::assertSame('Luxury', $lifestyle->name);
        self::assertSame('luxury', $lifestyle->id);
        self::assertSame(100000, $lifestyle->cost);
        self::assertNotNull($lifestyle->description);
        self::assertSame(373, $lifestyle->page);
        self::assertSame(12, $lifestyle->points);
        self::assertSame('core', $lifestyle->ruleset);
        $attributes = $lifestyle->attributes;
        self::assertInstanceOf(LifestyleAttributes::class, $attributes);
        self::assertSame(5, $attributes->comforts);
        self::assertSame(7, $attributes->comfortsMax);
        self::assertSame(5, $attributes->neighborhood);
        self::assertSame(7, $attributes->neighborhoodMax);
        self::assertSame(5, $attributes->security);
        self::assertSame(8, $attributes->securityMax);
        $zone = $lifestyle->getZone();
        self::assertInstanceOf(LifestyleZone::class, $zone);
        self::assertSame('A', $zone->name);
    }

    /**
     * Test the getCost() method.
     * @test
     */
    public function testGetCost(): void
    {
        $lifestyle = new Lifestyle('low');
        self::assertSame(2000, $lifestyle->getCost());
        $lifestyle = new Lifestyle('luxury');
        self::assertSame(100000, $lifestyle->getCost());
    }

    /**
     * Test the getZone method with different neighborhood values.
     * @test
     */
    public function testGetZoneWithValidNeighborhoods(): void
    {
        $lifestyle = new Lifestyle('low');
        $lifestyle->attributes->neighborhood = 0;
        self::assertEquals(new LifestyleZone('z'), $lifestyle->getZone());
        $lifestyle->attributes->neighborhood++;
        self::assertEquals(new LifestyleZone('e'), $lifestyle->getZone());
        $lifestyle->attributes->neighborhood++;
        self::assertEquals(new LifestyleZone('d'), $lifestyle->getZone());
        $lifestyle->attributes->neighborhood++;
        self::assertEquals(new LifestyleZone('c'), $lifestyle->getZone());
        $lifestyle->attributes->neighborhood++;
        self::assertEquals(new LifestyleZone('b'), $lifestyle->getZone());
        $lifestyle->attributes->neighborhood++;
        self::assertEquals(new LifestyleZone('a'), $lifestyle->getZone());
        $lifestyle->attributes->neighborhood++;
        self::assertEquals(new LifestyleZone('aa'), $lifestyle->getZone());
        $lifestyle->attributes->neighborhood++;
        self::assertEquals(new LifestyleZone('aaa'), $lifestyle->getZone());
        $lifestyle->attributes->neighborhood++;
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Neighborhood rating out of range');
        $lifestyle->getZone();
    }

    /**
     * Test getting the cost for a lifestyle with some multiplying options.
     *
     * Low lifestyle costs 2000.
     * @test
     */
    public function testGetCostWithMultiplier(): void
    {
        $lifestyle = new Lifestyle('low');
        $hotTub = new LifestyleOption('swimming-pool');
        unset($hotTub->cost);
        $hotTub->costMultiplier = 0.1;
        $lifestyle->options[] = $hotTub;
        self::assertEquals(2200, $lifestyle->getCost());

        $pool = new LifestyleOption('swimming-pool');
        unset($pool->cost);
        $pool->costMultiplier = -0.2;
        $lifestyle->options[] = $pool;
        self::assertEquals(1800, $lifestyle->getCost());
    }

    /**
     * Test increasing the Lifestyle's neighborhood.
     * @test
     */
    public function testGetNeighborhood(): void
    {
        $lifestyle = new Lifestyle('low');
        self::assertSame(2, $lifestyle->getNeighborhood());

        $lifestyle->options[] = new LifestyleOption('swimming-pool');
        self::assertSame(2, $lifestyle->getNeighborhood());

        $lifestyle->options[] = new LifestyleOption('increase-neighborhood');
        self::assertSame(3, $lifestyle->getNeighborhood());
    }
}
