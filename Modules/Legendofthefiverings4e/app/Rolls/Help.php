<?php

declare(strict_types=1);

namespace Modules\Legendofthefiverings4e\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

use function sprintf;

use const PHP_EOL;

/**
 * @psalm-api
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
            'title' => config('app.name') . ' - Legend of the Five Rings 4E',
            'text' => 'I am a bot that lets you roll Legend of the Five Rings '
                . 'dice.' . PHP_EOL
                . '· `6 3 [text]` - Roll 6 dice, keeping 3, with optional text '
                . '(automatics, perception, etc)' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];
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
            $value .= $element['title'] . PHP_EOL
                . $element['text'] . PHP_EOL;
        }
        return $value;
    }
}
