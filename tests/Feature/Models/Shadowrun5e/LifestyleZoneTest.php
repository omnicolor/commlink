<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\LifestyleZone;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Shadowrun 5E lifestyle zones.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class LifestyleZoneTest extends TestCase
{
    /**
     * Test trying to load an invalid zone.
     * @test
     */
    public function testLoadInvalidZone(): void
    {
        self::expectException(RuntimeException::class);
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
        self::assertSame('2d6 hours', $lifestyle->response_time);
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
