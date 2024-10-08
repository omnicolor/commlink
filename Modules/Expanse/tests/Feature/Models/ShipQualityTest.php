<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\ShipQuality;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class ShipQualityTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Expanse ship quality "not-found" is invalid'
        );
        new ShipQuality('not-found');
    }

    public function testLoad(): void
    {
        $quality = new ShipQuality('advanced-sensor-package-1');
        self::assertSame('Advanced sensor package 1', (string)$quality);
        self::assertSame(['sensors' => 1], $quality->effects);
        self::assertSame(122, $quality->page);
        self::assertSame('core', $quality->ruleset);
    }

    public function testAll(): void
    {
        self::assertCount(5, ShipQuality::all());
    }
}
