<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Features;

use Modules\Avatar\Enums\LegacyOfExcellenceDriveStatus;
use Modules\Avatar\Features\LegacyOfExcellence;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class LegacyOfExcellenceTest extends TestCase
{
    public function testConstructor(): void
    {
        $feature = new LegacyOfExcellence([
            'give-affection' => LegacyOfExcellenceDriveStatus::Chosen,
            'start-a-real-fight' => LegacyOfExcellenceDriveStatus::Fulfilled,
        ]);
        self::assertSame('Legacy of Excellence', (string)$feature);
        self::assertStringStartsWith(
            'You have dedicated yourself',
            $feature->description(),
        );
        self::assertStringContainsString(
            '- [ ] successfully lead',
            $feature->description(),
        );
        self::assertStringContainsString(
            '- [x] give your affection',
            $feature->description(),
        );
        self::assertStringContainsString(
            '- [x] ~~start a real fight',
            $feature->description(),
        );
    }
}
