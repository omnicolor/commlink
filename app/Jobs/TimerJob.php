<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Channel;
use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Response;
use Omnicolor\Slack\Sections\Markdown;
use RuntimeException;

use function config;
use function sprintf;

/**
 * A user in one of the supported chat systems can "roll" to create a timer.
 * The roll will create an instance of this job, delayed by how long the timer
 * is supposed to be for. When this job executes, it will notify the channel
 * it was created in that the timer is finished.
 *
 * Usage: TimerJob::dispatch($channel, $interval, $username)->delay($interval);
 * @see \App\Rolls\Timer
 */
class TimerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $channel_id;
    public string $webhook_url;

    public function __construct(
        public readonly Channel $channel,
        public readonly CarbonInterval $interval,
        public readonly string $user,
    ) {
        switch ($this->channel->type) {
            case Channel::TYPE_SLACK:
                $this->channel_id = $channel->channel_id;
                break;
            case Channel::TYPE_DISCORD:
                if (null === $channel->webhook) {
                    throw new RuntimeException(
                        'Can not start time for Discord channel without webhook',
                    );
                }
                $this->webhook_url = $channel->webhook;
                break;
            default:
                throw new RuntimeException(sprintf(
                    'Can not start timer for %s channels',
                    $channel->type,
                ));
        }
    }

    public function handle(): void
    {
        switch ($this->channel->type) {
            case Channel::TYPE_SLACK:
                $this->sendToSlack();
                return;
            case Channel::TYPE_DISCORD:
                $this->sendToDiscord();
                return;
        }
    }

    private function sendToDiscord(): void
    {
        $message = [
            'content' => sprintf(
                '<@%s>, your %s timer is finished.',
                $this->user,
                $this->interval,
            ),
        ];

        // TODO: Add error handling.
        Http::withHeaders([
            'Authorization' => sprintf('Bot %s', config('discord_token')),
        ])
            ->post($this->webhook_url, $message);
    }

    private function sendToSlack(): void
    {
        $message = (new Response())
            ->addBlock(new Header('Timer is finished!'))
            ->addBlock(new Markdown(sprintf(
                '<@%s>, your timer for %s is done.',
                $this->user,
                $this->interval,
            )))
            ->sendToChannel()
            ->jsonSerialize();
        $message['channel'] = $this->channel_id;

        // TODO: Add error handling.
        Http::withHeaders(
            [
                'Authorization' => sprintf('Bearer %s', config('services.slack.bot_token')),
                'Content-Type' => 'application/json;charset=UTF-8',
            ],
        )->post('https://slack.com/api/chat.postMessage', $message);
    }
}
