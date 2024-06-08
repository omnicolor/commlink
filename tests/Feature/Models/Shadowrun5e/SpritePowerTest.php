<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\SpritePower;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SpritePowerTest extends TestCase
{
    /**
     * Test trying to load an invalid power.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Sprite power ID "not-found" is invalid');
        new SpritePower('not-found');
    }

    /**
     * Test loading a valid power.
     */
    public function testLoad(): void
    {
        $power = new SpritePower('cookie');
        self::assertNotNull($power->description);
        self::assertSame('Cookie', $power->name);
        self::assertSame('Cookie', (string)$power);
        self::assertSame(256, $power->page);
        self::assertSame('core', $power->ruleset);
    }
}
