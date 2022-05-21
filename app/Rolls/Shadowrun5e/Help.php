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
                'text' => '· `init start` - Start a new initiative tracker, '
                    . 'removing any existing rolls' . \PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        } elseif (null !== $this->character) {
            /** @var \App\Models\Shadowrun5e\Character */
            $character = $this->character;

            $this->data[] = [
                'title' => 'Player',
                'text' => 'You\'re playing ' . (string)$this->character
                    . ' in this channel' . \PHP_EOL
                    . \sprintf(
                        '· `composure` - Make a composure roll (%d)' . \PHP_EOL
                        . '· `judge` - Make a judge intentions check (%d)'
                        . \PHP_EOL
                        . '· `lift` - Make a lift/carry roll (%d)' . \PHP_EOL
                        . '· `memory` - Make a memory test (%d)' . \PHP_EOL
                        . '· `soak` - Make a soak test (%d)' . \PHP_EOL
                        . '· `luck` - Make a luck (edge) test (%d)' . \PHP_EOL
                        . '· `init` - Roll your initiative (%dd6+%d)' . \PHP_EOL,
                        $character->composure,
                        $character->judge_intentions,
                        $character->lift_carry,
                        $character->memory,
                        $character->soak,
                        $character->edge,
                        $character->initiative_dice,
                        $character->initiative_score,
                    ),
                'color' => TextAttachment::COLOR_INFO,
            ];
        } else {
            $this->data[] = [
                'title' => 'Player',
                'text' => 'No character linked' . \PHP_EOL
                    . '· `link <characterId>` - Link a character to this '
                    . 'channel' . \PHP_EOL
                    . '· `init 12+3d6` - Roll your initiative' . \PHP_EOL,
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
        $response = new SlackResponse(channel: $this->channel);
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
