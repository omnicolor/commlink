<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatUser;

abstract class Roll
{
    /**
     * Campaign, if one is linked to the channel.
     * @var ?Campaign
     */
    protected ?Campaign $campaign;

    /**
     * Character, if one is linked to the channel.
     * @var ?Character
     */
    protected ?Character $character;

    /**
     * Linked user, if one exists.
     * @var ?ChatUser
     */
    protected ?ChatUser $chatUser;

    /**
     * Original text entered by the user.
     * @var string
     */
    protected string $content;

    /**
     * Optional description the user added for the roll.
     * @var string
     */
    protected string $description = '';

    /**
     * Optional footer of the roll.
     * @var string
     */
    protected string $footer = '';

    /**
     * Text of the roll.
     * @var string
     */
    protected string $text = '';

    /**
     * Title for the roll.
     * @var string
     */
    protected string $title = '';

    /**
     * Construct the roll.
     * @param string $content
     * @param string $username
     * @param Channel $channel
     */
    public function __construct(
        string $content,
        protected string $username,
        protected Channel $channel
    ) {
        $this->campaign = $channel->campaign;
        $this->character = $channel->character();
        $this->chatUser = $channel->getChatUser();
        $this->content = $content;
        if (null !== $this->character) {
            $this->username = (string)$this->character;
        }
    }

    /**
     * Return the roll's output, formatted for Slack.
     * @return SlackResponse
     */
    abstract public function forSlack(): SlackResponse;

    /**
     * Return the roll's output, formatted for Discord.
     * @return string
     */
    abstract public function forDiscord(): string;

    /**
     * Return whether the current user is the GM of the campaign attached to the
     * current channel.
     * @return bool
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
