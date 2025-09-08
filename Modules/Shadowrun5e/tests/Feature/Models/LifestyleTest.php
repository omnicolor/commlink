<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Lifestyle;
use Modules\Shadowrun5e\Models\LifestyleOption;
use Modules\Shadowrun5e\Models\LifestyleZone;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class LifestyleTest extends TestCase
{
    /**
     * Test trying to load an invalid lifestyle.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Lifestyle ID "invalid" is invalid');
        new Lifestyle('invalid');
    }

    /**
     * Test loading a low lifestyle.
     */
    public function testLoadLowLifestyle(): void
    {
        $lifestyle = new Lifestyle('low');
        self::assertSame('Low', $lifestyle->name);
        self::assertSame('low', $lifestyle->id);
        self::assertSame(2000, $lifestyle->cost);
        self::assertSame(373, $lifestyle->page);
        self::assertSame(3, $lifestyle->points);
        self::assertSame('core', $lifestyle->ruleset);
        $attributes = $lifestyle->attributes;
        self::assertSame(2, $attributes->comforts);
        self::assertSame(3, $attributes->comfortsMax);
        self::assertSame(2, $attributes->neighborhood);
        self::assertSame(3, $attributes->neighborhoodMax);
        self::assertSame(2, $attributes->security);
        self::assertSame(3, $attributes->securityMax);
        $zone = $lifestyle->getZone();
        self::assertSame('D', $zone->name);
    }

    /**
     * Test the toString method.
     */
    public function testToString(): void
    {
        $lifestyle = new Lifestyle('low');
        self::assertSame('Low', (string)$lifestyle);
    }

    /**
     * Test loading a luxury lifestyle.
     */
    public function testLoadLuxuryLifestyle(): void
    {
        $lifestyle = new Lifestyle('luxury');
        self::assertSame('Luxury', $lifestyle->name);
        self::assertSame('luxury', $lifestyle->id);
        self::assertSame(100000, $lifestyle->cost);
        self::assertSame(373, $lifestyle->page);
        self::assertSame(12, $lifestyle->points);
        self::assertSame('core', $lifestyle->ruleset);
        $attributes = $lifestyle->attributes;
        self::assertSame(5, $attributes->comforts);
        self::assertSame(7, $attributes->comfortsMax);
        self::assertSame(5, $attributes->neighborhood);
        self::assertSame(7, $attributes->neighborhoodMax);
        self::assertSame(5, $attributes->security);
        self::assertSame(8, $attributes->securityMax);
        $zone = $lifestyle->getZone();
        self::assertSame('A', $zone->name);
    }

    /**
     * Test the getCost() method.
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
     */
    public function testGetZoneWithValidNeighborhoods(): void
    {
        $lifestyle = new Lifestyle('low');
        $lifestyle->attributes->neighborhood = 0;
        self::assertEquals(new LifestyleZone('z'), $lifestyle->getZone());
        ++$lifestyle->attributes->neighborhood;
        self::assertEquals(new LifestyleZone('e'), $lifestyle->getZone());
        ++$lifestyle->attributes->neighborhood;
        self::assertEquals(new LifestyleZone('d'), $lifestyle->getZone());
        ++$lifestyle->attributes->neighborhood;
        self::assertEquals(new LifestyleZone('c'), $lifestyle->getZone());
        ++$lifestyle->attributes->neighborhood;
        self::assertEquals(new LifestyleZone('b'), $lifestyle->getZone());
        ++$lifestyle->attributes->neighborhood;
        self::assertEquals(new LifestyleZone('a'), $lifestyle->getZone());
        ++$lifestyle->attributes->neighborhood;
        self::assertEquals(new LifestyleZone('aa'), $lifestyle->getZone());
        ++$lifestyle->attributes->neighborhood;
        self::assertEquals(new LifestyleZone('aaa'), $lifestyle->getZone());
        ++$lifestyle->attributes->neighborhood;
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Neighborhood rating out of range');
        $lifestyle->getZone();
    }

    /**
     * Test getting the cost for a lifestyle with some multiplying options.
     *
     * Low lifestyle costs 2000.
     */
    public function testGetCostWithMultiplier(): void
    {
        $lifestyle = new Lifestyle('low');
        $hotTub = new LifestyleOption('increase-neighborhood');
        $lifestyle->options[] = $hotTub;
        self::assertSame(2200, $lifestyle->getCost());

        $pool = new LifestyleOption('swimming-pool');
        $lifestyle->options[] = $pool;
        self::assertSame(2300, $lifestyle->getCost());
    }

    /**
     * Test increasing the Lifestyle's neighborhood.
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

    public function testAll(): void
    {
        self::assertGreaterThan(1, Lifestyle::all());
    }
}
