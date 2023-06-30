<?php

declare(strict_types=1);

namespace App\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;
use App\Models\Channel;

/**
 * @psalm-suppress UnusedClass
 */
class InfoResponse
{
    /**
     * Construct a new instance.
     */
    public function __construct(protected DiscordMessageReceived $event)
    {
    }

    public function __toString(): string
    {
        $textChannel = $this->event->channel;
        $channel = Channel::discord()
            ->where('channel_id', $textChannel->id)
            ->where('server_id', $this->event->server->id)
            ->first();

        $character = 'No character';
        $campaignName = 'No campaign';
        $system = 'Unregistered';
        if (null !== $channel) {
            $channel->user = $this->event->user->id;
            $system = $channel->getSystem();
            if (null !== $channel->campaign) {
                $campaignName = $channel->campaign->name;
            }
            if (null !== $channel->character()) {
                $character = (string)$channel->character();
            }
        }

        return '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($this->event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($this->event->user)->id . \PHP_EOL
            . 'Server Name: ' . $this->event->server->name . \PHP_EOL
            . 'Server ID: ' . $this->event->server->id . \PHP_EOL
            // @phpstan-ignore-next-line
            . 'Channel Name: ' . $textChannel->name . \PHP_EOL
            . 'Channel ID: ' . $textChannel->id . \PHP_EOL
            . 'System: ' . $system . \PHP_EOL
            . 'Character: ' . $character . \PHP_EOL
            . 'Campaign: ' . $campaignName;
    }
}
