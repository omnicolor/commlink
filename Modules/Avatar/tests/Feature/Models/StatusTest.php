<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Models;

use Modules\Avatar\Models\Status;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class StatusTest extends TestCase
{
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Status ID "unknown" is invalid');
        new Status('unknown');
    }

    public function testLoadValid(): void
    {
        $status = new Status('doomed');
        self::assertSame('Doomed', (string)$status);
    }

    public function testAll(): void
    {
        self::assertCount(2, Status::all());
    }
}
