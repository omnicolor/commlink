<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\Origin;
use Modules\Expanse\Models\Origin\Belter;
use Modules\Expanse\Models\Origin\Earther;
use Modules\Expanse\Models\Origin\Martian;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class OriginTest extends TestCase
{
    /**
     * Test trying to create an invalid origin.
     */
    public function testInvalidOrigin(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Origin "jovian" is invalid');
        Origin::factory('Jovian');
    }

    /**
     * Test trying to create a belter.
     */
    public function testBelter(): void
    {
        $origin = Origin::factory('belter');
        self::assertInstanceOf(Belter::class, $origin);
        self::assertSame('Belter', (string)$origin);
    }

    /**
     * Test trying to create an Earther.
     */
    public function testEarther(): void
    {
        $origin = Origin::factory('Earther');
        self::assertInstanceOf(Earther::class, $origin);
        self::assertSame('Earther', (string)$origin);
    }

    /**
     * Test trying to create a Martian.
     */
    public function testMartian(): void
    {
        $origin = Origin::factory('martian');
        self::assertInstanceOf(Martian::class, $origin);
        self::assertSame('Martian', (string)$origin);
    }
}
