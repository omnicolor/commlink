<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Events\ChannelLinked;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

use function array_key_exists;
use function array_keys;
use function count;
use function explode;
use function implode;
use function sprintf;
use function str_replace;

class Register extends Roll
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
        $systems = config('app.systems');

        if (self::EXPECTED_ARGUMENTS !== count($arguments)) {
            $this->error = 'To register a channel, use `register [system]`, '
                . 'where system is a system code: '
                . implode(', ', array_keys($systems));
            return;
        }

        if (null !== $channel->system) {
            $this->error = sprintf(
                'This channel is already registered for "%s"',
                $channel->system
            );
            return;
        }

        $system = $arguments[1];
        if (!array_key_exists($system, $systems)) {
            $this->error = sprintf(
                '"%s" is not a valid system code. Use `register <system>`, '
                    . 'where system is one of: %s',
                $system,
                implode(', ', array_keys($systems))
            );
            return;
        }

        $chatUser = $channel->getChatUser();
        if (null === $chatUser) {
            $this->error = sprintf(
                'You must have already created an account on %s (%s/settings) '
                    . 'and linked it to this server before you can register a '
                    . 'channel to a specific system.',
                config('app.name'),
                config('app.url'),
            );
            return;
        }

        $this->channel->system = $system;
        // @phpstan-ignore-next-line
        $this->channel->registered_by = $chatUser->user->id;
        $this->channel->save();

        $this->message = sprintf(
            '%s has registered this channel for the "%s" system.',
            $this->channel->username,
            $systems[$system],
        );
        ChannelLinked::dispatch($this->channel);
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment('Registered', $this->message);
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
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
            return str_replace('`', '', $this->error);
        }
        return $this->message;
    }
}
