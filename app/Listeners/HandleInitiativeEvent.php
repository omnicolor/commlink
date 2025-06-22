<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\ChannelType;
use App\Events\InitiativeAdded;
use App\Models\Channel;
use App\Models\Initiative;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Response;
use Omnicolor\Slack\Sections\Text;

use function config;
use function sprintf;

class HandleInitiativeEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function handle(InitiativeAdded $event): void
    {
        /** @var Channel $channel */
        foreach ($event->campaign->channels ?? [] as $channel) {
            if (null !== $event->source && $event->source->id === $channel->id) {
                // Don't broadcast back to the original channel. The event's
                // source will be null if it came from the web, which means it
                // should get broadcast to all other channels.
                continue;
            }
            if (ChannelType::Slack === $channel->type) {
                $this->sendToSlack($event->initiative, $channel);
                continue;
            }
            if (null === $channel->webhook) {
                // We can't broadcast to Discord channels without webhooks.
                continue;
            }
            $this->sendToDiscord($event->initiative, $channel);
        }
    }

    /**
     * Send the initiative to a linked Slack channel.
     */
    protected function sendToSlack(Initiative $initiative, Channel $channel): void
    {
        $message = (new Response())
            ->addBlock(new Header('Initiative set'))
            ->addBlock(new Text(sprintf(
                'Set initiative for "%s" to "%d"',
                $initiative->name,
                $initiative->initiative
            )))->jsonSerialize();
        unset($message['response_type']);
        $message['channel'] = $channel->channel_id;

        try {
            Http::retry(3, 100, throw: false)
                ->withHeaders([
                    'Authorization' => sprintf('Bearer %s', config('services.slack.bot_token')),
                    'Content-Type' => 'application/json;charset=UTF-8',
                ])
                ->post('https://slack.com/api/chat.postMessage', $message);
            // @codeCoverageIgnoreStart
        } catch (RequestException $ex) {
            Log::error(
                'Sending to Slack failed',
                [
                    'exception' => $ex->getMessage(),
                ]
            );
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Send the roll to a linked Discord channel.
     */
    protected function sendToDiscord(Initiative $initiative, Channel $channel): void
    {
        $data = [
            'content' => sprintf(
                'Set initiative for "%s" to "%d"',
                $initiative->name,
                $initiative->initiative
            ),
        ];

        /** @var string */
        $url = $channel->webhook;

        try {
            Http::retry(3, 100, throw: false)
                ->withHeaders([
                    'Authorization' => sprintf('Bot %s', config('discord_token')),
                ])
                ->post($url, $data);
            // @codeCoverageIgnoreStart
        } catch (RequestException $ex) {
            Log::error(
                'Sending to Slack failed',
                [
                    'exception' => $ex->getMessage(),
                ]
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
