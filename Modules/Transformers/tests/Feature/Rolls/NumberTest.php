<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Transformers\Rolls\Number;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('transformers')]
#[Medium]
final class NumberTest extends TestCase
{
    #[Group('slack')]
    public function testRollSlackFailure(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';

        $response = (new Number('5', 'user', $channel))
            ->forSlack()
            ->jsonSerialize();
        $expected = [
            'blocks' => [],
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => Attachment::COLOR_DANGER,
                    'text' => '5 >= 5',
                    'title' => 'user rolled a failure',
                ],
            ],
        ];
        self::assertSame($expected, $response);
    }

    #[Group('slack')]
    public function testRollSlackSuccessWithDescription(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';

        $response = (new Number('10 espionage', 'user', $channel))
            ->forSlack()
            ->jsonSerialize();
        $expected = [
            'blocks' => [],
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => Attachment::COLOR_SUCCESS,
                    'text' => '5 < 10',
                    'title' => 'user rolled a success for "espionage"',
                ],
            ],
        ];
        self::assertSame($expected, $response);
    }

    #[Group('discord')]
    public function testRollDiscord(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';

        self::assertSame(
            '**user rolled a success**' . PHP_EOL . '5 < 10',
            (new Number('10', 'user', $channel))->forDiscord()
        );
    }

    #[Group('discord')]
    public function testRollIrc(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';

        self::assertSame(
            'user rolled a success' . PHP_EOL . '5 < 10',
            (new Number('10', 'user', $channel))->forIrc()
        );
    }
}
