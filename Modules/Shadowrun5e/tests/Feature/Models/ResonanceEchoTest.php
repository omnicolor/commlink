<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\ResonanceEcho;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
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
