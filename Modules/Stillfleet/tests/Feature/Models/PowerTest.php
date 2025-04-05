<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Enums\AdvancedPowersCategory;
use Modules\Stillfleet\Models\Power;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class PowerTest extends TestCase
{
    public function testFound(): void
    {
        $power = Power::findOrFail('tack');
        self::assertSame('Tack', (string)$power);
        self::assertSame(Power::TYPE_MARQUEE, $power->type);
        self::assertNull($power->advanced_list);
    }

    public function testAdvancedList(): void
    {
        $power = Power::findOrFail('ally');
        self::assertSame(
            AdvancedPowersCategory::Communications->value,
            $power->advanced_list,
        );
    }
}
