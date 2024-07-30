<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Roll;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

use function sprintf;

class HandleRollEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Handle the event.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function handle(RollEvent $event): void
    {
        if (null === $event->source) {
            // Not having a source should not happen.
            return;
        }

        $campaign = $event->source->campaign;
        if (null === $campaign) {
            // Without a campaign to tie the channels together, there's nothing
            // to broadcast.
            return;
        }

        foreach ($event->source->campaign->channels ?? [] as $channel) {
            if ($event->source->id === $channel->id) {
                // Don't broadcast back to the original channel.
                continue;
            }
            if ('slack' === $channel->type) {
                $this->sendToSlack($event->roll, $channel);
                continue;
            }
            if (null === $channel->webhook) {
                // We can't broadcast to Discord channels without webhooks.
                continue;
            }
            $this->sendToDiscord($event->roll, $channel);
        }
    }

    /**
     * Send the roll to a linked Slack channel.
     * @param Roll $roll
     * @param Channel $channel
     */
    protected function sendToSlack(Roll $roll, Channel $channel): void
    {
        try {
            $data = $roll->forSlack()->getData();
        } catch (Exception) {
            return;
        }
        $data->response_type = null;
        $data->channel = $channel->channel_id;

        // TODO: Add error handling.
        Http::withHeaders(
            [
                'Authorization' => sprintf('Bearer %s', config('services.slack.bot_token')),
                'Content-Type' => 'application/json;charset=UTF-8',
            ],
        )->post('https://slack.com/api/chat.postMessage', (array)$data);
    }

    /**
     * Send the roll to a linked Discord channel.
     * @param Roll $roll
     * @param Channel $channel
     */
    protected function sendToDiscord(Roll $roll, Channel $channel): void
    {
        $content = $roll->forDiscord();
        $data = [
            'content' => $content,
        ];

        /** @var string */
        $url = $channel->webhook;

        // TODO: Add error handling.
        Http::withHeaders([
            'Authorization' => sprintf('Bot %s', config('discord_token')),
        ])
            ->post($url, $data);
    }
}
