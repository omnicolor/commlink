<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Models;

use Modules\Avatar\Models\Move;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class MoveTest extends TestCase
{
    public function testLoadUnknown(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Move ID "invalid" is invalid');
        new Move('invalid');
    }

    public function testToString(): void
    {
        $move = new Move('driven-by-justice');
        self::assertSame('Driven by Justice', (string)$move);
    }

    public function testWithPlaybook(): void
    {
        $move = new Move('driven-by-justice');
        self::assertSame('The Adamant', $move->playbook?->name);
    }

    public function testWithoutPlaybook(): void
    {
        $move = new Move('live-up-to-your-principle');
        self::assertNull($move->playbook);
    }

    public function testAll(): void
    {
        self::assertCount(6, Move::all());
    }
}
