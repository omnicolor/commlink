<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Traits;

use App\Models\Character;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
final class GameSystemTest extends TestCase
{
    public function testSystemNotSet(): void
    {
        $character = new Character();
        self::assertSame('Unknown', $character->getSystem());
    }

    public function testUnknownSystem(): void
    {
        $character = new Character(['system' => 'unregistered-system']);
        self::assertSame('unregistered-system', $character->getSystem());
    }

    public function testGetSysem(): void
    {
        $character = new Character(['system' => 'shadowrun5e']);
        self::assertSame('Shadowrun 5th Edition', $character->getSystem());
    }
}
