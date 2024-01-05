<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatUser;
use Discord\Builders\MessageBuilder;

abstract class Roll
{
    /**
     * Campaign, if one is linked to the channel.
     */
    protected ?Campaign $campaign;

    /**
     * Character, if one is linked to the channel.
     */
    protected ?Character $character;

    /**
     * Linked user, if one exists.
     */
    protected ?ChatUser $chatUser;

    /**
     * Original text entered by the user.
     */
    protected string $content;

    /**
     * Optional description the user added for the roll.
     */
    protected string $description = '';

    /**
     * Optional footer of the roll.
     */
    protected string $footer = '';

    /**
     * Text of the roll.
     */
    protected string $text = '';

    /**
     * Title for the roll.
     */
    protected string $title = '';

    public function __construct(
        string $content,
        protected string $username,
        protected Channel $channel,
    ) {
        $this->campaign = $channel->campaign;
        $this->character = $channel->character();
        $this->chatUser = $channel->getChatUser();
        $this->content = $content;
        if (null !== $this->character) {
            $this->username = (string)$this->character;
        }
    }

    abstract public function forDiscord(): string | MessageBuilder;

    public function forIrc(): string
    {
        return 'Not implemented';
    }

    abstract public function forSlack(): SlackResponse;

    /**
     * Handle a callback from a Slack message.
     * @codeCoverageIgnore
     */
    public function handleSlackAction(): void
    {
    }

    /**
     * Return whether the current user is the GM of the campaign attached to the
     * current channel.
     */
    public function isGm(): bool
    {
        if (null === $this->campaign) {
            // You can't be a GM if there's no campaign.
            return false;
        }

        if (null === $this->chatUser || null === $this->chatUser->user) {
            // It doesn't matter if you're the GM if you're not registered, we
            // don't know who you are.
            return false;
        }

        if ($this->campaign->gm === $this->chatUser->user->id) {
            return true;
        }
        return false;
    }
}
