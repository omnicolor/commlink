<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Models;

use ErrorException;
use Modules\Avatar\Features\TheLodestar;
use Modules\Avatar\Models\Playbook;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class PlaybookTest extends TestCase
{
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Playbook ID "invalid" is invalid');
        new Playbook('invalid');
    }

    public function testToString(): void
    {
        self::assertSame('The Adamant', (string)(new Playbook('the-adamant')));
    }

    public function testAll(): void
    {
        self::assertCount(1, Playbook::all());
    }

    public function testMoves(): void
    {
        $playbook = new Playbook('the-adamant');
        self::assertCount(5, $playbook->moves);
    }

    public function testGetUnknownProperty(): void
    {
        $playbook = new Playbook('the-adamant');
        self::expectException(ErrorException::class);
        // @phpstan-ignore property.notFound
        self::assertNull($playbook->unknown);
    }

    public function testFeature(): void
    {
        $playbook = new Playbook('the-adamant');
        self::assertInstanceOf(TheLodestar::class, $playbook->feature);
    }
}
