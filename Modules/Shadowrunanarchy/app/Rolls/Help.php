<?php

declare(strict_types=1);

namespace Modules\Shadowrunanarchy\Rolls;

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
            'title' => config('app.name') . ' - Shadowrun Anarchy',
            'text' => 'I am a bot that lets you roll Shadowrun Anarchy dice.'
                . PHP_EOL
                . '· `6 [text]` - Roll 6 dice, with optional text (automatics, '
                . 'perception, etc)' . PHP_EOL
                . '· `7 glitch [text]` - Roll 7 dice plus a glitch die, with '
                . 'optional text (automatics, perception, etc)' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];
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
            $value .= $element['title'] . PHP_EOL
                . $element['text'] . PHP_EOL;
        }
        return $value;
    }
}
