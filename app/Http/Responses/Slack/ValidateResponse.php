<?php

declare(strict_types=1);

namespace App\Http\Responses\Slack;

use App\Events\SlackUserLinked;
use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatUser;
use App\Models\Slack\TextAttachment;
use App\Models\User;

/**
 * Slack response for registering a Commlink user to a Slack user.
 */
class ValidateResponse extends SlackResponse
{
    protected const MIN_NUM_ARGUMENTS = 2;

    /**
     * User linked to the request.
     * @var User
     */
    protected User $user;

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
        int $status = self::HTTP_OK,
        array $headers = [],
        ?Channel $channel = null,
    ) {
        parent::__construct($content, $status, $headers, $channel);
        if (null === $channel) {
            throw new SlackException('Channel is required');
        }

        $args = \explode(' ', $content);
        if (self::MIN_NUM_ARGUMENTS !== \count($args)) {
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
        foreach ($chatUsers as $chatUser) {
            if ($chatUser->verification !== $hash) {
                // Not the right user.
                continue;
            }
            if ($chatUser->verified) {
                throw new SlackException(
                    'It looks like you\'re already verfied!'
                );
            }

            $chatUser->verified = true;
            $chatUser->save();

            SlackUserLinked::dispatch($chatUser);

            // @phpstan-ignore-next-line
            $this->user = $chatUser->user;

            $nextStep = $this->getNextStep();

            $this->addAttachment(new TextAttachment(
                'Verified!',
                \sprintf(
                    'Your Commlink account (%s) has been linked with this '
                        . 'Slack user. You only need to do this once for this '
                        . 'server, no matter how many different channels you '
                        . 'play in.' . \PHP_EOL . \PHP_EOL . '%s',
                    // @phpstan-ignore-next-line
                    $this->user->email,
                    $nextStep
                ),
                TextAttachment::COLOR_SUCCESS,
                \sprintf(
                    'Server: %s User: %s Channel: %s Commlink User: %d',
                    $channel->server_id,
                    $channel->user,
                    $channel->channel_id,
                    optional($this->user)->id,
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

    /**
     * Get instructions for the next step.
     */
    protected function getNextStep(): string
    {
        if (null === $this->channel->id) {
            // Channel is not linked, give instructions for linking a campaign
            // or registering a system.
            $systems = [];
            foreach (config('app.systems') as $code => $name) {
                $systems[] = \sprintf('%s (%s)', $code, $name);
            }
            $next = 'Next, you can `/roll register <system>`, where <system> '
                . 'is one of: ' . \implode(', ', $systems);
            $campaigns = $this->user->campaignsRegistered
                ->merge($this->user->campaignsGmed)
                ->unique();
            if (0 === count($campaigns)) {
                return $next;
            }
            $campaignList = [];
            foreach ($campaigns as $campaign) {
                $campaignList[] = sprintf(
                    '· %d - %s (%s)',
                    $campaign->id,
                    $campaign->name,
                    $campaign->getSystem(),
                );
            }
            return $next . \PHP_EOL . '*Or*, you can type `/roll campaign '
                . '<campaignId>` to register this channel for the campaign '
                . 'with ID <campaignId>. Your campaigns:' . \PHP_EOL
                . implode(\PHP_EOL, $campaignList);
        }

        /** @var array<int, Character> */
        $characters = $this->user->characters($this->channel->system)->get();
        if (0 === count($characters)) {
            return '';
        }
        $charactersList = [];
        foreach ($characters as $character) {
            $charactersList[] = sprintf(
                '· %s - %s',
                $character->id,
                (string)$character
            );
        }
        return 'Next, you can `/roll link <characterId>` to link a character '
            . 'to this channel, where <characterId> is one of: ' . \PHP_EOL
            . implode(\PHP_EOL, $charactersList);
    }
}
