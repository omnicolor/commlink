<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Enums;

use Modules\Cyberpunkred\Enums\CostCategory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use ValueError;

#[Group('cyberpunkred')]
#[Small]
final class CostCategoryTest extends TestCase
{
    /**
     * Test loading a cost category that isn't valid.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage(
            '"invalid" is not a valid backing value for enum '
                . CostCategory::class
        );
        CostCategory::from('invalid');
    }

    /**
     * Test trying to load a cost category with a non-string.
     */
    public function testLoadNonString(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage(
            CostCategory::class . '::from(): Argument #1 '
                . '($value) must be of type string, int given'
        );
        CostCategory::from(1);
    }

    /**
     * Provider for testing cost category to EB mapping.
     * @return array<int, array<int, int|string>>
     */
    public static function categoryProvider(): array
    {
        return [
            ['Cheap', 10],
            ['Everyday', 20],
            ['Costly', 50],
            ['Premium', 100],
            ['Expensive', 500],
            ['V. Expensive', 1000],
            ['Luxury', 5000],
            ['Super Luxury', 10000],
        ];
    }

    /**
     * Test the market price for each cost category.
     */
    #[DataProvider('categoryProvider')]
    public function testMarketPrice(string $categoryName, int $cost): void
    {
        $category = CostCategory::from($categoryName);
        self::assertSame($cost, $category->marketPrice());
    }
}
