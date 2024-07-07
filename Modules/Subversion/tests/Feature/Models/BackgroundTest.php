<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Modules\Subversion\Models\Background;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class BackgroundTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Background "not-found" not found');
        new Background('not-found');
    }

    public function testConstructor(): void
    {
        $background = new Background('agriculturist');
        self::assertSame('Agriculturist', (string)$background);
    }

    public function testAll(): void
    {
        $backgrounds = Background::all();
        self::assertCount(1, $backgrounds);
    }
}
