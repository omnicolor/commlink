<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Models\Power;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class PowerTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Power ID "not-found" is invalid');
        new Power('not-found');
    }

    public function testFound(): void
    {
        $power = new Power('tack');
        self::assertSame('Tack', (string)$power);
        self::assertSame(Power::TYPE_MARQUEE, $power->type);
        self::assertNull($power->advanced_list);
    }

    public function testAdvancedList(): void
    {
        $power = new Power('ally');
        self::assertSame('communications', $power->advanced_list);
    }
}
