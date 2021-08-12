<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Events\SlackUserLinked;
use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\Slack\TextAttachment;
use App\Models\User;

/**
 * Slack response for registering a Commlink user to a Slack user.
 */
class ValidateUserResponse extends SlackResponse
{
    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     * @param ?Channel $channel
     * @throws SlackException
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = [],
        ?Channel $channel = null,
    ) {
        parent::__construct('', $status, $headers, $channel);
        if (null === $channel) {
            throw new SlackException('Channel is required');
        }

        $args = \explode(' ', $content);
        if (2 !== \count($args)) {
            throw new SlackException(\sprintf(
                'To link your Commlink user, go to the '
                    . '<%s/settings|settings page> and copy the command listed '
                    . 'there for this server. If the server isn\'t listed, '
                    . 'follow the instructions there to add it. You\'ll need '
                    . 'to know your server ID (`%s`) and your user ID (`%s`).',
                config('app.url'),
                $channel->server_id,
                $channel->user
            ));
        }

        $hash = $args[1];

        $chatUsers = ChatUser::slack()
            ->where('server_id', $channel->server_id)
            ->where('remote_user_id', $channel->user)
            ->get();
        foreach ($chatUsers as $user) {
            if ($user->verification !== $hash) {
                // Not the right user.
                continue;
            }
            if ($user->verified) {
                throw new SlackException(
                    'It looks like you\'re already verfied!'
                );
            }

            $user->verified = true;
            $user->save();

            SlackUserLinked::dispatch($user);

            $nextStep = 'Next, you can `/roll link <characterID>` to link a '
                . 'character to this channel.';
            if (null === $channel->id) {
                $systems = [];
                foreach (config('app.systems') as $code => $name) {
                    $systems[] = \sprintf('· %s (%s)', $name, $code);
                }
                $nextStep = 'Next, you can `/roll register <systemID>`, where '
                    . '<systemId> is the short code from:' . \PHP_EOL
                    . \implode(\PHP_EOL, $systems);
            }

            $this->addAttachment(new TextAttachment(
                'Verified!',
                \sprintf(
                    'Your Commlink account (%s) has been linked with this '
                        . 'Slack user. You only need to do this once for this '
                        . 'server, no matter how many different channels you '
                        . 'play in.' . \PHP_EOL . \PHP_EOL . '%s',
                    // @phpstan-ignore-next-line
                    $user->user->email,
                    $nextStep
                ),
                TextAttachment::COLOR_SUCCESS,
                \sprintf(
                    'Server: %s User: %s Channel: %s',
                    $channel->server_id,
                    $channel->user,
                    $channel->channel_id
                )
            ));
            return;
        }
        throw new SlackException(\sprintf(
            'We couldn\'t find a Commlink registration for this Slack team and '
                . 'your user. Go to the <%s/settings|settings page> and copy '
                . 'the command listed there for this server. If the server '
                . 'isn\'t listed, follow the instructions there to add it. '
                . 'You\'ll need to know your server ID (`%s`) and your user ID '
                . '(`%s`).',
            config('app.url'),
            $channel->server_id,
            $channel->user
        ));
    }
}
