<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Features;

use Modules\Avatar\Features\ProtectorsBurden;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class ProtectorsBurdenTest extends TestCase
{
    public function testFeatureSetBurden(): void
    {
        $feature = new ProtectorsBurden(['burden' => 'Phil']);
        self::assertSame('Protector’s Burden', (string)$feature);
        self::assertStringStartsWith(
            'You take it upon yourself',
            $feature->description(),
        );
        self::assertStringContainsString(
            'your ward:** Phil',
            $feature->description(),
        );
    }

    public function testFeatureDefaultBurden(): void
    {
        $feature = new ProtectorsBurden([]);
        self::assertStringContainsString(
            'your ward:** Unknown',
            $feature->description(),
        );
    }
}
