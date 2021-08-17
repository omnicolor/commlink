<?php

declare(strict_types=1);

namespace App\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;
use App\Events\DiscordUserLinked;
use App\Models\ChatUser;

/**
 * Discord response for linking a Commlink user to a Discord user.
 */
class ValidateUserResponse
{
    /**
     * Construct a new instance.
     * @param DiscordMessageReceived $event
     */
    public function __construct(protected DiscordMessageReceived $event)
    {
        $arguments = \explode(' ', trim($this->event->content));
        if (2 !== \count($arguments)) {
            $this->event->message->reply(\sprintf(
                'To link your Commlink user, go to the settings page '
                    . '(%s/settings) and copy the command listed there for '
                    . 'this server. If the server isn\'t listed, follow the '
                    . 'instructions there to add it. You\'ll need to know your '
                    . 'server ID (`%s`) and your user ID (`%s`).',
                config('app.url'),
                $this->event->server->id,
                $this->event->user->id,
            ));
            return;
        }

        $hash = $arguments[1];

        $chatUsers = ChatUser::discord()
            ->where('server_id', $this->event->server->id)
            ->where('remote_user_id', $this->event->user->id)
            ->get();
        foreach ($chatUsers as $user) {
            if ($user->verification !== $hash) {
                // Not the right user.
                continue;
            }
            if ($user->verified) {
                $this->event->message->reply(
                    'It looks like you\'re already verified!'
                );
                return;
            }

            $user->verified = true;
            $user->save();
            DiscordUserLinked::dispatch($user);

            $this->event->message->reply(
                'Your Commlink account has been linked with this Discord user. '
                    . 'You only need to do this once for this server, no '
                    . 'matter how many different channels you play in.'
            );
            return;
        }

        $this->event->message->reply(sprintf(
            'We couldn\'t find a Commlink registration for this Discord server '
                . 'and your user. Go to the settings page (%s/settings) and '
                . 'copy the command listed there for this server. If the '
                . 'server isn\'t listed, follow the instructions there to add '
                . 'it. You\'ll need to know your server ID (`%s`) and your '
                . 'user ID (`%s`).',
            config('app.url'),
            $this->event->server->id,
            $this->event->user->id,
        ));
    }

    /**
     * Format the response for Discord.
     * @return string
     */
    public function __toString(): string
    {
        return '';
    }
}
