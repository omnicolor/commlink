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

class Info extends Roll
{
    protected string $campaignName = 'No campaign';
    protected string $characterName = 'No character';
    protected ?ChatUser $chatUser;
    protected string $commlinkUser = 'Not linked';

    public function __construct(
        string $content,
        string $character,
        protected Channel $channel,
        protected MessageReceived $event
    ) {
        parent::__construct($content, $character, $channel);
        $this->chatUser = $this->channel->getChatUser();
        if (null !== $this->chatUser && null !== $this->chatUser->user) {
            $this->commlinkUser = $this->chatUser->user->email;
        }
        if (null !== $channel->campaign) {
            $this->campaignName = $channel->campaign->name;
        }
        $this->setCharacterName();
        $this->event = $event;
    }

    protected function setCharacterName(): void
    {
        if (null === $this->chatUser || null === $this->channel->id) {
            return;
        }
        $chatCharacter = ChatCharacter::where('channel_id', $this->channel->id)
            ->where('chat_user_id', $this->chatUser->id)
            ->first();
        if (null === $chatCharacter) {
            return;
        }
        $character = $chatCharacter->getCharacter();
        if (null === $character) {
            $this->characterName = 'Invalid character';
            return;
        }
        $this->characterName = (string)$character;
    }

    public function forDiscord(): string
    {
        /** @var DiscordMessageReceived */
        $event = $this->event;

        $system = $this->channel->system;
        if (null === $system || '' === $system) {
            $system = 'Unregistered';
        } else {
            $system = config('app.systems')[$this->channel->system];
        }

        return '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($event->user)->id . \PHP_EOL
            . 'Server Name: ' . $event->server->server_name . \PHP_EOL
            . 'Server ID: ' . $event->server->server_id . \PHP_EOL
            // @phpstan-ignore-next-line
            . 'Channel Name: ' . $event->channel->name . \PHP_EOL
            . 'Channel ID: ' . $event->channel->id . \PHP_EOL
            . 'System: ' . $system . \PHP_EOL
            . 'Character: ' . $this->characterName . \PHP_EOL
            . 'Campaign: ' . $this->campaignName;
    }

    public function forIrc(): string
    {
        /** @var IrcMessageReceived */
        $event = $this->event;

        $system = $this->channel->system;
        if (null === $system || '' === $system) {
            $system = 'unregistered';
        } else {
            $system = config('app.systems')[$this->channel->system];
        }

        return 'Debugging info' . \PHP_EOL
            . 'User name: ' . $event->user . \PHP_EOL
            . 'Server: ' . $event->server . \PHP_EOL
            . 'Channel name: ' . $event->channel->getName() . \PHP_EOL
            . 'System: ' . $system . \PHP_EOL
            . 'Character: ' . $this->characterName . \PHP_EOL
            . 'Campaign: ' . $this->campaignName;
    }

    public function forSlack(): SlackResponse
    {
        $attachment = (new FieldsAttachment('Debugging Info'))
            ->addField(new Field('Team ID', $this->channel->server_id))
            ->addField(new Field('Channel ID', $this->channel->channel_id))
            ->addField(new Field('User ID', $this->channel->user ?? ''))
            ->addField(new Field('Commlink User', $this->commlinkUser))
            ->addField(new Field(
                'System',
                config('app.systems')[$this->channel->system] ?? $this->channel->system ?? 'unregistered'
            ))
            ->addField(new Field('Character', $this->characterName))
            ->addField(new Field('Campaign', $this->campaignName));
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment);
    }
}
