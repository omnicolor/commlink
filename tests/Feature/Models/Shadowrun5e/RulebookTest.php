<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Rulebook;
use RuntimeException;
use Tests\TestCase;

/**
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class RulebookTest extends TestCase
{
    /**
     * @test
     */
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Ruleset ID "not-found" is invalid');
        new Rulebook('not-found');
    }

    /**
     * @test
     */
    public function testLoadCore(): void
    {
        $book = new Rulebook('core');
        self::assertSame('Core 5th Edition', (string) $book);
        self::assertTrue($book->default);
        self::assertTrue($book->required);
        self::assertNotNull($book->description);
    }

    /**
     * @test
     */
    public function testLoadForbiddenArcana(): void
    {
        $book = new Rulebook('forbidden-arcana');
        self::assertSame('Forbidden Arcana', (string) $book);
        self::assertFalse($book->default);
        self::assertFalse($book->required);
        self::assertNotNull($book->description);
    }

    /**
     * @test
     */
    public function testAll(): void
    {
        $books = Rulebook::all();
        self::assertNotEmpty($books);
        self::assertInstanceOf(Rulebook::class, current($books));
    }
}
