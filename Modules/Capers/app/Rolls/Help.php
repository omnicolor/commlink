<?php

declare(strict_types=1);

namespace Modules\Capers\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Response;
use Override;

use function sprintf;

use const PHP_EOL;

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
            'title' => sprintf('%s - Capers', config('app.name')),
            'text' => 'Commlink is a Slack/Discord bot that lets you track '
                . 'virtual card decks for the Capers RPG system.' . PHP_EOL
                . '· `draw [text]` - Draw a card, with optional text '
                . '(automatics, perception, etc)' . PHP_EOL
                . '· `shuffle` - Shuffle your deck' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];

        if ($this->isGm()) {
            $this->data[] = [
                'title' => 'Gamemaster commands',
                'text' => '· `shuffleAll` - Shuffle all decks',
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

    #[Override]
    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= sprintf('**%s**', $element['title']) . PHP_EOL
                . $element['text'] . PHP_EOL;
        }
        return $value;
    }

    #[Override]
    public function forIrc(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= $element['title'] . PHP_EOL
                . $element['text'] . PHP_EOL;
        }
        return $value;
    }

    #[Override]
    public function forSlack(): Response
    {
        $response = new Response();
        foreach ($this->data as $element) {
            // @phpstan-ignore method.deprecated
            $response->addAttachment(new TextAttachment(
                $element['title'],
                $element['text'],
                $element['color'],
            ));
        }
        return $response;
    }
}
