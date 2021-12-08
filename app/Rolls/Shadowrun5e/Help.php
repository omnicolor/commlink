<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

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
            'title' => 'Commlink - Shadowrun 5th Edition',
            'text' => 'Commlink is a Slack/Discord bot that lets you roll '
                . 'Shadowrun 5E dice.' . \PHP_EOL
                . '· `6 [text]` - Roll 6 dice, with optional text (automatics, '
                . 'perception, etc)' . \PHP_EOL
                . '· `12 6 [text]` - Roll 12 dice with a limit of 6' . \PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . \PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];

        if ($this->isGm()) {
            $this->data[] = [
                'title' => 'Gamemaster commands',
                'text' => '· None yet',
                'color' => TextAttachment::COLOR_INFO,
            ];
        } elseif (null !== $this->character) {
            $this->data[] = [
                'title' => 'Player',
                'text' => (string)$this->character,
                'color' => TextAttachment::COLOR_INFO,
            ];
        } else {
            $this->data[] = [
                'title' => 'Player',
                'text' => 'No character linked',
                'color' => TextAttachment::COLOR_INFO,
            ];
        }
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
