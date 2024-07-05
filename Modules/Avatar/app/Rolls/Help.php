<?php

declare(strict_types=1);

namespace Modules\Avatar\Rolls;

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
            'title' => 'Commlink - Avatar RPG',
            'text' => 'Commlink is a Slack/Discord ot that lets you roll dice '
                . 'for the Avatar RPG.' . PHP_EOL
                . '· `2d6 [1]` - Roll two dice, optionally adding a modifier '
                . '(+1 in this case)' . PHP_EOL,
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
                'title' => 'Player commands',
                'text' => 'Since you have linked a character to this channel, '
                    . 'you can use commands that will automatically add '
                    . $this->character . '\'s appropriate statistic to the '
                    . 'roll.' . PHP_EOL
                    . '· `plead [+1]` - Roll two dice, adding your '
                    . 'character\'s harmony and optionally a modifier.'
                    . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        } else {
            $this->data[] = [
                'title' => 'Player commands',
                'text' => '· `link <characterId>` - Link your Commlink '
                    . 'character to the channel.' . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        }
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
}
