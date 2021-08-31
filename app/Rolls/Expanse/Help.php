<?php

declare(strict_types=1);

namespace App\Rolls\Expanse;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

/**
 * Handle a user asking for help.
 */
class Help extends Roll
{
    /**
     * @var array<int, array<string, string>>
     */
    protected array $data = [];

    /**
     * Constructor.
     * @param string $content
     * @param string $username
     * @param Channel $channel
     */
    public function __construct(
        string $content,
        string $username,
        protected Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $this->data[] = [
            'title' => 'Commlink - The Expanse',
            'text' => 'Commlink is a Slack/Discord bot that lets you roll '
                . 'dice for The Expanse.' . \PHP_EOL
                . '· `4 [text]` - Roll 3d6 dice adding 4 to the result with '
                . 'optional text (automatics, perception, etc)' . \PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];
    }

    /**
     * Return the roll formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(): SlackResponse
    {
        $response = new SlackResponse(
            '',
            SlackResponse::HTTP_OK,
            [],
            $this->channel
        );
        foreach ($this->data as $element) {
            $response->addAttachment(new TextAttachment(
                $element['title'],
                $element['text'],
                $element['color'],
            ));
        }
        return $response;
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= \sprintf('**%s**', $element['title']) . \PHP_EOL
            . $element['text'] . \PHP_EOL;
        }
        return $value;
    }
}
