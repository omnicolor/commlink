<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Ideology;
use RuntimeException;
use Tests\TestCase;

/**
 * @group subversion
 * @small
 */
final class IdeologyTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Ideology "not-found" not found');
        new Ideology('not-found');
    }

    public function testConstructor(): void
    {
        $ideology = new Ideology('neo-anarchist');
        self::assertSame('Neo-anarchist', (string)$ideology);
    }

    public function testAll(): void
    {
        $ideologies = Ideology::all();
        self::assertCount(1, $ideologies);
    }
}
