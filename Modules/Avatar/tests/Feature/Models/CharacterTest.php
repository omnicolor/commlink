<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Models;

use Modules\Avatar\Models\Background;
use Modules\Avatar\Models\Character;
use Modules\Avatar\Models\Era;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testToStringUnnamed(): void
    {
        $character = new Character();
        self::assertSame('Unnamed character', (string)$character);
    }

    public function testToStringNamed(): void
    {
        $character = new Character(['name' => 'Aang']);
        self::assertSame('Aang', (string)$character);
    }

    public function testSetBackgroundEnum(): void
    {
        $character = new Character();
        $character->background = Background::Urban;
        self::assertSame('urban', $character->background->value);
    }

    public function testSetBackgroundString(): void
    {
        $character = new Character(['background' => 'outlaw']);
        self::assertSame(Background::Outlaw, $character->background);
    }

    public function testSetEraEnum(): void
    {
        $character = new Character();
        $character->era = Era::Roku;
        self::assertSame('roku', $character->era->value);
    }

    public function testSetEraString(): void
    {
        $character = new Character(['era' => 'roku']);
        self::assertSame(Era::Roku, $character->era);
    }
}
