<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Shadowrun5e\Number;
use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for RollEvents.
 * @group events
 * @medium
 */
final class RollEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the constructor.
     * @test
     */
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
