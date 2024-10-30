<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Events\DiscordMessageReceived;
use App\Events\IrcMessageReceived;
use App\Events\MessageReceived;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Slack\Field;
use App\Models\Slack\FieldsAttachment;

use const PHP_EOL;

/**
 * @psalm-suppress UnusedClass
 */
class Info extends Roll
{
    protected string $campaign_name = 'No campaign';
    protected string $character_name = 'No character';
    protected ?ChatUser $chat_user;
    protected string $commlink_user = 'Not linked';

    public function __construct(
        string $content,
        string $character,
        protected Channel $channel,
        protected ?MessageReceived $event = null
    ) {
        parent::__construct($content, $character, $channel);
        $this->chat_user = $this->channel->getChatUser();
        if (null !== $this->chat_user && null !== $this->chat_user->user) {
            $this->commlink_user = $this->chat_user->user->email;
        }
        if (null !== $channel->campaign) {
            $this->campaign_name = $channel->campaign->name;
        }
        $this->setCharacterName();
    }

    protected function setCharacterName(): void
    {
        if (null === $this->chat_user || null === $this->channel->id) {
            return;
        }
        $chatCharacter = ChatCharacter::where('channel_id', $this->channel->id)
            ->where('chat_user_id', $this->chat_user->id)
            ->first();
        if (null === $chatCharacter) {
            return;
        }
        $character = $chatCharacter->getCharacter();
        if (null === $character) {
            $this->character_name = 'Invalid character';
            return;
        }
        $this->character_name = (string)$character;
    }

    public function forDiscord(): string
    {
        /** @var DiscordMessageReceived */
        $event = $this->event;

        $system = $this->channel->system;
        if (null === $system || '' === $system) {
            $system = 'Unregistered';
        } else {
            $system = config('commlink.systems')[$this->channel->system];
        }

        return '**Debugging info**' . PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . PHP_EOL
            . 'User ID: ' . optional($event->user)->id . PHP_EOL
            . 'Commlink User: ' . $this->commlink_user . PHP_EOL
            /**
             * @psalm-suppress UndefinedMagicPropertyFetch
             */
            . 'Server Name: ' . $event->server->name . PHP_EOL
            . 'Server ID: ' . $event->server->id . PHP_EOL
            // @phpstan-ignore property.notFound
            . 'Channel Name: ' . $event->channel->name . PHP_EOL
            . 'Channel ID: ' . $event->channel->id . PHP_EOL
            . 'System: ' . $system . PHP_EOL
            . 'Character: ' . $this->character_name . PHP_EOL
            . 'Campaign: ' . $this->campaign_name;
    }

    public function forIrc(): string
    {
        /** @var IrcMessageReceived */
        $event = $this->event;

        $system = $this->channel->system;
        if (null === $system || '' === $system) {
            $system = 'unregistered';
        } else {
            $system = config('commlink.systems')[$this->channel->system];
        }

        return 'Debugging info' . PHP_EOL
            . 'User name: ' . $event->user->nick . PHP_EOL
            . 'Commlink User: ' . $this->commlink_user . PHP_EOL
            . 'Server: ' . $event->server . PHP_EOL
            . 'Channel name: ' . $event->channel->getName() . PHP_EOL
            . 'System: ' . $system . PHP_EOL
            . 'Character: ' . $this->character_name . PHP_EOL
            . 'Campaign: ' . $this->campaign_name;
    }

    public function forSlack(): SlackResponse
    {
        $attachment = (new FieldsAttachment('Debugging Info'))
            ->addField(new Field('Team ID', $this->channel->server_id))
            ->addField(new Field('Channel ID', $this->channel->channel_id))
            ->addField(new Field('User ID', $this->channel->user))
            ->addField(new Field('Commlink User', $this->commlink_user))
            ->addField(new Field(
                'System',
                config('commlink.systems')[$this->channel->system] ?? $this->channel->system ?? 'unregistered'
            ))
            ->addField(new Field('Character', $this->character_name))
            ->addField(new Field('Campaign', $this->campaign_name));
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment);
    }
}
