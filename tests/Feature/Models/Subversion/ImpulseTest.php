<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Impulse;
use RuntimeException;
use Tests\TestCase;

/**
 * @group subversion
 * @small
 */
final class ImpulseTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Impulse "not-found" not found');
        new Impulse('not-found');
    }

    public function testConstructor(): void
    {
        $impulse = new Impulse('indulgence');
        self::assertSame('Indulgence', (string)$impulse);
        self::assertCount(3, $impulse->responses);
        self::assertSame('Indulge', (string)$impulse->downtime);
    }

    public function testAll(): void
    {
        self::assertCount(1, Impulse::all());
    }
}
