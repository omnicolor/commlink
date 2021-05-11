<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\LifestyleZone;

/**
 * Tests for Shadowrun 5E lifestyle zones.
 * @covers \App\Models\Shadowrun5E\LifestyleZone
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class LifestyleZoneTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid zone.
     * @test
     */
    public function testLoadInvalidZone(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Lifestyle Zone ID "q" is invalid');
        new LifestyleZone('q');
    }

    /**
     * Test trying to load a valid zone.
     * @test
     */
    public function testLoadValidZone(): void
    {
        $lifestyle = new LifestyleZone('z');
        self::assertSame('Z', $lifestyle->name);
        self::assertSame('2d6 hours', $lifestyle->responseTime);
        self::assertNotNull($lifestyle->description);
    }

    /**
     * Test casting a zone to a string.
     * @test
     */
    public function testToString(): void
    {
        $lifestyle = new LifestyleZone('z');
        self::assertSame('Z', (string)$lifestyle);
    }
}
