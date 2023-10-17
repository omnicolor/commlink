<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Origin;
use App\Models\Expanse\Origin\Belter;
use App\Models\Expanse\Origin\Earther;
use App\Models\Expanse\Origin\Martian;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Expanse origins.
 * @group models
 * @group expanse
 * @small
 */
final class OriginTest extends TestCase
{
    /**
     * Test trying to create an invalid origin.
     * @test
     */
    public function testInvalidOrigin(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Origin "jovian" is invalid');
        Origin::factory('Jovian');
    }

    /**
     * Test trying to create a belter.
     * @test
     */
    public function testBelter(): void
    {
        $origin = Origin::factory('belter');
        self::assertInstanceOf(Belter::class, $origin);
        self::assertSame('Belter', (string)$origin);
    }

    /**
     * Test trying to create an Earther.
     * @test
     */
    public function testEarther(): void
    {
        $origin = Origin::factory('Earther');
        self::assertInstanceOf(Earther::class, $origin);
        self::assertSame('Earther', (string)$origin);
    }

    /**
     * Test trying to create a Martian.
     * @test
     */
    public function testMartian(): void
    {
        $origin = Origin::factory('martian');
        self::assertInstanceOf(Martian::class, $origin);
        self::assertSame('Martian', (string)$origin);
    }
}
