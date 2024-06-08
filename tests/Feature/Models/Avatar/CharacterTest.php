<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Avatar;

use App\Models\Avatar\Background;
use App\Models\Avatar\Character;
use App\Models\Avatar\Era;
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

    public function testBackground(): void
    {
        $character = new Character();
        $character->background = Background::Urban;
        self::assertSame('Urban', $character->background);
    }

    public function testEra(): void
    {
        $character = new Character();
        $character->era = Era::Roku;
        self::assertSame('Roku', $character->era);
    }
}
