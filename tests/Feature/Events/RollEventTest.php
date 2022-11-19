<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Shadowrun5e\Number;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpmock\phpunit\PHPMock;
use Tests\TestCase;

/**
 * Tests for RollEvents.
 * @group events
 * @medium
 */
final class RollEventTest extends TestCase
{
    use PHPMock;
    use RefreshDatabase;

    /**
     * Test the constructor.
     * @test
     */
    public function testConstructor(): void
    {
        $randomInt = $this->getFunctionMock(
            'App\\Rolls\\Shadowrun5e',
            'random_int'
        );
        $randomInt->expects(self::exactly(5))->willReturn(5);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $roll = new Number('5', 'Bob', $channel);
        $event = new RollEvent($roll, $channel);
        self::assertSame($channel, $event->source);
        self::assertSame($roll, $event->roll);
    }
}
