<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Events\ChannelLinked;
use App\Models\Channel;
use App\Models\ChatUser;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Response;
use Omnicolor\Slack\Sections\Text;
use Override;

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
        $systems = config('commlink.systems');

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
        if (!$chatUser instanceof ChatUser) {
            $this->error = sprintf(
                'You must have already created an account on %s '
                    . '(%s/settings/chat-users) and linked it to this server '
                    . 'before you can register a channel to a specific system.',
                config('app.name'),
                config('app.url'),
            );
            return;
        }

        $this->channel->system = $system;
        $this->channel->registered_by = $chatUser->user->id;
        $this->channel->save();

        $this->message = sprintf(
            '%s has registered this channel for the "%s" system.',
            $this->channel->username,
            $systems[$system],
        );
        ChannelLinked::dispatch($this->channel);
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        return (new Response())
            ->addBlock(new Header('Registered'))
            ->addBlock(new Text($this->message))
            ->sendToChannel();
    }

    #[Override]
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return $this->message;
    }

    #[Override]
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return str_replace('`', '', $this->error);
        }
        return $this->message;
    }
}
