<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Rulebook;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class RulebookTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Ruleset ID "not-found" is invalid');
        new Rulebook('not-found');
    }

    public function testLoadCore(): void
    {
        $book = new Rulebook('core');
        self::assertSame('Core 5th Edition', (string) $book);
        self::assertTrue($book->default);
        self::assertTrue($book->required);
    }

    public function testLoadForbiddenArcana(): void
    {
        $book = new Rulebook('forbidden-arcana');
        self::assertSame('Forbidden Arcana', (string) $book);
        self::assertFalse($book->default);
        self::assertFalse($book->required);
    }

    public function testAll(): void
    {
        $books = Rulebook::all();
        self::assertNotEmpty($books);
        self::assertInstanceOf(Rulebook::class, current($books));
    }
}
