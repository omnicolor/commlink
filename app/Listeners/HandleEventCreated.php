<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\EventCreated;
use App\Models\Channel;
use App\Models\Event;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function sprintf;

use const PHP_EOL;

class HandleEventCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function handle(EventCreated $event): bool
    {
        foreach ($event->event->campaign->channels ?? [] as $channel) {
            switch ($channel->type) {
                case Channel::TYPE_SLACK:
                    $this->sendToSlack($event->event, $channel);
                    break;
                case Channel::TYPE_DISCORD:
                    $this->sendToDiscord($event->event, $channel);
                    break;
            }
        }
        return true;
    }

    protected function sendToSlack(Event $event, Channel $channel): void
    {
        $data = [
            'channel' => $channel->channel_id,
            'text' => sprintf('%s scheduled an event', $event->creator->name),
            'blocks' => [
                [
                    'type' => 'header',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => sprintf('%s scheduled an event', $event->creator->name),
                        'emoji' => false,
                    ],
                ],
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => sprintf('*%s*', $event->name),
                    ],
                ],
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => sprintf(':calendar: %s', $event->real_start),
                    ],
                ],
            ],
        ];
        if (null !== $event->description) {
            $data['blocks'][] = [
                'type' => 'section',
                'text' => [
                    'type' => 'plain_text',
                    'text' => $event->description ?? '',
                ],
            ];
        }
        $data['blocks'][] = [
            'type' => 'input',
            'element' => [
                'type' => 'radio_buttons',
                'options' => [
                    [
                        'text' => [
                            'type' => 'plain_text',
                            'text' => '👍I\'ll be there',
                            'emoji' => true,
                        ],
                        'value' => 'accepted',
                    ],
                    [
                        'text' => [
                            'type' => 'plain_text',
                            'text' => '👎I can\'t make it',
                            'emoji' => true,
                        ],
                        'value' => 'declined',
                    ],
                    [
                        'text' => [
                            'type' => 'plain_text',
                            'text' => '¯\\_(ツ)_/¯ Not sure',
                            'emoji' => false,
                        ],
                        'value' => 'tentative',
                    ],
                ],
                'action_id' => sprintf('rsvp:%d', $event->id),
            ],
            'label' => [
                'type' => 'plain_text',
                'text' => 'Can you make it?',
                'emoji' => false,
            ],
        ];

        Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', config('app.slack_token')),
            'Content-Type' => 'application/json;charset=UTF-8',
        ])->post('https://slack.com/api/chat.postMessage', $data);
    }

    protected function sendToDiscord(Event $event, Channel $channel): void
    {
        if (null === $channel->webhook) {
            // We can't broadcast to Discord channels without webhooks.
            return;
        }

        $description = (string)$event->real_start;
        if (null !== $event->description) {
            $description = $event->description . PHP_EOL . $description;
        }
        $data = [
            'content' => sprintf('%s scheduled an event', $event->creator->name),
            'embeds' => [
                [
                    'title' => $event->name,
                    'description' => $description,
                    'color' => 8855355,
                    'timestamp' => now()->toJSON(),
                    'fields' => [
                        [
                            'inline' => true,
                            'name' => ':thumbsup:',
                            'value' => 'I\'ll be there!',
                        ],
                        [
                            'inline' => true,
                            'name' => ':thumbsdown:',
                            'value' => 'I can\'t make it.',
                        ],
                        [
                            'inline' => true,
                            'name' => ':shrug:',
                            'value' => 'I\'m not sure.',
                        ],
                    ],
                    'footer' => [
                        'text' => sprintf('rsvp:%d', $event->id),
                    ],
                ],
            ],
        ];

        try {
            Http::withHeaders([
                'Authorization' => sprintf(
                    'Bot %s',
                    config('services.discord.token'),
                ),
            ])->post($channel->webhook, $data);
            // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            Log::error(
                'Sending to Discord failed',
                ['exception' => $ex->getMessage()]
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
