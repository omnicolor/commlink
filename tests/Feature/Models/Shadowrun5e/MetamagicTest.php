<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Metamagic;

/**
 * Tests for Metamagic class.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class MetamagicTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid Metamagic.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Metamagic ID "foo" is invalid');
        new Metamagic('foo');
    }

    /**
     * Test trying to load a valid Metamagic and convert it to a string.
     * @test
     */
    public function testToString(): void
    {
        $magic = new Metamagic('centering');
        self::assertSame('Centering', (string)$magic);
    }

    /**
     * Test trying to find a metamagic by name, not found.
     * @test
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Metamagic "Not Found" was not found');
        Metamagic::findByName('Not Found');
    }

    /**
     * Test trying to find a metamagic by name.
     * @test
     */
    public function testFindByName(): void
    {
        self::assertInstanceOf(
            Metamagic::class,
            Metamagic::findByName('Centering')
        );
    }
}
