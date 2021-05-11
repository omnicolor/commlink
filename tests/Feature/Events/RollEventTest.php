<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Shadowrun5e\Number;

/**
 * Tests for RollEvents.
 * @group events
 * @small
 */
final class RollEventTest extends \Tests\TestCase
{
    use \phpmock\phpunit\PHPMock;

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
        $randomInt->expects(self::any())->willReturn(random_int(1, 6));

        $roll = new Number('5', 'Bob');
        $channel = Channel::factory()->make([
            'system' => 'shadowrun5e',
        ]);
        $event = new RollEvent($roll, $channel);
        self::assertSame($channel, $event->source);
        self::assertSame($roll, $event->roll);
    }
}
