<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Features;

use Modules\Avatar\Features\TheLodestar;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class TheLodestarTest extends TestCase
{
    public function testLodestar(): void
    {
        $feature = new TheLodestar(['lodestar' => 'Phil']);
        self::assertSame('The Lodestar', (string)$feature);
        self::assertStringStartsWith(
            'There’s only one person',
            $feature->description(),
        );
        self::assertStringContainsString(
            'your lodestar:** Phil',
            $feature->description(),
        );
    }

    public function testLodestarWithoutSetting(): void
    {
        $feature = new TheLodestar([]);
        self::assertStringContainsString(
            'your lodestar:** Unknown',
            $feature->description(),
        );
    }
}
