<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\LifestyleAttributes;

/**
 * Tests for Shadowrun 5E lifestyle attributes.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class LifestyleAttributesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test trying to initialize attributes without required data.
     * @test
     */
    public function testMissingAttributes(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Lifestyle attributes missing');
        new LifestyleAttributes([]);
    }

    /**
     * Test initializing lifestyle attributes with garbage.
     * @test
     */
    public function testGarbageAttributes(): void
    {
        // @phpstan-ignore-next-line
        $attributes = new LifestyleAttributes([
            'comforts' => 'a',
            'comfortsMax' => 'b',
            'neighborhood' => 'c',
            'neighborhoodMax' => 'd',
            'security' => 'e',
            'securityMax' => 'f',
        ]);
        self::assertSame(0, $attributes->comforts);
        self::assertSame(0, $attributes->comfortsMax);
        self::assertSame(0, $attributes->neighborhood);
        self::assertSame(0, $attributes->neighborhoodMax);
        self::assertSame(0, $attributes->security);
        self::assertSame(0, $attributes->securityMax);
    }

    /**
     * Test initializing lifestyle attributes with data.
     * @test
     */
    public function testAttributes(): void
    {
        $attributes = new LifestyleAttributes([
            'comforts' => 5,
            'comfortsMax' => 7,
            'neighborhood' => 5,
            'neighborhoodMax' => 7,
            'security' => 5,
            'securityMax' => 8,
        ]);
        self::assertSame(5, $attributes->comforts);
        self::assertSame(7, $attributes->comfortsMax);
        self::assertSame(5, $attributes->neighborhood);
        self::assertSame(7, $attributes->neighborhoodMax);
        self::assertSame(5, $attributes->security);
        self::assertSame(8, $attributes->securityMax);
    }
}
