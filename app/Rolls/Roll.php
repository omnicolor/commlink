<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;

abstract class Roll
{
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
     * @param string $character
     */
    abstract public function __construct(string $content, string $character);

    /**
     * Return the roll's output, formatted for Slack.
     * @param Channel $channel
     * @return SlackResponse
     */
    abstract public function forSlack(Channel $channel): SlackResponse;

    /**
     * Return the roll's output, formatted for Discord.
     * @return string
     */
    abstract public function forDiscord(): string;
}
