<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Program;

/**
 * Unit tests for Program class
 * @covers \App\Models\Shadowrun5E\Program
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class ProgramTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid program.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Program ID "foo" is invalid');
        new Program('foo');
    }

    /**
     * Test the constructor with a program with a rating.
     * @test
     */
    public function testConstructor(): void
    {
        $program = new Program('armor');
        self::assertEquals(['cyberdeck', 'rcc'], $program->allowedDevices);
        self::assertEquals('4R', $program->availability);
        self::assertEquals(250, $program->cost);
        self::assertNotNull($program->description);
        self::assertNotEmpty($program->effects);
        self::assertEquals('armor', $program->id);
        self::assertEquals('Armor', $program->name);
        self::assertEquals(245, $program->page);
        self::assertNull($program->rating);
        self::assertFalse($program->running);
        self::assertEquals('core', $program->ruleset);
    }

    /**
     * Test the constructor with a running program with effects.
     * @test
     */
    public function testConstructorEffects(): void
    {
        $program = new Program('armor', true);
        self::assertSame(['damage-resist' => 2], $program->effects);
        self::assertSame(245, $program->page);
        self::assertTrue($program->running);
        self::assertSame('core', $program->ruleset);
    }

    /**
     * Test the __toString() method.
     * @test
     */
    public function testToString(): void
    {
        $program = new Program('armor');
        self::assertEquals('Armor', (string)$program);
    }

    /**
     * Test the getCost method.
     * @test
     */
    public function testGetCost(): void
    {
        $program = new Program('armor');
        self::assertSame(250, $program->getCost());
    }
}
