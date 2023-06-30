<?php

declare(strict_types=1);

namespace App\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;
use App\Models\Channel;
use App\Models\ChatUser;

/**
 * @psalm-suppress UnusedClass
 */
class HelpResponse
{
    public function __construct(protected DiscordMessageReceived $event)
    {
    }

    public function __toString(): string
    {
        $help = \sprintf(
            '%1$s is a Discord bot that lets you roll dice appropriate for '
                . 'various RPG systems. For example, if you are playing The '
                . 'Expanse, it will roll three dice, marking one of them as '
                . 'the "drama die", adding up the result with the number you '
                . 'give for your attribute+focus score, and return the result '
                . 'along with any stunt points.' . \PHP_EOL . \PHP_EOL
                . 'If your game uses the web app for %1$s (%2$s) as well, '
                . 'links in the app will automatically roll in Discord, and '
                . 'changes made to your character via Discord will appear in '
                . '%1$s.' . \PHP_EOL . \PHP_EOL,
            config('app.name'),
            config('app.url')
        );

        $chatUser = ChatUser::where('server_id', $this->event->server->id)
            ->where('remote_user_id', optional($this->event->user)->id)
            ->first();
        if (null === $chatUser) {
            $help .= \sprintf(
                'Your Slack user has not been linked with a %s user. Go to the '
                    . 'settings page (%s/settings) and copy the command listed '
                    . 'there for this server. If the server isn\'t listed, '
                    . 'follow the instructions there to add it. You\'ll need '
                    . 'to know your server ID (`%s`) and your user ID (`%s`).',
                config('app.name'),
                config('app.url'),
                $this->event->server->id,
                optional($this->event->user)->id,
            ) . \PHP_EOL . \PHP_EOL;
        }

        $discordChannel = $this->event->channel;
        $channel = Channel::discord()
            ->where('channel_id', $discordChannel->id)
            ->where('server_id', $this->event->server->id)
            ->first();
        if (null === $channel) {
            // Channel is unregistered.
            $systems = [];
            foreach (config('app.systems') as $code => $name) {
                $systems[] = \sprintf('%s (%s)', $code, $name);
            }

            $help .= '**Commands for unregistered channels:**' . \PHP_EOL
                . '· `help` - Show help' . \PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                . 'optionally adding C to the result, optionally '
                . 'describing that the roll is for "text"' . \PHP_EOL
                . '· `register <system>` - Register this channel for '
                . 'system code <system>, where <system> is one of: '
                . \implode(', ', $systems);
            return $help;
        }

        $help .= 'This channel is registered for ' . $channel->getSystem()
            . '.';
        return $help;
    }
}
