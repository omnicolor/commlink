<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Talent;

/**
 * Tests for Expanse talents.
 * @covers \App\Models\Expanse\Talent
 * @group models
 * @group expanse
 * @small
 */
final class TalentTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid talent.
     * @test
     */
    public function testLoadInvalidTalent(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Talent ID "q" is invalid');
        new Talent('q');
    }

    /**
     * Test trying to load a valid talent.
     * @test
     */
    public function testLoadValidTalent(): void
    {
        $talent = new Talent('fringer');
        self::assertSame('fringer', $talent->id);
        self::assertSame('Fringer', $talent->name);
        self::assertNotNull($talent->description);
    }

    /**
     * Test casting a talent to a string.
     * @test
     */
    public function testToString(): void
    {
        $talent = new Talent('Fringer');
        self::assertSame('Fringer', (string)$talent);
    }

    /**
     * Test loading a talent without setting the level defaults to Novice.
     * @test
     */
    public function testDefaultLevel(): void
    {
        $talent = new Talent('fringer');
        self::assertSame(Talent::NOVICE, $talent->level);
    }

    /**
     * Test setting the level to an invalid level.
     * @test
     */
    public function testSetLevelInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Talent level outside allowed values');
        (new Talent('fringer'))->setLevel(99);
    }
}
