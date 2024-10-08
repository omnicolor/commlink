<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\SpritePower;
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
        self::assertSame('Cookie', $power->name);
        self::assertSame('Cookie', (string)$power);
        self::assertSame(256, $power->page);
        self::assertSame('core', $power->ruleset);
    }
}
