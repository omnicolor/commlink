<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Class representing a coin flip.
 */
class Coin extends Roll
{
    /**
     * Constructor.
     * @param string $content
     * @param string $username
     * @param Channel $channel
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);

        $flip = random_int(1, 2);
        $this->title = \sprintf(
            '%s flipped a coin: %s',
            $username,
            1 === $flip ? 'Heads' : 'Tails'
        );
        $this->text = '';
    }

    /**
     * Return the roll formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(): SlackResponse
    {
        $attachment = new TextAttachment(
            $this->title,
            $this->text,
            TextAttachment::COLOR_SUCCESS
        );
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        return \sprintf('**%s**', $this->title) . \PHP_EOL;
    }
}
