<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Shadowrun5e\Number;
use Facades\App\Services\DiceService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('events')]
#[Medium]
final class RollEventTest extends TestCase
{
    public function testConstructor(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(5)
            ->with(6)
            ->andReturn(5);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $roll = new Number('5', 'Bob', $channel);
        $event = new RollEvent($roll, $channel);
        self::assertSame($channel, $event->source);
        self::assertSame($roll, $event->roll);
    }
}
