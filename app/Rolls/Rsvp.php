<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Policies\EventPolicy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function sprintf;

/**
 * @psalm-suppress UnusedClass
 */
class Rsvp extends Roll
{
    public function forDiscord(): string
    {
        return 'RSVP is not a valid roll';
    }

    public function forIrc(): string
    {
        return 'RSVP is not a valid roll';
    }

    public function forSlack(): SlackResponse
    {
        throw new SlackException('RSVP is not a valid roll');
    }

    public function handleSlackAction(): void
    {
        $request = json_decode($this->content);
        if (
            !property_exists($request, 'actions')
            || !is_array($request->actions)
            || !property_exists($request->actions[0], 'action_id')
        ) {
            return;
        }
        $action = $request->actions[0];

        // Action ID should be something like rsvp:31, where rsvp is the name
        // of the action and 31 is the argument to the action.
        $arguments = explode(':', $action->action_id);
        $event = Event::find($arguments[1]);

        if (null === $event) {
            Log::warning(
                'Invalid Slack event responded to',
                [
                    'channel' => [
                        'id' => $this->channel->id,
                        'name' => $this->channel->channel_name,
                        'server' => $this->channel->server_id,
                        'server_name' => $this->channel->server_name,
                        'slack_user_id' => $this->chatUser?->remote_user_id,
                        'slack_user_name' => $this->chatUser?->remote_user_name,
                        'commlink_user_id' => $this->chatUser?->user_id,
                    ],
                    'campaign' => [
                        'id' => $this->campaign?->id,
                    ],
                    'event_id' => $arguments[1],
                ]
            );
            return;
        }

        Log::debug(
            'Found event',
            ['event' => ['id' => $event->id, 'name' => $event->name]],
        );

        if (null === $this->chatUser) {
            $data = [
                'channel' => $this->channel->channel_id,
                'user' => $this->channel->user,
                'text' => 'You don\'t appear to be registered!',
                'blocks' => [
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => sprintf(
                                'You must have already created an account on '
                                    . '<%s|%s> and linked it to this server '
                                    . 'before you can RSVP to events.',
                                config('app.url'),
                                config('app.name'),
                            ),
                        ],
                    ],
                ],
            ];
            Http::withHeaders([
                'Authorization' => sprintf('Bearer %s', config('app.slack_token')),
                'Content-Type' => 'application/json;charset=UTF-8',
            ])->post('https://slack.com/api/chat.postEphemeral', $data);
            return;
        }

        if (!(new EventPolicy())->view($this->chatUser->user, $event)) {
            $data = [
                'channel' => $this->channel->channel_id,
                'user' => $this->channel->user,
                'text' => 'You don\'t have permission for that event!',
                'blocks' => [
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => 'You must be a user attached to the '
                                . 'campaign the event is a part of in order to '
                                . 'RSVP.',
                        ],
                    ],
                ],
            ];
            $response = Http::withHeaders([
                'Authorization' => sprintf('Bearer %s', config('app.slack_token')),
                'Content-Type' => 'application/json;charset=UTF-8',
            ])->post('https://slack.com/api/chat.postEphemeral', $data);
            Log::debug(
                'Slack response after an unregistered user RSVP',
                ['response' => $response],
            );
            return;
        }

        $response = $action->selected_option->value;
        EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $this->chatUser->user?->id],
            ['response' => $response],
        );

        $data = [
            'channel' => $this->channel->channel_id,
            'user' => $this->channel->user,
            'text' => sprintf(
                'Thanks %s, we\'ve recorded your RSVP!',
                $this->username,
            ),
        ];
        Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', config('app.slack_token')),
            'Content-Type' => 'application/json;charset=UTF-8',
        ])->post('https://slack.com/api/chat.postEphemeral', $data);
    }
}
