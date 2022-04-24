<?php

declare(strict_types=1);

namespace App\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;
use App\Models\Channel;

class InfoResponse
{
    /**
     * Construct a new instance.
     * @param DiscordMessageReceived $event
     */
    public function __construct(protected DiscordMessageReceived $event)
    {
    }

    /**
     * Format the response for Discord.
     * @return string
     */
    public function __toString(): string
    {
        /** @var \Discord\Parts\Channel\Channel */
        $textChannel = $this->event->channel;
        $channel = Channel::discord()
            ->where('channel_id', $textChannel->id)
            ->where('server_id', $this->event->server->id)
            ->first();
        $campaignName = 'No campaign';
        $system = 'Unregistered';
        $character = 'No character';
        if (null !== $channel) {
            $system = $channel->getSystem();
            if (null !== $channel->campaign) {
                $campaignName = $channel->campaign->name;
            }
        }
        return '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($this->event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($this->event->user)->id . \PHP_EOL
            . 'Server Name: ' . $this->event->server->name . \PHP_EOL
            . 'Server ID: ' . $this->event->server->id . \PHP_EOL
            . 'Channel Name: ' . $textChannel->name . \PHP_EOL
            . 'Channel ID: ' . $textChannel->id . \PHP_EOL
            . 'System: ' . $system . \PHP_EOL
            . 'Character: ' . 'Unlinked' . \PHP_EOL
            . 'Campaign: ' . $campaignName;
    }
}
