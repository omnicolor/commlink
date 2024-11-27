<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models;

use Modules\Cyberpunkred\Models\TarotDeck;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('cyberpunkred')]
#[Small]
final class TarotDeckTest extends TestCase
{
    public function testDrawTooFew(): void
    {
        $deck = new TarotDeck();
        self::expectException(RuntimeException::class);
        $deck->draw(0);
    }
}
