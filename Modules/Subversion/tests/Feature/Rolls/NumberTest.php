<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Subversion\Rolls\Number;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('subversion')]
#[Medium]
final class NumberTest extends TestCase
{
    /**
     * Test trying to roll a simple three-dice roll without an attribute or TN
     * set in Slack.
     */
    #[Group('slack')]
    public function testSimpleRollSlack(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([4, 4, 4]);

        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('3', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();
        self::assertSame(
            'in_channel',
            $response['response_type']
        );
        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'footer' => '4 4 4',
                'text' => 'Rolled 3 dice: 4 + 4 + 4 = 12',
                'title' => 'username rolled 12',
            ],
            $response['attachments'][0],
        );
    }

    /**
     * Test trying to roll a simple three-dice roll without the TN set in
     * Discord, but including an attribute.
     */
    #[Group('discord')]
    public function testSimpleRollDiscord(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(4, 6)
            ->andReturn([5, 5, 5, 5]);

        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('4', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 15**' . PHP_EOL
                . 'Rolled 4 dice: 5 + 5 + 5 = 15' . PHP_EOL
                . 'Rolls: 5 5 5 5',
            $response
        );
    }

    #[Group('irc')]
    public function testRollIrc(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(4, 6)
            ->andReturn([5, 5, 5, 5]);

        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('4', 'username', $channel))->forIrc();
        self::assertEquals(
            'username rolled 15' . PHP_EOL
                . 'Rolled 4 dice: 5 + 5 + 5 = 15' . PHP_EOL
                . 'Rolls: 5 5 5 5',
            $response
        );
    }

    #[Group('discord')]
    public function testDulled5Roll(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([6, 6, 6]);

        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('2', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 15**' . PHP_EOL
                . 'Rolled 3 dulled (5) dice: 5 + 5 + 5 = 15' . PHP_EOL
                . 'Rolls: 6 6 6',
            $response
        );
    }

    #[Group('discord')]
    public function testDulled4Roll(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([6, 6, 6]);

        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('1', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 12**' . PHP_EOL
                . 'Rolled 3 dulled (4) dice: 4 + 4 + 4 = 12' . PHP_EOL
                . 'Rolls: 6 6 6',
            $response
        );
    }

    #[Group('discord')]
    public function testDulled3Roll(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([6, 6, 6]);

        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('0', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 9**' . PHP_EOL
                . 'Rolled 3 dulled (3) dice: 3 + 3 + 3 = 9' . PHP_EOL
                . 'Rolls: 6 6 6',
            $response
        );
    }
}
