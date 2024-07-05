<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Events\UserLinked;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\Slack\TextAttachment;

use function count;
use function explode;
use function sprintf;
use function str_replace;

/**
 * @psalm-suppress UnusedClass
 */
class Validate extends Roll
{
    protected const EXPECTED_ARGUMENTS = 2;

    protected ?string $error = null;
    protected string $message;

    public function __construct(
        string $content,
        string $character,
        protected Channel $channel,
    ) {
        parent::__construct($content, $character, $channel);
        $arguments = explode(' ', $content);

        if (self::EXPECTED_ARGUMENTS !== count($arguments)) {
            $this->error = sprintf(
                'To link your Commlink user, go to the settings page '
                    . '(%s/settings) and copy the command listed there for '
                    . 'this server. If the server isn\'t listed, follow the '
                    . 'instructions there to add it. You\'ll need to know your '
                    . 'server ID (`%s`) and your user ID (`%s`).',
                config('app.url'),
                $channel->server_id,
                $channel->user,
            );
            return;
        }

        $hash = $arguments[1];

        $chatUsers = ChatUser::where('server_type', $channel->type)
            ->where('server_id', $channel->server_id)
            ->where('remote_user_id', $channel->user)
            ->get();
        foreach ($chatUsers as $user) {
            if ($user->verification !== $hash) {
                // Not the right user.
                continue;
            }
            if ($user->verified) {
                $this->error = 'It looks like you\'re already verified!';
                return;
            }

            $user->verified = true;
            $user->save();
            $this->message = sprintf(
                'Your %s account has been linked with this user. You only need '
                    . 'to do this once for this server, no matter how many '
                    . 'different channels you play in.',
                config('app.name'),
            );
            UserLinked::dispatch($user);
            return;
        }

        $this->error = sprintf(
            'We couldn\'t find a %s registration for this server and your '
                . 'user. Go to the settings page (%s/settings) and copy the '
                . 'command listed there for this server. If the server isn\'t '
                . 'listed, follow the instructions there to add it. You\'ll '
                . 'need to know your server ID (`%s`) and your user ID (`%s`).',
            config('app.name'),
            config('app.url'),
            $channel->server_id,
            $channel->user,
        );
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return $this->message;
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return str_replace('`', '', $this->message);
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment('Verified!', $this->message);
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }
}
