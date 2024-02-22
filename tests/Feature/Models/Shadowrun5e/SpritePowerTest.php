<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\SpritePower;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for the SpritePower class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class SpritePowerTest extends TestCase
{
    /**
     * Test trying to load an invalid power.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Sprite power ID "not-found" is invalid');
        new SpritePower('not-found');
    }

    /**
     * Test loading a valid power.
     * @test
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
