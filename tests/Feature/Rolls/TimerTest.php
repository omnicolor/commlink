<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Channel;
use App\Models\User;
use App\Rolls\Timer;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_encode;

use const JSON_THROW_ON_ERROR;

#[Medium]
final class TimerTest extends TestCase
{
    #[Group('discord')]
    public function testTimerDiscordWithoutWebhook(): void
    {
        $timer = new Timer(
            'timer',
            '',
            new Channel(['type' => ChannelType::Discord]),
        );
        self::assertSame(
            'Discord webhooks must be set up to use timers',
            $timer->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testTimerIrc(): void
    {
        $timer = new Timer(
            'timer',
            '',
            new Channel(['type' => ChannelType::Irc]),
        );
        self::assertSame(
            'IRC channels are not supported',
            $timer->forIrc(),
        );
    }

    #[Group('slack')]
    public function testUnknownTimerCommand(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('I\'m not sure what that means.');
        (new Timer(
            'timer foo',
            '',
            new Channel(['type' => ChannelType::Slack]),
        ))->forSlack();
    }

    #[Group('discord')]
    public function testTimerCreateWithoutTime(): void
    {
        $timer = new Timer(
            'timer create',
            '',
            new Channel([
                'type' => ChannelType::Discord,
                'webhook' => 'https://example.com/webhook',
            ]),
        );

        self::assertSame(
            'You didn\'t specify a time.',
            $timer->forDiscord(),
        );
    }

    #[Group('slack')]
    public function testTimerCreateShorthand(): void
    {
        $timer = new Timer(
            'timer 10',
            '',
            Channel::create([
                'channel_id' => 'C1234567',
                'registered_by' => User::factory()->create()->id,
                'server_id' => 'T12345',
                'system' => 'shadowrun5e',
                'type' => ChannelType::Slack,
            ]),
        );
        self::assertStringContainsString(
            'I\'ll let you know when 10 minutes is up.',
            json_encode(
                $timer->forSlack()->jsonSerialize(),
                JSON_THROW_ON_ERROR,
            ),
        );
    }

    #[Group('discord')]
    public function testTimerCreate(): void
    {
        $timer = new Timer(
            'timer create 1:00',
            '',
            Channel::create([
                'channel_id' => '1231231',
                'registered_by' => User::factory()->create()->id,
                'server_id' => '123156354',
                'system' => 'shadowrun5e',
                'type' => ChannelType::Discord,
                'webhook' => 'https://example.com/webhook',
            ]),
        );

        self::assertStringContainsString(
            'I\'ll let you know when 1 hour is up.',
            $timer->forDiscord(),
        );
    }
}
