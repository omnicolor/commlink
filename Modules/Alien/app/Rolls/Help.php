<?php

declare(strict_types=1);

namespace Modules\Alien\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Response;
use Override;

use function config;
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
            'title' => config('app.name') . ' - Alien RPG',
            'text' => 'I am a bot that lets you roll Alien RPG dice. ' . PHP_EOL
                . '· `6 [text]` - Roll 6 dice, counting successes, optionally '
                . 'describing the roll for "text"' . PHP_EOL
                . '· `6 2` - Roll 6 regular dice and 2 stress dice, counting '
                . 'successes and checking stress dice for panic' . PHP_EOL
                . '· `injury` - Roll 2 dice and consult the injury table'
                . PHP_EOL
                . '· `trauma` - Roll on the permanent mental trauma table'
                . PHP_EOL
                . '· `panic 2` - Roll a panic test, adding 2 for your stress '
                . 'level' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];
        if ($this->isGm()) {
            // TODO
        } elseif (null !== $this->character) {
            $this->data[] = [
                'title' => 'Player',
                'text' => 'You\'re playing ' . $this->character
                    . ' in this channel' . PHP_EOL
                    . '· `skill [id]` - Make a skill roll for a skill with any '
                    . 'required stress dice' . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        } else {
            $this->data[] = [
                'title' => 'Player',
                'text' => 'No character linked' . PHP_EOL
                    . '· `link <characterId>` - Link a character to this '
                    . 'channel' . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        }
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
            $value .= $element['title'] . PHP_EOL . $element['text'] . PHP_EOL;
        }
        return $value;
    }
}
