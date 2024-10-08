<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Lifestyle;
use Modules\Shadowrun5e\Models\LifestyleOption;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class LifestyleOptionTest extends TestCase
{
    /**
     * Test trying to load a LifestyleOption that isn't found.
     */
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Lifestyle Option ID "invalid" is invalid'
        );
        new LifestyleOption('invalid');
    }

    /**
     * Test __toString returns the name of the option.
     */
    public function testToString(): void
    {
        self::assertSame(
            'Swimming Pool',
            (string)(new LifestyleOption('swimming-pool'))
        );
    }

    /**
     * Test lifestyle coverages items that require commercial lifestyles.
     */
    public function testIsCoveredCommercial(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'Commercial';
        self::assertTrue($option->isCovered(new Lifestyle('commercial')));
        self::assertFalse($option->isCovered(new Lifestyle('high')));
        self::assertFalse($option->isCovered(new Lifestyle('low')));
        self::assertFalse($option->isCovered(new Lifestyle('luxury')));
        self::assertFalse($option->isCovered(new Lifestyle('middle')));
        self::assertFalse($option->isCovered(new Lifestyle('squatter')));
        self::assertFalse($option->isCovered(new Lifestyle('street')));
    }

    /**
     * Test an option covered by High or higher lifestyles.
     */
    public function testHighLuxuryCoveredOption(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'High';
        self::assertFalse($option->isCovered(new Lifestyle('commercial')));
        self::assertTrue($option->isCovered(new Lifestyle('high')));
        self::assertFalse($option->isCovered(new Lifestyle('low')));
        self::assertTrue($option->isCovered(new Lifestyle('luxury')));
        self::assertFalse($option->isCovered(new Lifestyle('middle')));
        self::assertFalse($option->isCovered(new Lifestyle('squatter')));
        self::assertFalse($option->isCovered(new Lifestyle('street')));
    }

    /**
     * Test an option that is never covered by a lifestyle.
     */
    public function testNeverCoveredOption(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'None';
        self::assertFalse($option->isCovered(new Lifestyle('commercial')));
        self::assertFalse($option->isCovered(new Lifestyle('high')));
        self::assertFalse($option->isCovered(new Lifestyle('low')));
        self::assertFalse($option->isCovered(new Lifestyle('luxury')));
        self::assertFalse($option->isCovered(new Lifestyle('middle')));
        self::assertFalse($option->isCovered(new Lifestyle('squatter')));
        self::assertFalse($option->isCovered(new Lifestyle('street')));
    }

    /**
     * Test an option that requires luxury lifestyle.
     */
    public function testOptionCoveredByLuxury(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'Luxury';
        self::assertFalse($option->isCovered(new Lifestyle('commercial')));
        self::assertFalse($option->isCovered(new Lifestyle('high')));
        self::assertFalse($option->isCovered(new Lifestyle('low')));
        self::assertTrue($option->isCovered(new Lifestyle('luxury')));
        self::assertFalse($option->isCovered(new Lifestyle('middle')));
        self::assertFalse($option->isCovered(new Lifestyle('squatter')));
        self::assertFalse($option->isCovered(new Lifestyle('street')));
    }

    /**
     * Test an option that requires middle lifestyle or higher.
     */
    public function testOptionCoveredByMiddle(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'Middle';
        self::assertFalse($option->isCovered(new Lifestyle('commercial')));
        self::assertTrue($option->isCovered(new Lifestyle('high')));
        self::assertFalse($option->isCovered(new Lifestyle('low')));
        self::assertTrue($option->isCovered(new Lifestyle('luxury')));
        self::assertTrue($option->isCovered(new Lifestyle('middle')));
        self::assertFalse($option->isCovered(new Lifestyle('squatter')));
        self::assertFalse($option->isCovered(new Lifestyle('street')));
    }

    /**
     * Test an option that requires low lifestyle or higher.
     */
    public function testOptionCoveredByLow(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'Low';
        self::assertFalse($option->isCovered(new Lifestyle('commercial')));
        self::assertTrue($option->isCovered(new Lifestyle('high')));
        self::assertTrue($option->isCovered(new Lifestyle('low')));
        self::assertTrue($option->isCovered(new Lifestyle('luxury')));
        self::assertTrue($option->isCovered(new Lifestyle('middle')));
        self::assertFalse($option->isCovered(new Lifestyle('squatter')));
        self::assertFalse($option->isCovered(new Lifestyle('street')));
    }

    /**
     * Test an option that requires Squatter or higher.
     */
    public function testOptionCoveredBySquatter(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'Squatter';
        self::assertFalse($option->isCovered(new Lifestyle('commercial')));
        self::assertTrue($option->isCovered(new Lifestyle('high')));
        self::assertTrue($option->isCovered(new Lifestyle('low')));
        self::assertTrue($option->isCovered(new Lifestyle('luxury')));
        self::assertTrue($option->isCovered(new Lifestyle('middle')));
        self::assertTrue($option->isCovered(new Lifestyle('squatter')));
        self::assertFalse($option->isCovered(new Lifestyle('street')));
    }

    /**
     * Test an option with an invalid MinimumLifestyle.
     */
    public function testOptionCoveredUnknown(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'Unknown';
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Option has invalid minimum lifestyle');
        $option->isCovered(new Lifestyle('luxury'));
    }

    /**
     * Test getting the cost of an option covered by the lifestyle.
     */
    public function testGetCostCovered(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'High';
        self::assertSame(0, $option->getCost(new Lifestyle('high')));
    }

    /**
     * Test getting the cost of an option that can't be covered by the
     * lifestyle.
     */
    public function testGetCostWithMultiplierUncoverable(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'None';
        $option->cost = null;
        $option->costMultiplier = 0.2;
        self::assertSame(1000, $option->getCost(new Lifestyle('middle')));
        self::assertSame(2000, $option->getCost(new Lifestyle('high')));
    }

    /**
     * Test the cost of an option that can't be covered, and reduces the cost.
     */
    public function testGetCostWithNegativeMultiplier(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'None';
        $option->cost = null;
        $option->costMultiplier = -0.2;
        self::assertSame(-1000, $option->getCost(new Lifestyle('middle')));
        self::assertSame(-2000, $option->getCost(new Lifestyle('high')));
    }

    /**
     * Test getting the cost of some options that aren't covered and don't
     * multiply the baseCost.
     */
    public function testGetCost(): void
    {
        $option = new LifestyleOption('swimming-pool');
        $option->minimumLifestyle = 'None';
        self::assertSame(100, $option->getCost(new Lifestyle('low')));
        self::assertSame(100, $option->getCost(new Lifestyle('luxury')));
    }
}
