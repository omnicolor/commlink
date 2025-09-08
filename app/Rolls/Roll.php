<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatUser;
use Discord\Builders\MessageBuilder;
use Omnicolor\Slack\Response as SlackResponse;

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
        /**
         * Original text entered by the user.
         */
        protected string $content,
        protected string $username,
        protected Channel $channel,
    ) {
        $this->campaign = $channel->campaign;
        $this->character = $channel->character();
        $this->chatUser = $channel->getChatUser();
        if ($this->character instanceof Character) {
            $this->username = (string)$this->character;
        }
    }

    abstract public function forDiscord(): string | MessageBuilder;

    abstract public function forIrc(): string;

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
        if (!$this->campaign instanceof Campaign) {
            // You can't be a GM if there's no campaign.
            return false;
        }

        if (!$this->chatUser instanceof ChatUser || null === $this->chatUser->user) {
            // It doesn't matter if you're the GM if you're not registered, we
            // don't know who you are.
            return false;
        }

        return $this->campaign->gm === $this->chatUser->user->id;
    }
}
