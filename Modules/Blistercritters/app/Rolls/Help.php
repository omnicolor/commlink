<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

use function sprintf;

use const PHP_EOL;

/**
 * @psalm-suppress UnusedClass
 */
class Help extends Roll
{
    /**
     * @var array<int, array<string, string>>
     */
    protected array $data = [];

    public function __construct(
        string $content,
        string $username,
        protected Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $this->data[] = [
            'title' => config('app.name') . ' - Blister Critters RPG',
            'text' => 'I am a bot that lets you roll Blister Critters RPG dice.'
                . PHP_EOL
                . '· `8 6 [text]` - Roll an 8-sided die against a target '
                . 'number of 6, describing the roll is for "text"' . PHP_EOL
                . '· `6 6 adv [text]` - Roll two 6-sided dice with advantage, '
                . 'taking the higher of the two' . PHP_EOL
                . '· `6 6 dis [text]` - Roll two 6-sided dice with '
                . 'disadvantage, taking the lower of the two' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];
        if (null !== $this->character) {
            $this->data[] = [
                'title' => 'Player',
                'text' => 'You\'re playing ' . (string)$this->character
                    . ' in this channel' . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        }
    }

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

    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= sprintf('**%s**', $element['title']) . PHP_EOL
                . $element['text'] . PHP_EOL;
        }
        return $value;
    }

    public function forIrc(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= $element['title'] . PHP_EOL . $element['text'] . PHP_EOL;
        }
        return $value;
    }
}
