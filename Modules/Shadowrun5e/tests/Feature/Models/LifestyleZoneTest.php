<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\LifestyleZone;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class LifestyleZoneTest extends TestCase
{
    /**
     * Test trying to load an invalid zone.
     */
    public function testLoadInvalidZone(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Lifestyle Zone ID "q" is invalid');
        new LifestyleZone('q');
    }

    /**
     * Test trying to load a valid zone.
     */
    public function testLoadValidZone(): void
    {
        $lifestyle = new LifestyleZone('z');
        self::assertSame('Z', $lifestyle->name);
        self::assertSame('2d6 hours', $lifestyle->response_time);
    }

    /**
     * Test casting a zone to a string.
     */
    public function testToString(): void
    {
        $lifestyle = new LifestyleZone('z');
        self::assertSame('Z', (string)$lifestyle);
    }
}
