<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Background;
use App\Models\Expanse\Focus;
use App\Models\Expanse\FocusArray;
use App\Models\Expanse\Talent;
use App\Models\Expanse\TalentArray;

/**
 * Tests for Expanse backgrounds.
 * @covers \App\Models\Expanse\Background
 * @group models
 * @group expanse
 * @small
 */
final class BackgroundTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid background.
     * @test
     */
    public function testLoadInvalidBackground(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Background ID "q" is invalid');
        new Background('q');
    }

    /**
     * Test trying to load a valid background.
     * @test
     */
    public function testLoadValidBackground(): void
    {
        $background = new Background('trade');
        self::assertSame('dexterity', $background->ability);
        self::assertCount(11, $background->benefits);
        self::assertNotNull($background->description);
        self::assertCount(2, $background->focuses);
        self::assertSame('trade', $background->id);
        self::assertSame('Trade', $background->name);
        self::assertSame(33, $background->page);
        self::assertCount(2, $background->talents);
    }

    /**
     * Test casting a background to a string.
     * @test
     */
    public function testToString(): void
    {
        $background = new Background('trade');
        self::assertSame('Trade', (string)$background);
    }

    /**
     * Test getting the collection of potential focuses.
     * @test
     */
    public function testGetFocuses(): void
    {
        $focuses = (new Background('trade'))->getFocuses();
        self::assertInstanceOf(FocusArray::class, $focuses);
        /** @var Focus */
        $focus = $focuses[0];
        self::assertSame('Crafting', $focus->name);
    }

    /**
     * Test getting the collection of potential talents.
     * @test
     */
    public function testGetTalents(): void
    {
        $talents = (new Background('trade'))->getTalents();
        self::assertInstanceOf(TalentArray::class, $talents);
        /** @var Talent */
        $talent = $talents[0];
        self::assertSame('Maker', $talent->name);
    }
}
