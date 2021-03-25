<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Condition;

/**
 * Tests for Expanse conditions.
 * @covers \App\Models\Expanse\Condition
 * @group models
 * @group expanse
 */
final class ConditionTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid Condition.
     * @test
     */
    public function testLoadInvalidCondition(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Condition ID "q" is invalid');
        new Condition('q');
    }

    /**
     * Test trying to load a valid Condition.
     * @test
     */
    public function testLoadValidCondition(): void
    {
        $condition = new Condition('deafened');
        self::assertSame('deafened', $condition->id);
        self::assertSame('Deafened', $condition->name);
        self::assertNotNull($condition->description);
        self::assertSame(21, $condition->page);
    }

    /**
     * Test casting a condition to a string.
     * @test
     */
    public function testToString(): void
    {
        $condition = new Condition('deafened');
        self::assertSame('Deafened', (string)$condition);
    }
}
