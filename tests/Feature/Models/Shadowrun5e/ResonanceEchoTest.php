<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\ResonanceEcho;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class ResonanceEchoTest extends TestCase
{
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Echo ID "invalid" is invalid');
        new ResonanceEcho('invalid');
    }

    public function testConstructor(): void
    {
        $echo = new ResonanceEcho('attack-upgrade');
        self::assertSame('Attack upgrade', (string)$echo);
    }
}
