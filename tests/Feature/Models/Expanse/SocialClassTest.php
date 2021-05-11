<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\SocialClass;

/**
 * Tests for Expanse social classes.
 * @covers \App\Models\Expanse\SocialClass
 * @group models
 * @group expanse
 * @small
 */
final class SocialClassTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid class.
     * @test
     */
    public function testLoadInvalidClass(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Social Class ID "q" is invalid');
        new SocialClass('q');
    }

    /**
     * Test trying to load a valid class.
     * @test
     */
    public function testLoadValidClass(): void
    {
        $class = new SocialClass('outsider');
        self::assertSame('outsider', $class->id);
        self::assertSame('Outsider', $class->name);
        self::assertNotNull($class->description);
    }

    /**
     * Test casting a class to a string.
     * @test
     */
    public function testToString(): void
    {
        $class = new SocialClass('middle');
        self::assertSame('Middle Class', (string)$class);
    }
}
