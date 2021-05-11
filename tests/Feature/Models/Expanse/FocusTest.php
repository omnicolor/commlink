<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Focus;

/**
 * Tests for Expanse focuses.
 * @covers \App\Models\Expanse\Focus
 * @group models
 * @group expanse
 * @small
 */
final class FocusTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid focus.
     * @test
     */
    public function testLoadInvalidFocus(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Focus ID "q" is invalid');
        new Focus('q');
    }

    /**
     * Test trying to load a valid focus.
     * @test
     */
    public function testLoadValidFocus(): void
    {
        $focus = new Focus('crafting');
        self::assertSame('dexterity', $focus->attribute);
        self::assertSame('crafting', $focus->id);
        self::assertSame('Crafting', $focus->name);
        self::assertNotNull($focus->description);
        self::assertSame(47, $focus->page);
    }

    /**
     * Test casting a focus to a string.
     * @test
     */
    public function testToString(): void
    {
        $focus = new Focus('crafting');
        self::assertSame('Crafting', (string)$focus);
    }

    /**
     * Test not setting the level for the focus.
     * @test
     */
    public function testDefaultLevel(): void
    {
        $focus = new Focus('crafting');
        self::assertSame(1, $focus->level);
    }

    /**
     * Test setting the level for the focus.
     * @test
     */
    public function testSetLevel(): void
    {
        $focus = new Focus('crafting', 2);
        self::assertSame(2, $focus->level);
    }
}
