<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Enums;

use Modules\Avatar\Enums\Era;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class EraTest extends TestCase
{
    public function testValues(): void
    {
        self::assertSame(
            [
                'aang',
                'hundred-year-war',
                'korra',
                'kyoshi',
                'roku',
            ],
            Era::values()
        );
    }
}
