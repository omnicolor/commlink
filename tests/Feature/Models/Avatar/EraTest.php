<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Avatar;

use App\Models\Avatar\Era;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

/**
 * @group avatar
 */
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
